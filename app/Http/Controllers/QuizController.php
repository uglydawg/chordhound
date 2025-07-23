<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private QuizService $quizService
    ) {
        $this->middleware('auth');
    }

    public function show(Quiz $quiz): View
    {
        $user = auth()->user();
        
        return view('quizzes.show', [
            'quiz' => $quiz,
            'questions' => $this->quizService->getQuizQuestions($quiz),
            'lastAttempt' => $quiz->getLastAttempt($user->id),
            'bestScore' => $quiz->getBestScore($user->id),
            'statistics' => $this->quizService->getQuizStatistics($quiz),
        ]);
    }

    public function start(Quiz $quiz)
    {
        $user = auth()->user();
        $attempt = $this->quizService->startQuiz($quiz, $user);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'questions' => $this->quizService->getQuizQuestions($quiz),
            'time_limit' => $quiz->time_limit,
        ]);
    }

    public function submitAnswer(QuizAttempt $attempt, Request $request)
    {
        $user = auth()->user();
        
        // Verify the attempt belongs to the user
        if ($attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'answer' => 'required',
            'time_taken' => 'nullable|integer|min:0',
        ]);

        $question = QuizQuestion::findOrFail($validated['question_id']);
        
        // Verify the question belongs to the quiz
        if ($question->quiz_id !== $attempt->quiz_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid question for this quiz',
            ], 400);
        }

        $result = $this->quizService->submitAnswer(
            $attempt,
            $question,
            $validated['answer'],
            $validated['time_taken'] ?? null
        );

        return response()->json($result);
    }

    public function complete(QuizAttempt $attempt)
    {
        $user = auth()->user();
        
        // Verify the attempt belongs to the user
        if ($attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $result = $this->quizService->completeQuiz($attempt);

        return response()->json($result);
    }

    public function results(QuizAttempt $attempt): View
    {
        $user = auth()->user();
        
        // Verify the attempt belongs to the user
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        return view('quizzes.results', [
            'results' => $this->quizService->getQuizResults($attempt),
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
        ]);
    }

    public function history(Request $request): View
    {
        $user = auth()->user();
        
        $lessonId = $request->input('lesson_id');
        
        return view('quizzes.history', [
            'history' => $this->quizService->getUserQuizHistory($user, $lessonId),
            'lessonId' => $lessonId,
        ]);
    }
}
