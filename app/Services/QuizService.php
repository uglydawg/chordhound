<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Support\Collection;

class QuizService
{
    public function startQuiz(Quiz $quiz, User $user): QuizAttempt
    {
        // Check if user has an incomplete attempt
        $existingAttempt = $quiz->userAttempts($user->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingAttempt) {
            return $existingAttempt;
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        return $attempt;
    }

    public function submitAnswer(
        QuizAttempt $attempt,
        QuizQuestion $question,
        $userAnswer,
        ?int $timeTaken = null
    ): array {
        // Check if answer already exists
        $existingAnswer = $attempt->answers()
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            return [
                'success' => false,
                'message' => 'Answer already submitted for this question',
            ];
        }

        // Record the answer
        $answer = $attempt->recordAnswer($question, $userAnswer);

        if ($timeTaken) {
            $answer->update(['time_taken' => $timeTaken]);
        }

        return [
            'success' => true,
            'is_correct' => $answer->is_correct,
            'points_earned' => $answer->points_earned,
            'explanation' => $question->explanation,
        ];
    }

    public function completeQuiz(QuizAttempt $attempt): array
    {
        if ($attempt->completed_at) {
            return [
                'success' => false,
                'message' => 'Quiz already completed',
            ];
        }

        // Complete the attempt
        $attempt->completeAttempt();

        // Check for achievements
        app(AchievementService::class)->checkUserAchievements($attempt->user);

        return [
            'success' => true,
            'score' => $attempt->score,
            'total_points' => $attempt->total_points,
            'percentage' => $attempt->percentage,
            'passed' => $attempt->passed,
            'time_taken' => $attempt->time_taken,
        ];
    }

    public function getQuizQuestions(Quiz $quiz): Collection
    {
        return $quiz->questions()
            ->ordered()
            ->get()
            ->map(function ($question) use ($quiz) {
                $data = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $quiz->type,
                    'points' => $question->points,
                ];

                // Only include options for multiple choice
                if ($quiz->type === 'multiple_choice' && $question->options) {
                    $data['options'] = $question->options;
                }

                return $data;
            });
    }

    public function getQuizResults(QuizAttempt $attempt): array
    {
        $questions = $attempt->quiz->questions()
            ->ordered()
            ->with(['answers' => function ($query) use ($attempt) {
                $query->where('attempt_id', $attempt->id);
            }])
            ->get();

        $results = $questions->map(function ($question) {
            $answer = $question->answers->first();
            
            return [
                'question_id' => $question->id,
                'question' => $question->question,
                'user_answer' => $answer ? $answer->user_answer : null,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $answer ? $answer->is_correct : false,
                'points_earned' => $answer ? $answer->points_earned : 0,
                'points_possible' => $question->points,
                'explanation' => $question->explanation,
            ];
        });

        return [
            'quiz' => [
                'id' => $attempt->quiz->id,
                'title' => $attempt->quiz->title,
                'type' => $attempt->quiz->type,
                'passing_score' => $attempt->quiz->passing_score,
            ],
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'total_points' => $attempt->total_points,
                'percentage' => $attempt->percentage,
                'passed' => $attempt->passed,
                'time_taken' => $attempt->time_taken,
                'completed_at' => $attempt->completed_at,
            ],
            'questions' => $results,
        ];
    }

    public function getUserQuizHistory(User $user, ?int $lessonId = null): Collection
    {
        $query = QuizAttempt::where('user_id', $user->id)
            ->with('quiz.lesson')
            ->completed()
            ->orderBy('completed_at', 'desc');

        if ($lessonId) {
            $query->whereHas('quiz', function ($q) use ($lessonId) {
                $q->where('lesson_id', $lessonId);
            });
        }

        return $query->get()->map(function ($attempt) {
            return [
                'attempt_id' => $attempt->id,
                'quiz_id' => $attempt->quiz->id,
                'quiz_title' => $attempt->quiz->title,
                'lesson_title' => $attempt->quiz->lesson->title,
                'score' => $attempt->score,
                'percentage' => $attempt->percentage,
                'passed' => $attempt->passed,
                'time_taken' => $attempt->time_taken,
                'completed_at' => $attempt->completed_at,
            ];
        });
    }

    public function getQuizStatistics(Quiz $quiz): array
    {
        $attempts = $quiz->attempts()->completed()->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'pass_rate' => 0,
                'average_time' => 0,
                'best_score' => 0,
            ];
        }

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => round($attempts->avg('percentage')),
            'pass_rate' => round(($attempts->where('passed', true)->count() / $attempts->count()) * 100),
            'average_time' => round($attempts->avg('time_taken') / 60), // in minutes
            'best_score' => $attempts->max('percentage'),
        ];
    }

    public function generateChordQuiz(array $chordTypes, int $questionCount = 10): array
    {
        $questions = [];
        
        for ($i = 0; $i < $questionCount; $i++) {
            $chordType = $chordTypes[array_rand($chordTypes)];
            $root = ['C', 'D', 'E', 'F', 'G', 'A', 'B'][array_rand(['C', 'D', 'E', 'F', 'G', 'A', 'B'])];
            
            $chord = $this->generateChord($root, $chordType);
            
            $questions[] = [
                'type' => 'chord_identification',
                'question' => "Identify the notes in this chord",
                'chord_display' => $chord['display'],
                'correct_answer' => $chord['notes'],
                'options' => $this->generateChordOptions($chord['notes']),
            ];
        }

        return $questions;
    }

    protected function generateChord(string $root, string $type): array
    {
        // This would integrate with ChordService
        // For now, return a simple example
        $chords = [
            'major' => [
                'C' => ['C', 'E', 'G'],
                'D' => ['D', 'F#', 'A'],
                'E' => ['E', 'G#', 'B'],
                'F' => ['F', 'A', 'C'],
                'G' => ['G', 'B', 'D'],
                'A' => ['A', 'C#', 'E'],
                'B' => ['B', 'D#', 'F#'],
            ],
            'minor' => [
                'C' => ['C', 'Eb', 'G'],
                'D' => ['D', 'F', 'A'],
                'E' => ['E', 'G', 'B'],
                'F' => ['F', 'Ab', 'C'],
                'G' => ['G', 'Bb', 'D'],
                'A' => ['A', 'C', 'E'],
                'B' => ['B', 'D', 'F#'],
            ],
        ];

        $notes = $chords[$type][$root] ?? ['C', 'E', 'G'];
        
        return [
            'display' => $root . ($type === 'minor' ? 'm' : ''),
            'notes' => $notes,
        ];
    }

    protected function generateChordOptions(array $correctNotes): array
    {
        $allNotes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $options = [$correctNotes];
        
        // Generate 3 more incorrect options
        for ($i = 0; $i < 3; $i++) {
            $wrongOption = [];
            for ($j = 0; $j < count($correctNotes); $j++) {
                do {
                    $note = $allNotes[array_rand($allNotes)];
                } while (in_array($note, $wrongOption));
                $wrongOption[] = $note;
            }
            $options[] = $wrongOption;
        }

        shuffle($options);
        return $options;
    }
}