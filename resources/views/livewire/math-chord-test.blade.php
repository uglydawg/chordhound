<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="mathChordPlayer">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mathematical Chord Calculator Test</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Test the new mathematical chord calculation engine</p>
    </div>

    <!-- Key and Progression Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Chord Progressions</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
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
            
            <!-- Rhythm Pattern -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rhythm</label>
                <select wire:model.live="selectedRhythm" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($rhythmPatterns as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Time Signature -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                <select wire:model.live="timeSignature" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($timeSignatures as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- BPM Control -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">BPM</label>
                <select wire:model.live="bpm" wire:change="setBpm($event.target.value)" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="60">60 (Slow)</option>
                    <option value="80">80 (Relaxed)</option>
                    <option value="100">100 (Moderate)</option>
                    <option value="120">120 (Standard)</option>
                    <option value="140">140 (Upbeat)</option>
                    <option value="160">160 (Fast)</option>
                    <option value="180">180 (Very Fast)</option>
                </select>
            </div>
            
            <!-- Starting Inversion -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Inv</label>
                <select wire:model.live="startingInversion" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="0">Root</option>
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                </select>
            </div>
        </div>
        
        <!-- Play Controls - Separate Row -->
        <div class="flex items-center gap-4">
            <div class="flex gap-2">
                @if(!$isPlaying)
                    <button wire:click="playRhythm" class="bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded-md transition text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3l14 7-14 7V3z"/>
                        </svg>
                        Play
                    </button>
                @else
                    <button wire:click="stopProgression" class="bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-md transition text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <rect x="5" y="5" width="10" height="10"/>
                        </svg>
                        Stop
                    </button>
                @endif
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Playing: {{ $rhythmPatterns[$selectedRhythm] }} in {{ $timeSignature }} @ {{ $bpm }} BPM
            </span>
        </div>
        
        <!-- Progression Display -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Current Progression in {{ $selectedKey }} Major</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($progressions[$selectedProgression] as $index => $roman)
                    @php
                        $isActive = $isPlaying && $currentChordIndex === $index;
                        // Calculate the actual chord for display
                        $keyIndex = array_search($selectedKey, $roots);
                        $romanMap = [
                            'I' => [0, 'major'],
                            'ii' => [2, 'minor'],
                            'iii' => [4, 'minor'],
                            'IV' => [5, 'major'],
                            'V' => [7, 'major'],
                            'vi' => [9, 'minor'],
                            'iv' => [5, 'minor'],
                            'II' => [2, 'major'],
                        ];
                        $chordRoot = $selectedKey;
                        $chordType = 'major';
                        if (isset($romanMap[$roman])) {
                            [$interval, $chordType] = $romanMap[$roman];
                            $chordRoot = $roots[($keyIndex + $interval) % 12];
                        }
                        $chordLabel = $chordRoot . ($chordType === 'major' ? 'maj' : 'min');
                    @endphp
                    <button 
                        wire:click="$set('root', '{{ $chordRoot }}'); $set('type', '{{ $chordType }}'); calculateChord()"
                        class="text-center group cursor-pointer">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $roman }}</div>
                        <div class="px-4 py-2 rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-white dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500' }} {{ $isActive ? '' : 'text-gray-900 dark:text-white' }} font-bold transition-all duration-300">
                            {{ $chordLabel }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Chord Parameters</h2>
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Root Note -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Root Note
                    </label>
                    <select wire:model.live="root" class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($roots as $rootNote)
                            <option value="{{ $rootNote }}">{{ $rootNote }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Chord Type -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Chord Type
                    </label>
                    <select wire:model.live="type" class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($types as $chordType)
                            <option value="{{ $chordType }}">{{ ucfirst($chordType) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Starting Position -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Starting Octave
                    </label>
                    <select 
                        wire:model.live="startPosition" 
                        class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        @php
                            $octaves = [
                                $root . '3' => 'Octave 3',
                                $root . '4' => 'Octave 4',
                                $root . '5' => 'Octave 5',
                                $root . '6' => 'Octave 6'
                            ];
                        @endphp
                        @foreach($octaves as $position => $label)
                            <option value="{{ $position }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Inversion -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Inversion
                    </label>
                    <select wire:model.live="inversion" class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($inversions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-3 grid grid-cols-1 gap-2">
                <button 
                    wire:click="playChord"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded text-sm transition"
                >
                    Play Chord
                </button>

                @if($root === 'C' && $type === 'major')
                <button 
                    wire:click="compareWithHardcoded"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-1.5 px-3 rounded text-sm transition"
                >
                    Voice Leading to F Major
                </button>
                @endif
            </div>
        </div>

        <!-- Results -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            @if($error)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-2 mb-2">
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $error }}</p>
                </div>
            @endif

            @if(!empty($calculatedNotes))
                <div class="space-y-3">
                    <!-- Compact Note Display -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $root }} {{ ucfirst($type) }} 
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    {{ $inversions[$inversion] }}
                                </span>
                            </h2>
                            <span class="text-sm font-mono text-gray-600 dark:text-gray-300">
                                {{ implode(' - ', $calculatedNotes) }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            @foreach($calculatedNotes as $index => $note)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded px-3 py-1.5 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all cursor-pointer"
                                     x-data>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $index === 0 ? 'Bass' : ($index === 1 ? '3rd' : ($index === 2 ? '5th' : 'N' . $index)) }}
                                    </div>
                                    <div class="text-base font-bold text-gray-900 dark:text-white">
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

    <!-- Piano Player Visualization -->
    <div class="mt-8">
        @if(!empty($calculatedNotes))
            <livewire:piano-player 
                :currentChord="[
                    'tone' => $root,
                    'semitone' => $type,
                    'inversion' => $inversions[$inversion]
                ]"
                :showLabels="true"
                :tempo="$bpm"
                :key="'piano-player-' . $root . '-' . $type . '-' . $inversion . '-' . $bpm . '-' . implode('-', $calculatedNotes)"
            />
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Piano Visualization</h2>
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <p>Configure chord parameters above to see piano visualization</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Voice Leading Analysis -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Voice Leading Analysis</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Optimal inversions for smooth voice leading in {{ $selectedKey }} Major - {{ $selectedProgression }}
        </p>
        
        @if(!empty($voiceLeadingAnalysis))
            <div class="space-y-6">
                @foreach($voiceLeadingAnalysis as $index => $analysis)
                    <div class="border {{ $index === 0 ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold {{ $index === 0 ? 'text-green-700 dark:text-green-300' : 'text-gray-900 dark:text-white' }}">
                                Starting with {{ $inversions[$analysis['startingInversion']] }}
                            </h3>
                            <div class="flex items-center gap-2">
                                @if($index === 0)
                                    <span class="text-xs bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 px-2 py-1 rounded">
                                        OPTIMAL
                                    </span>
                                @endif
                                <span class="text-sm font-mono {{ $index === 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                    Total Distance: {{ $analysis['totalDistance'] }} semitones
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach($analysis['sequence'] as $chordIndex => $chord)
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $chord['roman'] }}</div>
                                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-3 py-2">
                                        <div class="font-semibold text-sm">{{ $chord['chord'] }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $inversions[$chord['inversion']] }}</div>
                                        @if($chord['distance'] > 0)
                                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                ↔ {{ $chord['distance'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($chordIndex < count($analysis['sequence']) - 1)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                            <details>
                                <summary class="cursor-pointer hover:text-gray-800 dark:hover:text-gray-200">View note sequences</summary>
                                <div class="mt-2 space-y-1 font-mono">
                                    @foreach($analysis['sequence'] as $chord)
                                        <div>{{ $chord['roman'] }} ({{ $chord['chord'] }}): {{ $chord['notes'] }}</div>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                <p>Select a key and progression to see voice leading analysis</p>
            </div>
        @endif
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

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mathChordPlayer', () => ({
        pianoPlayer: null,
        progressionTimer: null,
        pressedKeys: new Map(), // Track pressed keys with reference counting
        
        init() {
            // Initialize the piano player with retry mechanism
            this.initializePianoPlayer();
            
            // Try to resume audio context on first user interaction
            document.addEventListener('click', () => {
                if (this.pianoPlayer && this.pianoPlayer.context && this.pianoPlayer.context.state === 'suspended') {
                    this.pianoPlayer.context.resume().then(() => {
                        console.log('Audio context resumed');
                    });
                }
            }, { once: true });
            
            // Listen for play chord events
            this.$wire.on('play-math-chord', (event) => {
                console.log('Play chord event received:', event);
                const notes = event.detail?.notes || event.notes;
                if (this.pianoPlayer && notes) {
                    console.log('Playing chord:', notes);
                    
                    // Check if piano player is ready
                    if (!this.pianoPlayer.isLoaded) {
                        console.warn('Piano samples still loading, trying to play anyway...');
                    }
                    
                    // Play audio and show visual feedback
                    this.playChordWithVisualization(notes, 1.5, 0);
                } else {
                    console.error('Piano Player not ready or no notes found:', {
                        hasPianoPlayer: !!this.pianoPlayer,
                        hasNotes: !!notes,
                        event: event
                    });
                }
            });
            
            // Listen for rhythm pattern events
            this.$wire.on('play-rhythm-pattern', (event) => {
                console.log('Play rhythm pattern:', event);
                const data = event.detail || event;
                if (this.pianoPlayer && data.notes && data.pattern) {
                    console.log('BPM:', data.bpm, 'Time signature:', data.timeSignature, 'Pattern duration:', data.pattern.duration, 'Measure duration:', data.measureDuration);
                    
                    // Check if piano player is loaded
                    if (!this.pianoPlayer.isLoaded) {
                        console.warn('Piano samples still loading for rhythm pattern, trying to play anyway...');
                    }
                    
                    this.playRhythmPattern(data.notes, data.pattern, data.rhythm, data.measureDuration || 2.0);
                } else {
                    console.error('Rhythm pattern failed to start:', {
                        hasPianoPlayer: !!this.pianoPlayer,
                        hasNotes: !!data.notes,
                        hasPattern: !!data.pattern,
                        data: data
                    });
                }
            });
            
            // Listen for progression timing events
            this.$wire.on('schedule-next-chord', (event) => {
                console.log('Schedule next chord event:', event);
                if (this.progressionTimer) {
                    clearTimeout(this.progressionTimer);
                }
                const delay = event.detail?.delay || event.delay || 1000;
                this.progressionTimer = setTimeout(() => {
                    this.$wire.playNextChord();
                }, delay);
            });
            
            // Listen for progression chord changes
            this.$wire.on('progression-chord-changed', (event) => {
                console.log('Progression chord:', event.index, event.roman, event.root, event.type);
            });
        },
        
        initializePianoPlayer() {
            if (window.MultiInstrumentPlayer) {
                this.pianoPlayer = new window.MultiInstrumentPlayer();
                console.log('MultiInstrumentPlayer initialized');
            } else {
                console.warn('MultiInstrumentPlayer not found, retrying in 500ms...');
                setTimeout(() => {
                    this.initializePianoPlayer();
                }, 500);
            }
        },
        
        // Calculate realistic sustain duration based on acoustic piano data
        getRealisticSustainDuration(note, baseDuration = 2.0) {
            // Extract octave number from note (e.g., "C4" -> 4, "A0" -> 0)
            const octaveMatch = note.match(/([A-G]#?)(\d+)/);
            if (!octaveMatch) return baseDuration;
            
            const octave = parseInt(octaveMatch[2]);
            
            // Based on acoustic piano sustain data:
            // Low/Bass (A0 to C2): 30-60+ seconds sustain
            // Middle (C3 to C5): 10-20 seconds sustain  
            // High/Treble (C6 to C8): 1-5 seconds sustain
            
            // Using 2 seconds as reference for middle range, scale by octave
            if (octave <= 2) {
                // Bass range: 3-4x longer sustain than reference
                return baseDuration * (3.5 - (octave * 0.25)); // A0: 3.5x, C2: 3.0x
            } else if (octave <= 5) {
                // Middle range: use reference duration with slight variation
                return baseDuration * (1.2 - ((octave - 3) * 0.1)); // C3: 1.2x, C5: 1.0x
            } else {
                // Treble range: much shorter sustain
                return baseDuration * Math.max(0.2, 0.8 - ((octave - 6) * 0.2)); // C6: 0.6x, C8+: 0.2x
            }
        },
        
        playRhythmPattern(notes, pattern, rhythm, measureDuration) {
            if (!this.pianoPlayer) {
                console.error('Piano player not initialized for rhythm pattern');
                return;
            }
            
            if (!notes || notes.length === 0) {
                console.error('No notes provided for rhythm pattern');
                return;
            }
            
            const duration = pattern.duration * 1000; // Convert to ms (individual note duration)
            const fullMeasureDuration = measureDuration; // Full measure duration in seconds
            const bassNote = notes[0]; // First note is bass
            const chordNotes = notes.slice(1); // Rest are chord notes
            
            console.log('Playing rhythm:', rhythm, 'note duration:', duration, 'measure duration:', fullMeasureDuration, 'bass:', bassNote, 'chord notes:', chordNotes);
            
            // Calculate realistic sustain duration for bass note based on acoustic piano data
            const bassSustainDuration = this.getRealisticSustainDuration(bassNote, fullMeasureDuration);
            console.log('Playing realistic sustained bass note:', bassNote, 'for', bassSustainDuration, 'seconds (vs measure', fullMeasureDuration, 'seconds)');
            this.playNoteWithVisualization(bassNote, bassSustainDuration, 0);
            
            switch (rhythm) {
                case 'alberti':
                    // Alberti bass: 5th-3rd-5th pattern on top of sustained bass (16th notes)
                    if (chordNotes.length >= 2) {
                        const fifthNote = chordNotes[1] || chordNotes[0];
                        const thirdNote = chordNotes[0];
                        const noteBaseDuration = 0.3;
                        
                        this.playNoteWithVisualization(fifthNote, this.getRealisticSustainDuration(fifthNote, noteBaseDuration), 0);
                        this.playNoteWithVisualization(thirdNote, this.getRealisticSustainDuration(thirdNote, noteBaseDuration), duration / 1000);
                        this.playNoteWithVisualization(fifthNote, this.getRealisticSustainDuration(fifthNote, noteBaseDuration), (duration * 2) / 1000);
                        this.playNoteWithVisualization(thirdNote, this.getRealisticSustainDuration(thirdNote, noteBaseDuration), (duration * 3) / 1000);
                    }
                    break;
                    
                case 'waltz':
                    // Waltz: sustained bass, chord on beats 2 and 3
                    this.playChordWithRealisticSustain(chordNotes, 0.4, duration / 1000);
                    this.playChordWithRealisticSustain(chordNotes, 0.4, (duration * 2) / 1000);
                    break;
                    
                case 'broken':
                    // Broken chord: play chord notes sequentially on top of sustained bass (8th notes)
                    chordNotes.forEach((note, i) => {
                        const noteBaseDuration = 0.4;
                        this.playNoteWithVisualization(note, this.getRealisticSustainDuration(note, noteBaseDuration), (i * duration) / 1000);
                    });
                    break;
                    
                case 'arpeggio':
                    // Arpeggio up chord notes on top of sustained bass (16th notes)
                    chordNotes.forEach((note, i) => {
                        const noteBaseDuration = 0.3;
                        this.playNoteWithVisualization(note, this.getRealisticSustainDuration(note, noteBaseDuration), (i * duration) / 1000);
                    });
                    break;
                    
                case 'march':
                    // March: sustained bass with strong-weak chord pattern (quarter notes)
                    this.playChordWithRealisticSustain(chordNotes, 0.6, 0);
                    this.playChordWithRealisticSustain(chordNotes, 0.4, duration / 1000);
                    break;
                    
                case 'ballad':
                    // Ballad: sustained bass with sustained chord (already playing bass for full measure)
                    this.playChordWithRealisticSustain(chordNotes, fullMeasureDuration, 0);
                    break;
                    
                case 'ragtime':
                    // Ragtime: sustained bass with syncopated chord pattern (8th note syncopation)
                    this.playChordWithRealisticSustain(chordNotes, 0.3, (duration * 1.5) / 1000);
                    this.playChordWithRealisticSustain(chordNotes, 0.3, (duration * 2.5) / 1000);
                    break;
                    
                default:
                    // Default: sustained bass with full chord (whole chord duration)
                    this.playChordWithRealisticSustain(chordNotes, fullMeasureDuration, 0);
            }
        },
        
        // Play chord with realistic sustain durations for each note
        playChordWithRealisticSustain(notes, baseDuration, delay) {
            setTimeout(() => {
                notes.forEach(note => {
                    const realisticDuration = this.getRealisticSustainDuration(note, baseDuration);
                    this.pianoPlayer.playNote(note, realisticDuration);
                    this.pressKey(note);
                    
                    // Release key after realistic duration
                    setTimeout(() => {
                        this.releaseKey(note);
                    }, realisticDuration * 1000);
                });
            }, delay * 1000);
        },
        
        playNoteWithVisualization(note, noteDuration, delay) {
            setTimeout(() => {
                // Play the audio
                this.pianoPlayer.playNote(note, noteDuration);
                
                // Show key press on piano
                this.pressKey(note);
                
                // Release key after note duration
                setTimeout(() => {
                    this.releaseKey(note);
                }, noteDuration * 1000);
            }, delay);
        },
        
        playChordWithVisualization(notes, chordDuration, delay, isSustained = false) {
            setTimeout(() => {
                // Play the audio
                if (isSustained) {
                    this.pianoPlayer.playChordWithSustain(notes, chordDuration);
                } else {
                    this.pianoPlayer.playChord(notes, chordDuration);
                }
                
                // Show keys press on piano
                notes.forEach(note => {
                    this.pressKey(note);
                });
                
                // Release keys after chord duration
                setTimeout(() => {
                    notes.forEach(note => {
                        this.releaseKey(note);
                    });
                }, chordDuration * 1000);
            }, delay);
        },
        
        pressKey(note) {
            // Convert flat notes to sharp equivalents for key lookup
            const flatToSharp = {
                'Db': 'C#', 'Eb': 'D#', 'Gb': 'F#', 'Ab': 'G#', 'Bb': 'A#'
            };
            
            let pianoNote = note;
            for (const [flat, sharp] of Object.entries(flatToSharp)) {
                pianoNote = pianoNote.replace(flat, sharp);
            }
            
            // Increment reference count for this key
            const currentCount = this.pressedKeys.get(pianoNote) || 0;
            this.pressedKeys.set(pianoNote, currentCount + 1);
            
            const keyId = 'key-' + pianoNote;
            const key = document.getElementById(keyId);
            if (key) {
                key.classList.add('pressed', 'active');
                // Add visual animation
                if (!key.style.transform) {
                    key.style.transform = key.classList.contains('black-key') ? 'translateY(2px)' : 'translateY(3px)';
                    key.style.transition = 'transform 0.1s ease';
                }
            }
            
            console.log(`Pressed key ${pianoNote}, count: ${this.pressedKeys.get(pianoNote)}`);
        },
        
        releaseKey(note) {
            // Convert flat notes to sharp equivalents for key lookup
            const flatToSharp = {
                'Db': 'C#', 'Eb': 'D#', 'Gb': 'F#', 'Ab': 'G#', 'Bb': 'A#'
            };
            
            let pianoNote = note;
            for (const [flat, sharp] of Object.entries(flatToSharp)) {
                pianoNote = pianoNote.replace(flat, sharp);
            }
            
            // Decrement reference count for this key
            const currentCount = this.pressedKeys.get(pianoNote) || 0;
            if (currentCount > 1) {
                // Still other notes holding this key
                this.pressedKeys.set(pianoNote, currentCount - 1);
                console.log(`Released key ${pianoNote}, still held by ${currentCount - 1} notes`);
                return; // Don't release the visual key yet
            } else if (currentCount === 1) {
                // Last reference, actually release the key
                this.pressedKeys.delete(pianoNote);
                console.log(`Final release of key ${pianoNote}`);
            }
            
            const keyId = 'key-' + pianoNote;
            const key = document.getElementById(keyId);
            if (key) {
                key.classList.remove('pressed', 'active');
                key.style.transform = '';
            }
        }
    }));
});
</script>