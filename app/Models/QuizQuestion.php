<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\QuizQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'order_index',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'integer',
        'order_index' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }

    public function checkAnswer($userAnswer): bool
    {
        // Handle different types of questions
        switch ($this->quiz->type) {
            case 'multiple_choice':
                return $this->checkMultipleChoice($userAnswer);
            case 'chord_identification':
                return $this->checkChordIdentification($userAnswer);
            case 'ear_training':
                return $this->checkEarTraining($userAnswer);
            case 'drag_drop':
                return $this->checkDragDrop($userAnswer);
            default:
                return false;
        }
    }

    protected function checkMultipleChoice($userAnswer): bool
    {
        if (is_array($this->correct_answer)) {
            // Multiple correct answers
            return in_array($userAnswer, $this->correct_answer);
        }
        
        return $userAnswer === $this->correct_answer;
    }

    protected function checkChordIdentification($userAnswer): bool
    {
        // Compare chord arrays
        if (!is_array($userAnswer) || !is_array($this->correct_answer)) {
            return false;
        }

        sort($userAnswer);
        $correctAnswer = $this->correct_answer;
        sort($correctAnswer);

        return $userAnswer === $correctAnswer;
    }

    protected function checkEarTraining($userAnswer): bool
    {
        // Similar to chord identification
        return $this->checkChordIdentification($userAnswer);
    }

    protected function checkDragDrop($userAnswer): bool
    {
        // Check if the order matches
        return $userAnswer === $this->correct_answer;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}
