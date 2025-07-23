<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonModule;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class LearningPathService
{
    public function getUserLearningPath(User $user): Collection
    {
        $modules = LessonModule::active()
            ->ordered()
            ->with(['lessons' => function ($query) {
                $query->active()->ordered();
            }])
            ->get();

        return $modules->map(function ($module) use ($user) {
            $lessons = $module->lessons->map(function ($lesson) use ($user) {
                $progress = $lesson->userProgress($user->id);
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'slug' => $lesson->slug,
                    'description' => $lesson->description,
                    'difficulty_level' => $lesson->difficulty_level,
                    'estimated_time' => $lesson->estimated_time,
                    'is_premium' => $lesson->is_premium,
                    'status' => $progress ? $progress->status : 'not_started',
                    'score' => $progress ? $progress->score : null,
                    'completed_at' => $progress ? $progress->completed_at : null,
                    'is_locked' => $this->isLessonLocked($lesson, $user),
                ];
            });

            return [
                'id' => $module->id,
                'name' => $module->name,
                'slug' => $module->slug,
                'description' => $module->description,
                'icon' => $module->icon,
                'lessons' => $lessons,
                'progress' => $this->calculateModuleProgress($module, $user),
            ];
        });
    }

    public function getNextRecommendedLesson(User $user): ?Lesson
    {
        // Find the first incomplete lesson
        $inProgressLesson = Lesson::active()
            ->whereHas('progress', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'in_progress');
            })
            ->ordered()
            ->first();

        if ($inProgressLesson) {
            return $inProgressLesson;
        }

        // Find the next lesson that hasn't been started
        $nextLesson = Lesson::active()
            ->whereDoesntHave('progress', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->ordered()
            ->first();

        if ($nextLesson && !$this->isLessonLocked($nextLesson, $user)) {
            return $nextLesson;
        }

        // Find a lesson to review (completed but with low score)
        $reviewLesson = Lesson::active()
            ->whereHas('progress', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->where('score', '<', 80);
            })
            ->ordered()
            ->first();

        return $reviewLesson;
    }

    public function isLessonLocked(Lesson $lesson, User $user): bool
    {
        // Premium lessons are locked for non-subscribers
        if ($lesson->is_premium && !$user->subscribed()) {
            return true;
        }

        // Check if previous lessons in the module are completed
        $previousLesson = $lesson->getPreviousLesson();
        
        if ($previousLesson) {
            $previousProgress = $previousLesson->userProgress($user->id);
            
            if (!$previousProgress || $previousProgress->status !== 'completed') {
                return true;
            }
        }

        return false;
    }

    public function calculateModuleProgress(LessonModule $module, User $user): array
    {
        $totalLessons = $module->lessons()->active()->count();
        
        if ($totalLessons === 0) {
            return [
                'completed' => 0,
                'total' => 0,
                'percentage' => 0,
            ];
        }

        $completedLessons = $module->lessons()
            ->active()
            ->whereHas('progress', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'completed');
            })
            ->count();

        return [
            'completed' => $completedLessons,
            'total' => $totalLessons,
            'percentage' => round(($completedLessons / $totalLessons) * 100),
        ];
    }

    public function startLesson(Lesson $lesson, User $user): LessonProgress
    {
        $progress = $lesson->userProgress($user->id);

        if (!$progress) {
            $progress = LessonProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } elseif ($progress->status === 'not_started') {
            $progress->markAsStarted();
        }

        return $progress;
    }

    public function completeLesson(Lesson $lesson, User $user, int $score = null): LessonProgress
    {
        $progress = $this->startLesson($lesson, $user);
        $progress->markAsCompleted($score);

        // Check for achievement unlocks
        $this->checkAchievements($user);

        return $progress;
    }

    protected function checkAchievements(User $user): void
    {
        // This will be handled by the AchievementService
        app(AchievementService::class)->checkUserAchievements($user);
    }

    public function getUserDashboardStats(User $user): array
    {
        $totalLessons = Lesson::active()->count();
        $completedLessons = $user->getCompletedLessonsCount();
        $totalTime = $user->lessonProgress()->sum('time_spent');
        $averageScore = $user->lessonProgress()
            ->where('status', 'completed')
            ->whereNotNull('score')
            ->avg('score') ?? 0;

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $user->getOverallProgress(),
            'total_time_minutes' => round($totalTime / 60),
            'average_score' => round($averageScore),
            'achievement_points' => $user->getTotalAchievementPoints(),
            'learning_streak' => $user->getLearningStreak(),
        ];
    }
}