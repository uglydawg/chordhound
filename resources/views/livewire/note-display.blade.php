<div class="w-full px-4 py-2">
    @if (empty($noteNames) && $showInstructions)
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p class="text-lg">Click a chord to see the notes</p>
        </div>
    @elseif (empty($noteNames))
        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
            <p class="text-sm">No notes playing</p>
        </div>
    @else
        <div class="w-full max-w-2xl mx-auto">
            {{-- Bass note display (if enabled) --}}
            @if ($bassNote)
                <div class="text-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Bass:</span>
                    <span class="ml-2 text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $bassNote }}</span>
                </div>
            @endif

            {{-- Main chord notes display --}}
            <div class="flex flex-wrap gap-3 justify-center items-center py-4">
                @foreach ($noteNames as $note)
                    <div
                        class="inline-flex items-center justify-center px-6 py-3 rounded-lg shadow-md border-2 transition-all duration-200 hover:shadow-lg hover:scale-105 @if ($highlightAccidentals && $this->isAccidental($note)) bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-700 @else bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 @endif"
                        x-data="{ show: false }"
                        x-init="setTimeout(() => show = true, {{ $loop->index * 100 }})"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-50"
                        x-transition:enter-end="opacity-100 scale-100"
                    >
                        <span class="text-4xl font-bold select-none font-mono @if ($highlightAccidentals && $this->isAccidental($note)) text-orange-600 dark:text-orange-400 @else text-gray-900 dark:text-gray-100 @endif" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);">
                            {{ $note }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Note count indicator --}}
            <div class="mt-4 text-center">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($noteNames) }} {{ Str::plural('note', count($noteNames)) }}
                </span>
            </div>
        </div>
    @endif
</div>
