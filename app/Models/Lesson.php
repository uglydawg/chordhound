<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'description',
        'content',
        'difficulty_level',
        'estimated_time',
        'order_index',
        'is_premium',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'difficulty_level' => 'integer',
        'estimated_time' => 'integer',
        'order_index' => 'integer',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(LessonModule::class, 'module_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)
            ->orderBy('order_index');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function userProgress($userId = null): ?LessonProgress
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return null;
        }

        return $this->progress()
            ->where('user_id', $userId)
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    public function scopeByDifficulty($query, int $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function isCompletedBy($userId = null): bool
    {
        $progress = $this->userProgress($userId);
        return $progress && $progress->status === 'completed';
    }

    public function isInProgressBy($userId = null): bool
    {
        $progress = $this->userProgress($userId);
        return $progress && $progress->status === 'in_progress';
    }

    public function getNextLesson(): ?self
    {
        return self::where('module_id', $this->module_id)
            ->where('order_index', '>', $this->order_index)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->first();
    }

    public function getPreviousLesson(): ?self
    {
        return self::where('module_id', $this->module_id)
            ->where('order_index', '<', $this->order_index)
            ->where('is_active', true)
            ->orderBy('order_index', 'desc')
            ->first();
    }
}
