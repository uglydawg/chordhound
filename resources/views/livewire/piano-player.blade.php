<div class="piano-player space-y-4">
    {{-- Piano Display --}}
    <div class="bg-zinc-950 rounded-lg p-4 shadow-inner">

        {{-- Full Piano Layout (C1 - C5) --}}
        <div class="bg-zinc-900 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="inline-flex items-center gap-2">
                    <span class="text-sm text-zinc-400">Now Playing:</span>
                    @if(!empty($currentChord['tone']))
                        <span class="text-lg font-bold text-white">
                            {{ $currentChord['tone'] }}{{ $currentChord['semitone'] === 'minor' ? 'm' : ($currentChord['semitone'] === 'diminished' ? 'dim' : '') }}
                            @if($currentChord['inversion'] !== 'root')
                                <span class="text-sm text-zinc-400">/ {{ ucfirst($currentChord['inversion']) }} inv.</span>
                            @endif
                        </span>
                    @else
                        <span class="text-lg font-medium text-zinc-500">No chord selected</span>
                    @endif
                </div>
            </div>

            {{-- Piano Keyboard - Proper Layout --}}
            <div class="piano-container bg-zinc-800 rounded-lg p-4 overflow-x-auto" id="piano-keyboard">
                <div class="piano-keys relative" style="height: 120px; width: 100%; min-width: 600px;">
                    {{-- White Keys Container --}}
                    <div class="white-keys-container absolute inset-0 flex">
                        @php
                            $whiteKeyPattern = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
                            $octaves = [2, 3, 4];
                            $totalWhiteKeys = count($octaves) * count($whiteKeyPattern);
                            $whiteKeyIndex = 0;
                        @endphp

                        @foreach($octaves as $octave)
                            @foreach($whiteKeyPattern as $note)
                                @php
                                    $noteWithOctave = $note . $octave;
                                    $isC = $note === 'C';
                                    $isActive = isset($activeNotes) && in_array($noteWithOctave, $activeNotes);
                                    $whiteKeyIndex++;
                                @endphp
                                <button
                                    class="piano-key white-key bg-white hover:bg-gray-100 border-r border-gray-300 transition-all duration-100 {{ $isActive ? 'pressed' : '' }}"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="flex: 1; height: 100%; border-radius: 0 0 4px 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                >
                                    <div class="h-full flex flex-col justify-between items-center p-1">
                                        @if($isC)
                                            <span class="octave-marker text-blue-600 font-bold text-xs">C{{ $octave }}</span>
                                        @endif
                                        <div class="flex-1"></div>
                                        @if($showLabels || $isC)
                                            <span class="key-label text-xs text-gray-600 font-medium">{{ $noteWithOctave }}</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        @endforeach
                    </div>

                    {{-- Black Keys Container --}}
                    <div class="black-keys-container absolute top-0 w-full h-3/5 pointer-events-none">
                        @php
                            $blackKeyPattern = ['C#', 'D#', null, 'F#', 'G#', 'A#', null];
                            // Calculate positions based on white key width
                            $whiteKeyWidth = 100 / $totalWhiteKeys; // percentage width of each white key
                        @endphp

                        @foreach($octaves as $octaveIndex => $octave)
                            @foreach($blackKeyPattern as $positionInOctave => $note)
                                @if($note)
                                    @php
                                        $noteWithOctave = $note . $octave;
                                        $isActive = isset($activeNotes) && in_array($noteWithOctave, $activeNotes);
                                        
                                        // Calculate position based on which white keys this black key sits between
                                        $basePosition = ($octaveIndex * 7 + $positionInOctave) * $whiteKeyWidth;
                                        
                                        // Fine-tune positions for better visual alignment
                                        $adjustments = [
                                            0 => 0.7,  // C# - between C and D
                                            1 => 0.3,  // D# - between D and E  
                                            3 => 0.7,  // F# - between F and G
                                            4 => 0,    // G# - between G and A
                                            5 => 0.3   // A# - between A and B
                                        ];
                                        
                                        $leftPercentage = $basePosition + ($adjustments[$positionInOctave] * $whiteKeyWidth);
                                    @endphp
                                    <button
                                        class="piano-key black-key absolute bg-black hover:bg-gray-800 transition-all duration-100 pointer-events-auto z-10 {{ $isActive ? 'pressed' : '' }}"
                                        data-note="{{ $noteWithOctave }}"
                                        id="key-{{ $noteWithOctave }}"
                                        style="width: {{ $whiteKeyWidth * 0.6 }}%; height: 100%; left: {{ $leftPercentage }}%; border-radius: 0 0 3px 3px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);"
                                    >
                                        @if($showLabels)
                                            <div class="h-full flex items-end justify-center pb-1">
                                                <span class="key-label text-xs text-gray-300 font-medium">{{ $noteWithOctave }}</span>
                                            </div>
                                        @endif
                                    </button>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Audio Button --}}
    <div class="mb-4 text-center">
        <button
            onclick="testAudio()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
        >
            Test Piano Sound
        </button>
    </div>

    {{-- Transport Controls --}}
    <div class="flex items-center justify-between space-x-4 bg-zinc-900 border border-zinc-800 rounded-lg p-4">
        {{-- Play/Pause/Stop Controls --}}
        <div class="flex items-center space-x-2">
            {{-- Play/Pause Button --}}
            <button
                wire:click="togglePlayback"
                class="transport-button group flex items-center justify-center"
                title="{{ $isPlaying ? 'Pause' : 'Play' }}"
            >
                @if($isPlaying)
                    <svg class="w-6 h-6 text-secondary group-hover:text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <rect x="6" y="4" width="4" height="16" />
                        <rect x="14" y="4" width="4" height="16" />
                    </svg>
                @else
                    <svg class="w-6 h-6 text-secondary group-hover:text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                @endif
            </button>

            {{-- Stop Button --}}
            <button
                wire:click="stop"
                class="transport-button group flex items-center justify-center"
                title="Stop"
            >
                <svg class="w-6 h-6 text-secondary group-hover:text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="6" y="6" width="12" height="12" />
                </svg>
            </button>
        </div>

        {{-- Timeline Progress --}}
        <div class="flex-1 max-w-md">
            <div class="relative h-2 bg-zinc-800 rounded-full overflow-hidden">
                <div
                    class="absolute h-full bg-blue-500 transition-all duration-100"
                    style="width: {{ ($currentTime / $duration) * 100 }}%"
                ></div>
            </div>
        </div>

        {{-- Controls Group --}}
        <div class="flex items-center space-x-4">
            {{-- Labels Toggle --}}
            <button
                wire:click="toggleLabels"
                class="transport-button group flex items-center space-x-1 {{ $showLabels ? 'bg-blue-600' : '' }}"
                title="{{ $showLabels ? 'Hide Labels' : 'Show Labels' }}"
            >
                <svg class="w-4 h-4 text-secondary group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2.001 2.001 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-xs text-secondary group-hover:text-primary">Labels</span>
            </button>

            {{-- Tempo Control --}}
            <div class="flex items-center space-x-2">
                <label class="text-sm text-secondary">BPM</label>
                <input
                    type="number"
                    wire:model.lazy="tempo"
                    wire:change="updateTempo($event.target.value)"
                    min="60"
                    max="200"
                    class="w-16 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-sm text-primary focus:border-blue-500 focus:outline-none"
                >
            </div>

            {{-- Piano Sound Selector --}}
            <div class="flex items-center space-x-2">
                <span class="text-sm text-secondary">Sound</span>
                <select
                    wire:model.live="selectedSound"
                    wire:change="updateSound($event.target.value)"
                    class="bg-zinc-800 border border-zinc-700 rounded px-3 py-1 text-sm text-primary focus:border-blue-500 focus:outline-none"
                >
                    @foreach($availableSounds as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                            @if(str_contains($label, '(Sample)'))
                                🎵
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <style>
/* Piano key styling */
.white-key {
    background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%);
    border: 1px solid #d1d5db;
    cursor: pointer;
    transition: all 0.1s ease;
}

