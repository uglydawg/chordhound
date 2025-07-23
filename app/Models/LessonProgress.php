<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'started_at',
        'completed_at',
        'score',
        'time_spent',
        'progress_data',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'time_spent' => 'integer',
        'progress_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(int $score = null): void
    {
        $data = [
            'status' => 'completed',
            'completed_at' => now(),
        ];

        if ($score !== null) {
            $data['score'] = $score;
        }

        if ($this->started_at) {
            $data['time_spent'] = $this->started_at->diffInSeconds(now());
        }

        $this->update($data);
    }

    public function updateTimeSpent(): void
    {
        if ($this->started_at && !$this->completed_at) {
            $this->update([
                'time_spent' => $this->started_at->diffInSeconds(now()),
            ]);
        }
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
