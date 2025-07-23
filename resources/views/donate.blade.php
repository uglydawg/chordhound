<x-layouts.app title="Support ChordHound">
    <div class="max-w-2xl mx-auto py-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">
                Support <span style="font-family: 'Henny Penny', cursive;">ChordHound</span> 🎵
            </h1>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-2">
                Help keep <span style="font-family: 'Henny Penny', cursive;">ChordHound</span> free and support future development
            </p>
            <p class="text-sm text-zinc-500 dark:text-zinc-300">
                Your donations help us maintain the servers, improve the platform, and add new features for the music community.
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-8 border border-zinc-200 dark:border-zinc-700">
            @livewire('support-site')
        </div>

        <div class="mt-8 text-center">
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3">
                    Why Support <span style="font-family: 'Henny Penny', cursive;">ChordHound</span>?
                </h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-zinc-600 dark:text-zinc-300">
                    <div class="flex items-start space-x-3">
                        <span class="text-orange-500 text-base">🎹</span>
                        <span>Free chord generation for all musicians</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-orange-500 text-base">🔧</span>
                        <span>Regular updates and new features</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-orange-500 text-base">🌐</span>
                        <span>Reliable hosting and fast performance</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-orange-500 text-base">📱</span>
                        <span>Mobile-friendly design</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-600">
                    <p class="text-xs text-zinc-500 dark:text-zinc-300">
                        <span style="font-family: 'Henny Penny', cursive;">ChordHound</span> is a passion project created by musicians, for musicians. Every contribution helps us keep the music flowing! 🎶
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>