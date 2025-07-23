<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <x-app-logo-icon class="size-12 fill-current text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Welcome to <span style="font-family: 'Henny Penny', cursive;">ChordHound</span>!</h1>
                    <p class="text-orange-100">Create, save, and play beautiful piano chord progressions</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('chords.index') }}" class="group relative overflow-hidden rounded-xl border border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 p-6 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-orange-500 p-2">
                        <svg class="size-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Chord Generator</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Create new chord progressions</p>
                    </div>
                </div>
            </a>

            @auth
            <a href="{{ route('chords.my-sets') }}" class="group relative overflow-hidden rounded-xl border border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 p-6 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-orange-500 p-2">
                        <svg class="size-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">My Chord Sets</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">View saved progressions</p>
                    </div>
                </div>
            </a>
            @endauth

            <a href="{{ route('settings.profile') }}" class="group relative overflow-hidden rounded-xl border border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 p-6 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-orange-500 p-2">
                        <svg class="size-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Settings</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Customize your experience</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Recent Activity or Tips --}}
        <div class="relative flex-1 overflow-hidden rounded-xl border border-orange-200 dark:border-orange-700 bg-white dark:bg-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🎵 Piano Tips from <span style="font-family: 'Henny Penny', cursive;">ChordHound</span></h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <h3 class="font-medium text-orange-800 dark:text-orange-200 mb-2">🐕 Tip #1: Voice Leading</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Keep common tones between chords to create smooth progressions, just like a good dog follows a scent trail!</p>
                </div>
                <div class="p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <h3 class="font-medium text-orange-800 dark:text-orange-200 mb-2">🎹 Tip #2: Blue Notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Blue notes add character and emotion to your progressions. Watch for them to light up in the generator!</p>
                </div>
                <div class="p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <h3 class="font-medium text-orange-800 dark:text-orange-200 mb-2">🎵 Tip #3: Inversions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Experiment with different inversions to create interesting bass lines and avoid boring root position chords.</p>
                </div>
                <div class="p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <h3 class="font-medium text-orange-800 dark:text-orange-200 mb-2">💾 Tip #4: Save Your Work</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Found a progression you love? Save it to your chord sets so you can return to it later!</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