.white-key:hover {
    background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
    transform: translateY(1px);
}

.white-key.pressed,
.white-key:active {
    background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
    transform: translateY(3px);
    box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
}

.white-key.pressed .key-label,
.white-key.pressed .octave-marker {
    color: white !important;
    font-weight: 600;
}

.black-key {
    background: linear-gradient(180deg, #374151 0%, #1f2937 100%);
    border: 1px solid #111827;
    cursor: pointer;
    transition: all 0.1s ease;
}

.black-key:hover {
    background: linear-gradient(180deg, #4b5563 0%, #374151 100%);
    transform: translateY(1px);
}

.black-key.pressed,
.black-key:active {
    background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
    transform: translateY(2px);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.5);
}

.black-key.pressed .key-label {
    color: white !important;
    font-weight: 600;
}

/* Animation for key press */
@keyframes pianoKeyPress {
    0% { transform: translateY(0); }
    50% { transform: translateY(3px); }
    100% { transform: translateY(0); }
}

.piano-key.active {
    animation: pianoKeyPress 0.2s ease-out;
}

/* Piano container styling */
.piano-container {
    background: linear-gradient(145deg, #1f2937, #374151);
    border: 2px solid #4b5563;
}

.piano-keys {
    background: #000;
    border-radius: 4px;
    padding: 2px;
}
</style>

{{-- Load the multi-instrument player --}}
<script src="{{ asset('js/multi-instrument-player.js') }}"></script>

{{-- JavaScript for audio playback --}}
<script>
// Audio context and sample management
let audioContext = null;
let pianoPlayer = null;
let isAudioLoaded = false;

// Global test function
window.testAudio = async function() {
    try {
        // Initialize piano player if not already done
        if (!pianoPlayer && typeof MultiInstrumentPlayer !== 'undefined') {
            pianoPlayer = new MultiInstrumentPlayer();
            window.pianoPlayer = pianoPlayer; // Make globally accessible
            isAudioLoaded = true;
        }

        // Resume audio context if suspended
        if (pianoPlayer && pianoPlayer.audioContext && pianoPlayer.audioContext.state === 'suspended') {
            await pianoPlayer.audioContext.resume();
        }

        // Test with piano player
        if (pianoPlayer && pianoPlayer.isLoaded) {
            pianoPlayer.playNote('C4', 0.5);
            console.log('Test sound played using MultiInstrumentPlayer');
        } else {
            alert('Audio not loaded. Please wait a moment and try again.');
        }
    } catch (error) {
        alert('Audio error: ' + error.message);
        console.error('Audio test failed:', error);
    }
};

document.addEventListener('livewire:initialized', () => {
    let sequence = null;
    let currentSound = 'piano';
    let audioInitialized = false;

    // Initialize audio with MultiInstrumentPlayer
    async function initializeAudio() {
        if (!audioInitialized) {
            try {
                // Initialize multi-instrument player if available
                if (typeof MultiInstrumentPlayer !== 'undefined' && !pianoPlayer) {
                    pianoPlayer = new MultiInstrumentPlayer();
                    window.pianoPlayer = pianoPlayer; // Make it globally accessible
                    console.log('Multi-instrument player initialized');
                }

                // Resume audio context if suspended
                if (pianoPlayer && pianoPlayer.audioContext && pianoPlayer.audioContext.state === 'suspended') {
                    await pianoPlayer.audioContext.resume();
                    console.log('Audio context resumed');
                }

                audioInitialized = true;
                isAudioLoaded = true;
                console.log('Audio initialized successfully with MultiInstrumentPlayer');
            } catch (error) {
                console.error('Failed to initialize audio:', error);
            }
        }
    }

    // Initialize on first user interaction
    document.addEventListener('click', async () => {
        await initializeAudio();
    }, { once: true });
    document.addEventListener('keydown', async () => {
        await initializeAudio();
    }, { once: true });

    // Listen for playback events
    Livewire.on('toggle-playback', async ({ isPlaying }) => {
        await initializeAudio(); // Ensure audio is initialized
        if (!pianoPlayer) {
            console.error('Piano player not initialized');
            return;
        }

        if (isPlaying) {
            startPlayback();
        } else {
            stopPlayback();
        }
    });

    Livewire.on('stop-playback', () => {
        stopPlayback();
    });

    Livewire.on('tempo-changed', ({ tempo }) => {
        console.log('Tempo changed to:', tempo);
        // MultiInstrumentPlayer doesn't use transport, but we can note the tempo change
    });

    Livewire.on('sound-changed', async ({ sound }) => {
        currentSound = sound;

        // Update multi-instrument player to use piano
        if (pianoPlayer) {
            await pianoPlayer.switchInstrument('piano');
            console.log('Multi-instrument player using piano');

            // Play a test note to demonstrate the sound
            if (audioInitialized) {
                pianoPlayer.playNote('C4', 0.5);
            }
        }
    });
    
    // Listen for chord click events from ChordGrid
    Livewire.on('play-chord', async ({ chord }) => {
        await initializeAudio();
        if (!audioInitialized) return;
        
        console.log('Playing chord from grid click:', chord);
        
        // Get the chord notes
        const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
        
        // Play the chord using MultiInstrumentPlayer
        if (pianoPlayer && pianoPlayer.isLoaded) {
            pianoPlayer.playChordWithSustain(notes, 4.0);
            console.log('Playing chord with multi-instrument player:', notes);
        }
        
        // Update the piano player's current chord display
        @this.call('setCurrentChord', chord);
    });

    function startPlayback() {
        // Get current chords from the component
        const chords = @js($chords);
        const tempo = @this.tempo;

        // Create chord progression sequence
        const chordNotes = [];
        Object.values(chords).forEach(chord => {
            if (chord.tone) {
                const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
                chordNotes.push(notes);
            }
        });

        if (chordNotes.length > 0 && pianoPlayer) {
            let chordIndex = 0;
            const beatDuration = 60000 / tempo; // Duration of one beat in milliseconds
            const chordDuration = beatDuration * 2; // 2 beats per chord

            function playNextChord() {
                if (chordIndex < chordNotes.length) {
                    // Play current chord
                    pianoPlayer.playChordWithSustain(chordNotes[chordIndex], 2.0);

                    // Update the displayed chord
                    @this.updateCurrentChord(chordIndex);

                    chordIndex++;

                    // Update progress
                    @this.currentTime = (chordIndex * 2) % 8;

                    // Schedule next chord
                    sequence = setTimeout(playNextChord, chordDuration);
                } else {
                    // Loop back to beginning
                    chordIndex = 0;
                    sequence = setTimeout(playNextChord, 500);
                }
            }

            playNextChord();
        }
    }

    function stopPlayback() {
        if (sequence) {
            clearTimeout(sequence);
            sequence = null;
        }
        if (pianoPlayer) {
            pianoPlayer.stopAll();
        }
        @this.currentTime = 0;
    }

    function getChordNotes(root, type, inversion) {
        // Complete chord to notes conversion with all keys
        const noteMap = {
            'C': ['C4', 'E4', 'G4'],
            'C#': ['C#4', 'F4', 'G#4'],
            'D': ['D4', 'F#4', 'A4'],
            'D#': ['D#4', 'G4', 'A#4'],
            'E': ['E4', 'G#4', 'B4'],
            'F': ['F4', 'A4', 'C5'],
            'F#': ['F#4', 'A#4', 'C#5'],
            'G': ['G4', 'B4', 'D5'],
            'G#': ['G#4', 'C5', 'D#5'],
            'A': ['A4', 'C#5', 'E5'],
            'A#': ['A#4', 'D5', 'F5'],
            'B': ['B4', 'D#5', 'F#5']
        };

        let notes = noteMap[root] || ['C4', 'E4', 'G4'];

        // Apply chord type modifications
        if (type === 'minor') {
            // Lower the third by a semitone for minor chords
            const note = notes[1].replace(/([A-G]#?)([0-9])/, (match, pitch, octave) => {
                const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                let idx = noteOrder.indexOf(pitch);
                idx = (idx - 1 + 12) % 12;
                if (idx === 11 && pitch === 'C') {
                    octave = String(parseInt(octave) - 1);
                }
                return noteOrder[idx] + octave;
            });
            notes[1] = note;
        } else if (type === 'diminished') {
            // Lower both third and fifth for diminished
            notes[1] = lowerNote(notes[1]);
            notes[2] = lowerNote(notes[2]);
        } else if (type === 'augmented') {
            // Raise the fifth for augmented
            notes[2] = raiseNote(notes[2]);
        }

        // Apply inversion
        if (inversion === 'first') {
            notes.push(notes.shift());
        } else if (inversion === 'second') {
            notes.push(notes.shift());
            notes.push(notes.shift());
        }

        return notes;
    }

    // Helper functions for note manipulation
    function lowerNote(note) {
        return note.replace(/([A-G]#?)([0-9])/, (match, pitch, octave) => {
            const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
            let idx = noteOrder.indexOf(pitch);
            idx = (idx - 1 + 12) % 12;
            if (idx === 11 && pitch === 'C') {
                octave = String(parseInt(octave) - 1);
            }
            return noteOrder[idx] + octave;
        });
    }

    function raiseNote(note) {
        return note.replace(/([A-G]#?)([0-9])/, (match, pitch, octave) => {
            const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
            let idx = noteOrder.indexOf(pitch);
            idx = (idx + 1) % 12;
            if (idx === 0 && pitch === 'B') {
                octave = String(parseInt(octave) + 1);
            }
            return noteOrder[idx] + octave;
        });
    }

    // Add keyboard interaction
    document.addEventListener('keydown', async (e) => {
        // Skip if input is focused
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        await initializeAudio();
        if (!pianoPlayer || !audioInitialized) return;

        // Map keyboard keys to piano notes
        const keyMap = {
            'a': 'C3', 'w': 'C#3', 's': 'D3', 'e': 'D#3', 'd': 'E3',
            'f': 'F3', 't': 'F#3', 'g': 'G3', 'y': 'G#3', 'h': 'A3',
            'u': 'A#3', 'j': 'B3', 'k': 'C4'
        };

        const note = keyMap[e.key.toLowerCase()];
        if (note) {
            pianoPlayer.playNote(note, 0.5);
        }
    });

    // Add click handlers for piano keys
    document.addEventListener('click', async (e) => {
        const target = e.target.closest('.white-key, .black-key, .piano-key');
        if (target) {
            await initializeAudio();
            if (!audioInitialized) return;

            const note = target.getAttribute('data-note');
            if (note) {
                // Use MultiInstrumentPlayer for note playback
                if (pianoPlayer && pianoPlayer.isLoaded) {
                    pianoPlayer.playNote(note, 0.5);
                    console.log('Playing note with multi-instrument player:', note);
                }

                // Visual feedback
                target.classList.add('pressed', 'active');
                setTimeout(() => {
                    target.classList.remove('pressed', 'active');
                }, 200);
            }
        }
    });
});
</script>
</div> {{-- Close the root piano-player div --}}