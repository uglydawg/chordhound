<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonModule;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProgressTrackingService
{
    public function trackLessonView(Lesson $lesson, User $user): void
    {
        $progress = $lesson->userProgress($user->id);

        if (!$progress) {
            LessonProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } elseif ($progress->status === 'not_started') {
            $progress->markAsStarted();
        } else {
            // Update time spent
            $progress->updateTimeSpent();
        }
    }

    public function updateLessonProgress(Lesson $lesson, User $user, array $progressData): LessonProgress
    {
        $progress = $lesson->userProgress($user->id) ?? LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $progress->update([
            'progress_data' => array_merge($progress->progress_data ?? [], $progressData),
        ]);

        $progress->updateTimeSpent();

        return $progress;
    }

    public function getModuleProgress(LessonModule $module, User $user): array
    {
        $lessons = $module->lessons()->active()->get();
        
        $progress = $lessons->map(function ($lesson) use ($user) {
            $lessonProgress = $lesson->userProgress($user->id);
            
            return [
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'status' => $lessonProgress ? $lessonProgress->status : 'not_started',
                'score' => $lessonProgress ? $lessonProgress->score : null,
                'time_spent' => $lessonProgress ? $lessonProgress->time_spent : 0,
                'completed_at' => $lessonProgress ? $lessonProgress->completed_at : null,
            ];
        });

        $completedCount = $progress->where('status', 'completed')->count();
        $totalCount = $progress->count();
        $totalTime = $progress->sum('time_spent');
        $averageScore = $progress->whereNotNull('score')->avg('score') ?? 0;

        return [
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
            ],
            'lessons' => $progress,
            'summary' => [
                'completed' => $completedCount,
                'total' => $totalCount,
                'percentage' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0,
                'total_time_minutes' => round($totalTime / 60),
                'average_score' => round($averageScore),
            ],
        ];
    }

    public function getUserProgressHistory(User $user, int $days = 30): Collection
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        
        $lessonProgress = LessonProgress::where('user_id', $user->id)
            ->where('completed_at', '>=', $startDate)
            ->orderBy('completed_at')
            ->get()
            ->groupBy(function ($progress) {
                return $progress->completed_at->format('Y-m-d');
            })
            ->map(function ($dayProgress) {
                return [
                    'lessons_completed' => $dayProgress->count(),
                    'average_score' => $dayProgress->whereNotNull('score')->avg('score'),
                    'time_spent_minutes' => round($dayProgress->sum('time_spent') / 60),
                ];
            });

        $quizProgress = QuizAttempt::where('user_id', $user->id)
            ->where('completed_at', '>=', $startDate)
            ->orderBy('completed_at')
            ->get()
            ->groupBy(function ($attempt) {
                return $attempt->completed_at->format('Y-m-d');
            })
            ->map(function ($dayAttempts) {
                return [
                    'quizzes_completed' => $dayAttempts->count(),
                    'average_score' => $dayAttempts->avg('percentage'),
                    'quizzes_passed' => $dayAttempts->where('passed', true)->count(),
                ];
            });

        // Merge lesson and quiz progress
        $dates = collect($lessonProgress->keys())->merge($quizProgress->keys())->unique()->sort();
        
        return $dates->mapWithKeys(function ($date) use ($lessonProgress, $quizProgress) {
            return [$date => [
                'date' => $date,
                'lessons' => $lessonProgress->get($date, [
                    'lessons_completed' => 0,
                    'average_score' => null,
                    'time_spent_minutes' => 0,
                ]),
                'quizzes' => $quizProgress->get($date, [
                    'quizzes_completed' => 0,
                    'average_score' => null,
                    'quizzes_passed' => 0,
                ]),
            ]];
        });
    }

    public function getWeeklyReport(User $user): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $lessonsCompleted = LessonProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->count();

        $quizzesCompleted = QuizAttempt::where('user_id', $user->id)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->count();

        $timeSpent = LessonProgress::where('user_id', $user->id)
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->sum('time_spent');

        $previousWeekLessons = LessonProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [
                $startOfWeek->copy()->subWeek(),
                $endOfWeek->copy()->subWeek()
            ])
            ->count();

        return [
            'period' => [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
            ],
            'stats' => [
                'lessons_completed' => $lessonsCompleted,
                'quizzes_completed' => $quizzesCompleted,
                'time_spent_minutes' => round($timeSpent / 60),
                'daily_average_minutes' => round($timeSpent / 60 / 7),
            ],
            'comparison' => [
                'lessons_vs_last_week' => $lessonsCompleted - $previousWeekLessons,
                'trend' => $lessonsCompleted >= $previousWeekLessons ? 'up' : 'down',
            ],
        ];
    }

    public function getSkillProgress(User $user): array
    {
        $modules = LessonModule::active()->get();
        
        return $modules->map(function ($module) use ($user) {
            $moduleProgress = app(LearningPathService::class)->calculateModuleProgress($module, $user);
            
            $quizScores = Quiz::whereHas('lesson', function ($query) use ($module) {
                    $query->where('module_id', $module->id);
                })
                ->with(['attempts' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('passed', true);
                }])
                ->get()
                ->pluck('attempts')
                ->flatten()
                ->avg('percentage') ?? 0;

            return [
                'skill' => $module->name,
                'icon' => $module->icon,
                'progress_percentage' => $moduleProgress['percentage'],
                'average_quiz_score' => round($quizScores),
                'lessons_completed' => $moduleProgress['completed'],
                'total_lessons' => $moduleProgress['total'],
            ];
        })->toArray();
    }
}