<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonModule;
use App\Models\Quiz;
use App\Models\User;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function __construct(
        private ProgressTrackingService $progressTrackingService
    ) {
        $this->middleware(['auth', 'teacher']);
    }

    public function index(): View
    {
        $stats = $this->getOverallStats();
        $recentActivity = $this->getRecentActivity();
        $topPerformers = $this->getTopPerformers();
        
        return view('teacher.dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'topPerformers' => $topPerformers,
        ]);
    }

    public function students(): View
    {
        $students = User::whereHas('lessonProgress')
            ->orWhereHas('quizAttempts')
            ->paginate(20);

        $students->each(function ($student) {
            $student->overall_progress = $student->getOverallProgress();
            $student->completed_lessons = $student->getCompletedLessonsCount();
            $student->achievement_points = $student->getTotalAchievementPoints();
        });

        return view('teacher.students', [
            'students' => $students,
        ]);
    }

    public function studentDetail(User $student): View
    {
        return view('teacher.student-detail', [
            'student' => $student,
            'learningPath' => app(\App\Services\LearningPathService::class)->getUserLearningPath($student),
            'weeklyReport' => $this->progressTrackingService->getWeeklyReport($student),
            'skillProgress' => $this->progressTrackingService->getSkillProgress($student),
            'quizHistory' => app(\App\Services\QuizService::class)->getUserQuizHistory($student),
            'achievements' => app(\App\Services\AchievementService::class)->getUserAchievements($student),
        ]);
    }

    public function moduleProgress(LessonModule $module): View
    {
        $students = User::whereHas('lessonProgress.lesson', function ($query) use ($module) {
                $query->where('module_id', $module->id);
            })
            ->get()
            ->map(function ($student) use ($module) {
                $progress = $this->progressTrackingService->getModuleProgress($module, $student);
                
                return [
                    'student' => $student,
                    'progress' => $progress['summary'],
                ];
            })
            ->sortByDesc('progress.percentage');

        return view('teacher.module-progress', [
            'module' => $module,
            'students' => $students,
        ]);
    }

    public function quizResults(Quiz $quiz): View
    {
        $attempts = $quiz->attempts()
            ->with('user')
            ->completed()
            ->orderBy('percentage', 'desc')
            ->get();

        $statistics = app(\App\Services\QuizService::class)->getQuizStatistics($quiz);

        return view('teacher.quiz-results', [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'statistics' => $statistics,
        ]);
    }

    protected function getOverallStats(): array
    {
        return [
            'total_students' => User::whereHas('lessonProgress')->count(),
            'active_students' => User::whereHas('lessonProgress', function ($query) {
                $query->where('updated_at', '>=', now()->subDays(7));
            })->count(),
            'total_lessons_completed' => DB::table('lesson_progress')
                ->where('status', 'completed')
                ->count(),
            'average_quiz_score' => DB::table('quiz_attempts')
                ->where('completed_at', '!=', null)
                ->avg('percentage') ?? 0,
        ];
    }

    protected function getRecentActivity()
    {
        $lessonActivity = DB::table('lesson_progress')
            ->join('users', 'lesson_progress.user_id', '=', 'users.id')
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->select(
                'users.name as student_name',
                'lessons.title as lesson_title',
                'lesson_progress.status',
                'lesson_progress.updated_at'
            )
            ->orderBy('lesson_progress.updated_at', 'desc')
            ->limit(10)
            ->get();

        $quizActivity = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->select(
                'users.name as student_name',
                'quizzes.title as quiz_title',
                'quiz_attempts.percentage',
                'quiz_attempts.passed',
                'quiz_attempts.completed_at as updated_at'
            )
            ->whereNotNull('quiz_attempts.completed_at')
            ->orderBy('quiz_attempts.completed_at', 'desc')
            ->limit(10)
            ->get();

        return collect($lessonActivity)
            ->merge($quizActivity)
            ->sortByDesc('updated_at')
            ->take(15);
    }

    protected function getTopPerformers()
    {
        return User::select('users.*')
            ->selectRaw('COUNT(DISTINCT lesson_progress.id) as completed_lessons')
            ->selectRaw('AVG(quiz_attempts.percentage) as average_quiz_score')
            ->leftJoin('lesson_progress', function ($join) {
                $join->on('users.id', '=', 'lesson_progress.user_id')
                    ->where('lesson_progress.status', '=', 'completed');
            })
            ->leftJoin('quiz_attempts', function ($join) {
                $join->on('users.id', '=', 'quiz_attempts.user_id')
                    ->whereNotNull('quiz_attempts.completed_at');
            })
            ->groupBy('users.id')
            ->having('completed_lessons', '>', 0)
            ->orderByDesc('completed_lessons')
            ->orderByDesc('average_quiz_score')
            ->limit(10)
            ->get();
    }
}
