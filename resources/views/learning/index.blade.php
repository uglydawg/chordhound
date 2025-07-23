<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Learning Hub</h1>
                <p class="mt-2 text-lg text-gray-600">Master piano chords with our interactive lessons</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Progress</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $dashboardStats['progress_percentage'] }}%</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <flux:icon name="chart-pie" class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Lessons Completed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $dashboardStats['completed_lessons'] }}/{{ $dashboardStats['total_lessons'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <flux:icon name="academic-cap" class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Practice Time</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $dashboardStats['total_time_minutes'] }} min</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <flux:icon name="clock" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Achievement Points</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $dashboardStats['achievement_points'] }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <flux:icon name="star" class="w-6 h-6 text-yellow-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Lesson CTA -->
            @if($nextLesson)
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <h3 class="text-xl font-semibold">Continue Learning</h3>
                            <p class="mt-1">{{ $nextLesson->title }}</p>
                            <p class="text-sm opacity-90">{{ $nextLesson->module->name }} • {{ $nextLesson->estimated_time }} min</p>
                        </div>
                        <flux:button variant="primary" href="{{ route('lessons.show', $nextLesson) }}" class="bg-white text-purple-600 hover:bg-gray-100">
                            Start Lesson
                        </flux:button>
                    </div>
                </div>
            @endif

            <!-- Recent Achievements -->
            @if($recentAchievements->count() > 0)
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent Achievements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($recentAchievements as $achievement)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="p-2 bg-yellow-100 rounded-full">
                                    <flux:icon name="trophy" class="w-5 h-5 text-yellow-600" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $achievement['name'] }}</p>
                                    <p class="text-sm text-gray-600">+{{ $achievement['points'] }} points</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Learning Modules -->
            <div class="space-y-6">
                @foreach($learningPath as $module)
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $module['name'] }}</h3>
                                <p class="text-gray-600">{{ $module['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-600">{{ $module['progress']['percentage'] }}%</p>
                                <p class="text-sm text-gray-600">{{ $module['progress']['completed'] }}/{{ $module['progress']['total'] }} lessons</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $module['progress']['percentage'] }}%"></div>
                        </div>

                        <!-- Lessons Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($module['lessons'] as $lesson)
                                <div class="border rounded-lg p-4 {{ $lesson['is_locked'] ? 'bg-gray-50 opacity-60' : 'hover:shadow-md transition-shadow' }}">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $lesson['title'] }}</h4>
                                        @if($lesson['status'] === 'completed')
                                            <flux:icon name="check-circle" class="w-5 h-5 text-green-600" />
                                        @elseif($lesson['status'] === 'in_progress')
                                            <flux:icon name="clock" class="w-5 h-5 text-blue-600" />
                                        @elseif($lesson['is_locked'])
                                            <flux:icon name="lock-closed" class="w-5 h-5 text-gray-400" />
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ $lesson['description'] }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $lesson['estimated_time'] }} min</span>
                                        @if(!$lesson['is_locked'])
                                            <flux:button size="sm" variant="secondary" href="{{ route('lessons.show', $lesson['id']) }}">
                                                {{ $lesson['status'] === 'completed' ? 'Review' : 'Start' }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-center">
                            <flux:button variant="secondary" href="{{ route('lessons.index', $module['id']) }}">
                                View All Lessons
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>