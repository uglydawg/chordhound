<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    /** @use HasFactory<\Database\Factories\AchievementFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'type',
        'criteria',
        'points',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'points' => 'integer',
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function isUnlockedBy($userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }

        return $this->userAchievements()
            ->where('user_id', $userId)
            ->exists();
    }

    public function checkCriteria(User $user): bool
    {
        // Check if the achievement criteria are met
        switch ($this->type) {
            case 'lesson':
                return $this->checkLessonCriteria($user);
            case 'quiz':
                return $this->checkQuizCriteria($user);
            case 'practice':
                return $this->checkPracticeCriteria($user);
            case 'streak':
                return $this->checkStreakCriteria($user);
            case 'milestone':
                return $this->checkMilestoneCriteria($user);
            default:
                return false;
        }
    }

    protected function checkLessonCriteria(User $user): bool
    {
        $criteria = $this->criteria;
        
        if (isset($criteria['lessons_completed'])) {
            $completedCount = $user->lessonProgress()
                ->where('status', 'completed')
                ->count();
            
            return $completedCount >= $criteria['lessons_completed'];
        }

        if (isset($criteria['module_completed'])) {
            $module = LessonModule::find($criteria['module_completed']);
            if (!$module) {
                return false;
            }

            $totalLessons = $module->lessons()->count();
            $completedLessons = $user->lessonProgress()
                ->whereIn('lesson_id', $module->lessons()->pluck('id'))
                ->where('status', 'completed')
                ->count();

            return $totalLessons > 0 && $totalLessons === $completedLessons;
        }

        return false;
    }

    protected function checkQuizCriteria(User $user): bool
    {
        $criteria = $this->criteria;
        
        if (isset($criteria['quizzes_passed'])) {
            $passedCount = $user->quizAttempts()
                ->where('passed', true)
                ->distinct('quiz_id')
                ->count('quiz_id');
            
            return $passedCount >= $criteria['quizzes_passed'];
        }

        if (isset($criteria['perfect_scores'])) {
            $perfectCount = $user->quizAttempts()
                ->where('percentage', 100)
                ->count();
            
            return $perfectCount >= $criteria['perfect_scores'];
        }

        return false;
    }

    protected function checkPracticeCriteria(User $user): bool
    {
        $criteria = $this->criteria;
        
        if (isset($criteria['practice_minutes'])) {
            $totalMinutes = $user->lessonProgress()
                ->sum('time_spent') / 60;
            
            return $totalMinutes >= $criteria['practice_minutes'];
        }

        return false;
    }

    protected function checkStreakCriteria(User $user): bool
    {
        // This would require additional tracking of daily activity
        // For now, return false
        return false;
    }

    protected function checkMilestoneCriteria(User $user): bool
    {
        $criteria = $this->criteria;
        
        if (isset($criteria['total_points'])) {
            $totalPoints = $user->achievements()
                ->sum('points');
            
            return $totalPoints >= $criteria['total_points'];
        }

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
