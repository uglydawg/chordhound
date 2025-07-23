<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AchievementService;
use App\Services\LearningPathService;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function __construct(
        private LearningPathService $learningPathService,
        private ProgressTrackingService $progressTrackingService,
        private AchievementService $achievementService
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();
        
        return view('learning.index', [
            'learningPath' => $this->learningPathService->getUserLearningPath($user),
            'nextLesson' => $this->learningPathService->getNextRecommendedLesson($user),
            'dashboardStats' => $this->learningPathService->getUserDashboardStats($user),
            'recentAchievements' => $this->achievementService->getRecentAchievements($user),
        ]);
    }

    public function progress(): View
    {
        $user = auth()->user();
        
        return view('learning.progress', [
            'weeklyReport' => $this->progressTrackingService->getWeeklyReport($user),
            'progressHistory' => $this->progressTrackingService->getUserProgressHistory($user),
            'skillProgress' => $this->progressTrackingService->getSkillProgress($user),
        ]);
    }

    public function achievements(): View
    {
        $user = auth()->user();
        
        return view('learning.achievements', [
            'achievements' => $this->achievementService->getUserAchievements($user),
            'totalPoints' => $user->getTotalAchievementPoints(),
            'leaderboard' => $this->achievementService->getAchievementLeaderboard(),
        ]);
    }
}
