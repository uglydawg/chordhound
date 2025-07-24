<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mathematical Chord Calculator Test</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Test the new mathematical chord calculation engine</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Chord Parameters</h2>
            
            <div class="space-y-4">
                <!-- Root Note -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Root Note
                    </label>
                    <select wire:model.live="root" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($roots as $rootNote)
                            <option value="{{ $rootNote }}">{{ $rootNote }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Chord Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Chord Type
                    </label>
                    <select wire:model.live="type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($types as $chordType)
                            <option value="{{ $chordType }}">{{ ucfirst($chordType) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Starting Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Starting Position
                    </label>
                    <input 
                        type="text" 
                        wire:model.live="startPosition" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="e.g., C4, G5"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter a note with octave (e.g., C4, E5, G3)</p>
                </div>

                <!-- Inversion -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Inversion
                    </label>
                    <select wire:model.live="inversion" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($inversions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Play Button -->
                <div class="pt-4">
                    <button 
                        wire:click="playChord"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition"
                    >
                        Play Chord
                    </button>
                </div>

                <!-- Voice Leading Test -->
                @if($root === 'C' && $type === 'major')
                <div class="pt-2">
                    <button 
                        wire:click="compareWithHardcoded"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md transition"
                    >
                        Calculate Voice Leading to F Major
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Results -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Calculated Notes</h2>
            
            @if($error)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-4">
                    <p class="text-red-600 dark:text-red-400">{{ $error }}</p>
                </div>
            @endif

            @if(!empty($calculatedNotes))
                <div class="space-y-4">
                    <!-- Note Display -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $root }} {{ ucfirst($type) }} - {{ $inversions[$inversion] }}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($calculatedNotes as $index => $note)
                                <div class="bg-white dark:bg-gray-600 rounded-lg px-4 py-2 shadow-sm">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $index === 0 ? 'Bass' : 'Note ' . $index }}
                                    </div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $note }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Technical Details</h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Chord Formula:</dt>
                                <dd class="font-mono text-gray-900 dark:text-white">
                                    {{ implode('-', $calculatedNotes) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Note Count:</dt>
                                <dd class="font-mono text-gray-900 dark:text-white">{{ count($calculatedNotes) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Bass Note:</dt>
                                <dd class="font-mono text-gray-900 dark:text-white">{{ $calculatedNotes[0] ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Voice Leading Result -->
                    @if(session('voiceLeading'))
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-md p-4">
                            <p class="text-purple-600 dark:text-purple-400">{{ session('voiceLeading') }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Visual Piano Display -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Piano Visualization</h2>
        <div id="piano-container" class="overflow-x-auto">
            <!-- Piano will be rendered here -->
        </div>
    </div>

    <!-- Example Progressions -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Example Progressions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button wire:click="$set('root', 'C'); $set('type', 'major'); $set('inversion', 0)" 
                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                C Major
            </button>
            <button wire:click="$set('root', 'A'); $set('type', 'minor'); $set('inversion', 0)" 
                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                A Minor
            </button>
            <button wire:click="$set('root', 'G'); $set('type', 'major'); $set('inversion', 1)" 
                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                G Major (1st Inv)
            </button>
            <button wire:click="$set('root', 'D'); $set('type', 'minor'); $set('inversion', 2)" 
                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                D Minor (2nd Inv)
            </button>
        </div>
    </div>
</div>

@script
<script>
    // Initialize piano player
    let pianoPlayer = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Import the MultiInstrumentPlayer
        if (window.MultiInstrumentPlayer) {
            pianoPlayer = new window.MultiInstrumentPlayer();
        }
    });

    // Listen for play chord events
    $wire.on('play-math-chord', (event) => {
        if (pianoPlayer && event.notes) {
            console.log('Playing chord:', event.notes);
            pianoPlayer.playChord(event.notes, 1.5);
        }
    });
</script>
@endscript