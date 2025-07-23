<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Collection;

class AchievementService
{
    public function checkUserAchievements(User $user): Collection
    {
        $newAchievements = collect();
        
        $achievements = Achievement::active()
            ->whereDoesntHave('userAchievements', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        foreach ($achievements as $achievement) {
            if ($achievement->checkCriteria($user)) {
                $user->awardAchievement($achievement);
                $newAchievements->push($achievement);
            }
        }

        return $newAchievements;
    }

    public function getUserAchievements(User $user): Collection
    {
        return Achievement::active()
            ->ordered()
            ->get()
            ->map(function ($achievement) use ($user) {
                $userAchievement = $user->userAchievements()
                    ->where('achievement_id', $achievement->id)
                    ->first();

                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'type' => $achievement->type,
                    'points' => $achievement->points,
                    'is_unlocked' => $userAchievement !== null,
                    'unlocked_at' => $userAchievement ? $userAchievement->unlocked_at : null,
                    'progress' => $this->getAchievementProgress($achievement, $user),
                ];
            });
    }

    public function getAchievementProgress(Achievement $achievement, User $user): array
    {
        $criteria = $achievement->criteria;
        $current = 0;
        $target = 0;
        $percentage = 0;

        switch ($achievement->type) {
            case 'lesson':
                if (isset($criteria['lessons_completed'])) {
                    $target = $criteria['lessons_completed'];
                    $current = $user->getCompletedLessonsCount();
                } elseif (isset($criteria['module_completed'])) {
                    $target = 1;
                    $current = $achievement->checkCriteria($user) ? 1 : 0;
                }
                break;

            case 'quiz':
                if (isset($criteria['quizzes_passed'])) {
                    $target = $criteria['quizzes_passed'];
                    $current = $user->quizAttempts()
                        ->where('passed', true)
                        ->distinct('quiz_id')
                        ->count('quiz_id');
                } elseif (isset($criteria['perfect_scores'])) {
                    $target = $criteria['perfect_scores'];
                    $current = $user->quizAttempts()
                        ->where('percentage', 100)
                        ->count();
                }
                break;

            case 'practice':
                if (isset($criteria['practice_minutes'])) {
                    $target = $criteria['practice_minutes'];
                    $current = round($user->lessonProgress()->sum('time_spent') / 60);
                }
                break;

            case 'milestone':
                if (isset($criteria['total_points'])) {
                    $target = $criteria['total_points'];
                    $current = $user->getTotalAchievementPoints();
                }
                break;
        }

        if ($target > 0) {
            $percentage = min(100, round(($current / $target) * 100));
        }

        return [
            'current' => $current,
            'target' => $target,
            'percentage' => $percentage,
        ];
    }

    public function getAchievementsByType(string $type): Collection
    {
        return Achievement::active()
            ->byType($type)
            ->ordered()
            ->get();
    }

    public function getRecentAchievements(User $user, int $limit = 5): Collection
    {
        return $user->userAchievements()
            ->with('achievement')
            ->orderBy('unlocked_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($userAchievement) {
                return [
                    'id' => $userAchievement->achievement->id,
                    'name' => $userAchievement->achievement->name,
                    'description' => $userAchievement->achievement->description,
                    'icon' => $userAchievement->achievement->icon,
                    'points' => $userAchievement->achievement->points,
                    'unlocked_at' => $userAchievement->unlocked_at,
                ];
            });
    }

    public function getAchievementLeaderboard(int $limit = 10): Collection
    {
        return User::withCount('userAchievements')
            ->with('userAchievements.achievement')
            ->having('user_achievements_count', '>', 0)
            ->orderBy('user_achievements_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'display_name' => $user->display_name,
                    'achievement_count' => $user->user_achievements_count,
                    'total_points' => $user->getTotalAchievementPoints(),
                ];
            });
    }

    public function createDefaultAchievements(): void
    {
        $achievements = [
            // Lesson achievements
            [
                'name' => 'First Steps',
                'slug' => 'first-steps',
                'description' => 'Complete your first lesson',
                'icon' => 'puppy-paw',
                'type' => 'lesson',
                'criteria' => ['lessons_completed' => 1],
                'points' => 10,
            ],
            [
                'name' => 'Chord Puppy',
                'slug' => 'chord-puppy',
                'description' => 'Complete 5 lessons',
                'icon' => 'puppy-badge',
                'type' => 'lesson',
                'criteria' => ['lessons_completed' => 5],
                'points' => 25,
            ],
            [
                'name' => 'Harmony Hound',
                'slug' => 'harmony-hound',
                'description' => 'Complete 20 lessons',
                'icon' => 'hound-badge',
                'type' => 'lesson',
                'criteria' => ['lessons_completed' => 20],
                'points' => 100,
            ],
            
            // Quiz achievements
            [
                'name' => 'Quiz Whiz',
                'slug' => 'quiz-whiz',
                'description' => 'Pass your first quiz',
                'icon' => 'quiz-bone',
                'type' => 'quiz',
                'criteria' => ['quizzes_passed' => 1],
                'points' => 15,
            ],
            [
                'name' => 'Perfect Pup',
                'slug' => 'perfect-pup',
                'description' => 'Get a perfect score on any quiz',
                'icon' => 'gold-bone',
                'type' => 'quiz',
                'criteria' => ['perfect_scores' => 1],
                'points' => 50,
            ],
            
            // Practice achievements
            [
                'name' => 'Practice Pooch',
                'slug' => 'practice-pooch',
                'description' => 'Practice for 60 minutes total',
                'icon' => 'clock-dog',
                'type' => 'practice',
                'criteria' => ['practice_minutes' => 60],
                'points' => 30,
            ],
            [
                'name' => 'Dedicated Doggo',
                'slug' => 'dedicated-doggo',
                'description' => 'Practice for 300 minutes total',
                'icon' => 'dedicated-badge',
                'type' => 'practice',
                'criteria' => ['practice_minutes' => 300],
                'points' => 75,
            ],
            
            // Milestone achievements
            [
                'name' => 'Rising Star',
                'slug' => 'rising-star',
                'description' => 'Earn 100 achievement points',
                'icon' => 'star-collar',
                'type' => 'milestone',
                'criteria' => ['total_points' => 100],
                'points' => 25,
            ],
            [
                'name' => 'Top Dog',
                'slug' => 'top-dog',
                'description' => 'Earn 500 achievement points',
                'icon' => 'crown-dog',
                'type' => 'milestone',
                'criteria' => ['total_points' => 500],
                'points' => 100,
            ],
        ];

        foreach ($achievements as $index => $achievementData) {
            $achievementData['order_index'] = $index;
            Achievement::updateOrCreate(
                ['slug' => $achievementData['slug']],
                $achievementData
            );
        }
    }
}