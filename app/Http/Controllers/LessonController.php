<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonModule;
use App\Services\LearningPathService;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        private LearningPathService $learningPathService,
        private ProgressTrackingService $progressTrackingService
    ) {
        $this->middleware('auth');
    }

    public function index(LessonModule $module): View
    {
        $user = auth()->user();
        
        return view('lessons.index', [
            'module' => $module,
            'lessons' => $module->activeLessons()->get()->map(function ($lesson) use ($user) {
                return [
                    'lesson' => $lesson,
                    'progress' => $lesson->userProgress($user->id),
                    'isLocked' => $this->learningPathService->isLessonLocked($lesson, $user),
                ];
            }),
            'moduleProgress' => $this->learningPathService->calculateModuleProgress($module, $user),
        ]);
    }

    public function show(Lesson $lesson): View
    {
        $user = auth()->user();
        
        // Check if lesson is locked
        if ($this->learningPathService->isLessonLocked($lesson, $user)) {
            return redirect()->route('learning.index')
                ->with('error', 'This lesson is locked. Please complete previous lessons first.');
        }

        // Track lesson view
        $this->progressTrackingService->trackLessonView($lesson, $user);

        return view('lessons.show', [
            'lesson' => $lesson,
            'progress' => $lesson->userProgress($user->id),
            'nextLesson' => $lesson->getNextLesson(),
            'previousLesson' => $lesson->getPreviousLesson(),
            'quizzes' => $lesson->quizzes()->active()->get(),
        ]);
    }

    public function complete(Lesson $lesson, Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'score' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $this->learningPathService->completeLesson(
            $lesson,
            $user,
            $validated['score'] ?? null
        );

        $nextLesson = $lesson->getNextLesson();

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'nextLesson' => $nextLesson ? [
                'id' => $nextLesson->id,
                'title' => $nextLesson->title,
                'url' => route('lessons.show', $nextLesson),
            ] : null,
        ]);
    }

    public function updateProgress(Lesson $lesson, Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'progress_data' => 'required|array',
        ]);

        $progress = $this->progressTrackingService->updateLessonProgress(
            $lesson,
            $user,
            $validated['progress_data']
        );

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }
}
