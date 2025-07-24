<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mathematical Chord Calculator Test</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Test the new mathematical chord calculation engine</p>
    </div>

    <!-- Key and Progression Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Chord Progressions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Key Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Key</label>
                <select wire:model.live="selectedKey" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($roots as $key)
                        <option value="{{ $key }}">{{ $key }} Major</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Progression Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Progression</label>
                <select wire:model.live="selectedProgression" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($progressions as $name => $chords)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Speed Control -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Speed (seconds)</label>
                <select wire:model.live="playbackSpeed" wire:change="setPlaybackSpeed($event.target.value)" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="0.5">0.5s (Fast)</option>
                    <option value="1.0">1.0s (Normal)</option>
                    <option value="1.5">1.5s (Moderate)</option>
                    <option value="2.0">2.0s (Slow)</option>
                    <option value="3.0">3.0s (Very Slow)</option>
                </select>
            </div>
            
            <!-- Play Controls -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Controls</label>
                <div class="flex gap-2">
                    @if(!$isPlaying)
                        <button wire:click="playProgression" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition">
                            ▶ Play
                        </button>
                    @else
                        <button wire:click="stopProgression" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md transition">
                            ⏹ Stop
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Progression Display -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Current Progression in {{ $selectedKey }} Major</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($progressions[$selectedProgression] as $index => $roman)
                    @php
                        $isActive = $isPlaying && $currentChordIndex === $index;
                    @endphp
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $roman }}</div>
                        <div class="px-4 py-2 rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-white dark:bg-gray-600' }} {{ $isActive ? '' : 'text-gray-900 dark:text-white' }} font-bold transition-all duration-300">
                            @php
                                // Calculate the actual chord for display
                                $keyIndex = array_search($selectedKey, $roots);
                                $romanMap = [
                                    'I' => [0, 'maj'],
                                    'ii' => [2, 'min'],
                                    'iii' => [4, 'min'],
                                    'IV' => [5, 'maj'],
                                    'V' => [7, 'maj'],
                                    'vi' => [9, 'min'],
                                    'iv' => [5, 'min'],
                                    'II' => [2, 'maj'],
                                ];
                                if (isset($romanMap[$roman])) {
                                    [$interval, $quality] = $romanMap[$roman];
                                    $chordRoot = $roots[($keyIndex + $interval) % 12];
                                    echo $chordRoot . $quality;
                                }
                            @endphp
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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
                                <div class="bg-white dark:bg-gray-600 rounded-lg px-4 py-2 shadow-sm hover:shadow-md transition-all cursor-pointer hover:bg-yellow-50 dark:hover:bg-yellow-900/20"
                                     x-data
                                     @mouseenter="$wire.dispatchTo('math-chord-piano', 'highlight-note', { note: '{{ $note }}' })"
                                     @mouseleave="$wire.dispatchTo('math-chord-piano', 'highlight-note', { note: null })">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $index === 0 ? 'Root/Bass' : ($index === 1 ? 'Third' : ($index === 2 ? 'Fifth' : 'Note ' . $index)) }}
                                    </div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $note }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                        @php
                                            $noteOnly = preg_replace('/\d+$/', '', $note);
                                            $octave = preg_replace('/^[A-G]#?/', '', $note);
                                        @endphp
                                        <span class="font-mono">{{ $noteOnly }}</span>
                                        <span class="text-gray-400">{{ $octave }}</span>
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
                                    {{ implode(' - ', $calculatedNotes) }}
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
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Chord Type:</dt>
                                <dd class="font-mono text-gray-900 dark:text-white">
                                    {{ ucfirst($type) }} {{ $inversions[$inversion] }}
                                </dd>
                            </div>
                            @if(count($calculatedNotes) >= 3)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600 dark:text-gray-400">Intervals from Root:</dt>
                                    <dd class="font-mono text-gray-900 dark:text-white text-xs">
                                        @php
                                            $intervals = [];
                                            if ($type === 'major') {
                                                $intervals = ['Root', 'Major 3rd', 'Perfect 5th'];
                                            } elseif ($type === 'minor') {
                                                $intervals = ['Root', 'Minor 3rd', 'Perfect 5th'];
                                            } elseif ($type === 'diminished') {
                                                $intervals = ['Root', 'Minor 3rd', 'Diminished 5th'];
                                            } elseif ($type === 'augmented') {
                                                $intervals = ['Root', 'Major 3rd', 'Augmented 5th'];
                                            }
                                        @endphp
                                        {{ implode(', ', array_slice($intervals, 0, count($calculatedNotes))) }}
                                    </dd>
                                </div>
                            @endif
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
        <div class="overflow-x-auto">
            @if(!empty($calculatedNotes))
                <livewire:math-chord-piano 
                    :calculatedNotes="$calculatedNotes"
                    :chordName="$root . ' ' . ucfirst($type) . ' - ' . $inversions[$inversion]"
                    :larger="true"
                    :showLabels="true"
                    :key="'math-chord-piano-' . $root . '-' . $type . '-' . $inversion . '-' . implode('-', $calculatedNotes)"
                />
                <div class="mt-4 flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-400 rounded"></div>
                        <span class="text-gray-600 dark:text-gray-400">Active Note</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-yellow-400 rounded"></div>
                        <span class="text-gray-600 dark:text-gray-400">Highlighted Note (hover)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <span class="text-gray-600 dark:text-gray-400">Inactive Key</span>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <p>Configure chord parameters above to see piano visualization</p>
                </div>
            @endif
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
        
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">Common Chord Progressions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button wire:click="$set('root', 'C'); $set('type', 'major'); $set('startPosition', 'C4')" 
                        class="text-left p-3 bg-blue-50 dark:bg-blue-900/20 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    <div class="font-semibold text-blue-900 dark:text-blue-100">I-IV-V-I (Classic)</div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">C - F - G - C</div>
                </button>
                <button wire:click="$set('root', 'C'); $set('type', 'major'); $set('startPosition', 'E4')" 
                        class="text-left p-3 bg-purple-50 dark:bg-purple-900/20 rounded hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                    <div class="font-semibold text-purple-900 dark:text-purple-100">I-vi-IV-V (Pop)</div>
                    <div class="text-sm text-purple-700 dark:text-purple-300">C - Am - F - G</div>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Quick Progression Launcher -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Quick Progression Examples</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <button wire:click="$set('selectedKey', 'C'); $set('selectedProgression', 'I-IV-V-I'); playProgression()" 
                    class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded hover:bg-blue-200 dark:hover:bg-blue-900/30 transition text-left">
                <div class="font-semibold text-blue-900 dark:text-blue-100">Classic (I-IV-V-I)</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">C Major: C - F - G - C</div>
            </button>
            <button wire:click="$set('selectedKey', 'G'); $set('selectedProgression', 'I-V-vi-IV'); playProgression()" 
                    class="p-3 bg-green-100 dark:bg-green-900/20 rounded hover:bg-green-200 dark:hover:bg-green-900/30 transition text-left">
                <div class="font-semibold text-green-900 dark:text-green-100">Pop (I-V-vi-IV)</div>
                <div class="text-sm text-green-700 dark:text-green-300">G Major: G - D - Em - C</div>
            </button>
            <button wire:click="$set('selectedKey', 'F'); $set('selectedProgression', 'I-vi-IV-V'); playProgression()" 
                    class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded hover:bg-purple-200 dark:hover:bg-purple-900/30 transition text-left">
                <div class="font-semibold text-purple-900 dark:text-purple-100">50s (I-vi-IV-V)</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">F Major: F - Dm - Bb - C</div>
            </button>
            <button wire:click="$set('selectedKey', 'D'); $set('selectedProgression', 'vi-IV-I-V'); playProgression()" 
                    class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded hover:bg-orange-200 dark:hover:bg-orange-900/30 transition text-left">
                <div class="font-semibold text-orange-900 dark:text-orange-100">Alternative (vi-IV-I-V)</div>
                <div class="text-sm text-orange-700 dark:text-orange-300">D Major: Bm - G - D - A</div>
            </button>
            <button wire:click="$set('selectedKey', 'A'); $set('selectedProgression', 'ii-V-I'); playProgression()" 
                    class="p-3 bg-red-100 dark:bg-red-900/20 rounded hover:bg-red-200 dark:hover:bg-red-900/30 transition text-left">
                <div class="font-semibold text-red-900 dark:text-red-100">Jazz (ii-V-I)</div>
                <div class="text-sm text-red-700 dark:text-red-300">A Major: Bm - E - A</div>
            </button>
            <button wire:click="$set('selectedKey', 'E'); $set('selectedProgression', 'I-iv-I-II'); playProgression()" 
                    class="p-3 bg-indigo-100 dark:bg-indigo-900/20 rounded hover:bg-indigo-200 dark:hover:bg-indigo-900/30 transition text-left">
                <div class="font-semibold text-indigo-900 dark:text-indigo-100">Modal Mix (I-iv-I-II)</div>
                <div class="text-sm text-indigo-700 dark:text-indigo-300">E Major: E - Am - E - F#</div>
            </button>
        </div>
    </div>
</div>

@script
<script>
    // Initialize piano player
    let pianoPlayer = null;
    let progressionTimer = null;
    
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
    
    // Listen for progression timing events
    $wire.on('schedule-next-chord', (event) => {
        if (progressionTimer) {
            clearTimeout(progressionTimer);
        }
        progressionTimer = setTimeout(() => {
            $wire.playNextChord();
        }, event.delay);
    });
    
    // Listen for progression chord changes
    $wire.on('progression-chord-changed', (event) => {
        console.log('Progression chord:', event.index, event.roman, event.root, event.type);
    });
</script>
@endscript