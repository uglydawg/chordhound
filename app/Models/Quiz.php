<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'type',
        'passing_score',
        'time_limit',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)
            ->orderBy('order_index');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function userAttempts($userId = null): HasMany
    {
        $userId = $userId ?? auth()->id();
        
        return $this->attempts()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');
    }

    public function getLastAttempt($userId = null): ?QuizAttempt
    {
        return $this->userAttempts($userId)->first();
    }

    public function hasPassedBy($userId = null): bool
    {
        return $this->userAttempts($userId)
            ->where('passed', true)
            ->exists();
    }

    public function getBestScore($userId = null): ?int
    {
        return $this->userAttempts($userId)
            ->max('percentage');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    public function getTotalPoints(): int
    {
        return $this->questions()->sum('points');
    }
}
