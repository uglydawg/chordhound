<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\AchievementService;
use App\Services\LearningPathService;
use App\Services\ProgressTrackingService;
use Livewire\Component;

class LearningDashboard extends Component
{
    public $learningPath;
    public $nextLesson;
    public $dashboardStats;
    public $recentAchievements;
    public $weeklyReport;
    
    public function mount()
    {
        $user = auth()->user();
        $learningPathService = app(LearningPathService::class);
        $progressTrackingService = app(ProgressTrackingService::class);
        $achievementService = app(AchievementService::class);
        
        $this->learningPath = $learningPathService->getUserLearningPath($user);
        $this->nextLesson = $learningPathService->getNextRecommendedLesson($user);
        $this->dashboardStats = $learningPathService->getUserDashboardStats($user);
        $this->recentAchievements = $achievementService->getRecentAchievements($user, 3);
        $this->weeklyReport = $progressTrackingService->getWeeklyReport($user);
    }
    
    public function startNextLesson()
    {
        if ($this->nextLesson) {
            return redirect()->route('lessons.show', $this->nextLesson);
        }
    }
    
    public function render()
    {
        return view('livewire.learning-dashboard');
    }
}
