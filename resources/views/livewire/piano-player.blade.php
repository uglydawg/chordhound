<div class="piano-player">
    {{-- Play/Pause Button - Moved to top --}}
    <div class="flex items-center justify-between mb-4 px-6">
        <div class="flex items-center gap-4">
            <button
                wire:click="togglePlayback"
                class="flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
                aria-label="{{ $isPlaying ? 'Pause' : 'Play' }}"
            >
                @if($isPlaying)
                    {{-- Pause Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    {{-- Play Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </button>
            
            {{-- Tempo Control --}}
            <div class="flex items-center gap-2">
                <button
                    wire:click="decrementTempo"
                    class="w-8 h-8 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900 flex items-center justify-center"
                    aria-label="Decrease tempo"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                
                <div class="flex items-center gap-1 min-w-[80px] justify-center">
                    <input
                        type="number"
                        wire:model.live="tempo"
                        wire:change="updateTempo($event.target.value)"
                        min="60"
                        max="200"
                        step="5"
                        class="w-14 px-2 py-1 bg-zinc-800 border border-zinc-700 text-white rounded text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        aria-label="Tempo in BPM"
                    />
                    <span class="text-zinc-400 text-sm">BPM</span>
                </div>
                
                <button
                    wire:click="incrementTempo"
                    class="w-8 h-8 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900 flex items-center justify-center"
                    aria-label="Increase tempo"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
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
    </div>

    {{-- Piano Display --}}
    <div class="bg-zinc-950 rounded-lg shadow-inner">


        {{-- Full Piano Layout (C1 - C5) --}}
        <div class="bg-zinc-900 rounded-lg">

            {{-- Piano Keyboard - Realistic Layout --}}
            <div class="piano-container bg-zinc-800 rounded-lg overflow-x-auto" id="piano-keyboard">
                <div class="piano-keys relative" style="height: 150px; width: 100%; min-width: 600px; max-width: 900px; margin: 0 auto;">
                    {{-- White Keys Container (Full height, will be overlapped by black keys) --}}
                    <div class="white-keys-container absolute inset-0 flex gap-0">
                        @php
                            $whiteKeyPattern = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
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
                                    class="piano-key white-key relative {{ $isActive ? 'pressed active' : '' }}"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="flex: 1; height: 100%; position: relative;"
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

                    {{-- Black Keys Container (Overlaps white keys) --}}
                    <div class="black-keys-container absolute top-0 w-full pointer-events-none" style="height: 70%;">
                        @php
                            // Calculate positions based on white key width
                            $whiteKeyWidth = 100 / $totalWhiteKeys; // percentage width of each white key
                            $blackKeyWidth = $whiteKeyWidth * 0.65; // Black keys are 65% width of white keys for better visibility
                            
                            // Black keys with their positions relative to white keys
                            // The number represents which white key they come after (0-based)
                            $blackKeys = [
                                ['note' => 'C#', 'afterWhiteKey' => 0],  // After C
                                ['note' => 'D#', 'afterWhiteKey' => 1],  // After D
                                ['note' => 'F#', 'afterWhiteKey' => 3],  // After F
                                ['note' => 'G#', 'afterWhiteKey' => 4],  // After G
                                ['note' => 'A#', 'afterWhiteKey' => 5],  // After A
                            ];
                        @endphp

                        @foreach($octaves as $octaveIndex => $octave)
                            @foreach($blackKeys as $blackKey)
                                @php
                                    $note = $blackKey['note'];
                                    $noteWithOctave = $note . $octave;
                                    $isActive = isset($activeNotes) && in_array($noteWithOctave, $activeNotes);
                                    
                                    // Calculate which white key this comes after in the full keyboard
                                    $whiteKeyIndex = ($octaveIndex * 7) + $blackKey['afterWhiteKey'];
                                    
                                    // Position the black key between this white key and the next
                                    // Black keys are positioned at the right edge of their "after" white key
                                    $leftPosition = ($whiteKeyIndex + 1) * $whiteKeyWidth - ($blackKeyWidth * 0.5);
                                @endphp
                                <button
                                    class="piano-key black-key absolute {{ $isActive ? 'pressed active' : '' }}"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="width: {{ $blackKeyWidth }}%; height: 100%; left: {{ $leftPosition }}%; z-index: 20;"
                                >
                                        @if($showLabels)
                                            <div class="h-full flex items-end justify-center pb-1">
                                                <span class="key-label text-xs text-gray-300 font-medium">{{ $noteWithOctave }}</span>
                                            </div>
                                        @endif
                                    </button>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
/* Piano key styling to match reference */
.piano-keys {
    display: flex;
    position: relative;
    background: transparent;
}

.white-key {
    background: white;
    border: 1px solid #000;
    border-radius: 0 0 4px 4px;
    cursor: pointer;
    transition: background-color 0.1s ease;
    position: relative;
    margin-right: 1px;
}

.white-key:hover {
    background: #f0f0f0;
}

.white-key.pressed,
.white-key.active {
    background: #60a5fa !important; /* Light blue like in reference */
}

.white-key.pressed .key-label,
.white-key.active .key-label,
.white-key.pressed .octave-marker,
.white-key.active .octave-marker {
    color: #1e40af !important; /* Dark blue text */
}

.black-key {
    background: #000;
    border: none;
    border-radius: 0 0 4px 4px;
    cursor: pointer;
    transition: background-color 0.1s ease;
    position: absolute;
    top: 0;
}

.black-key:hover {
    background: #333;
}

.black-key.pressed,
.black-key.active {
    background: #3b82f6 !important; /* Darker blue for black keys */
}

.black-key.pressed .key-label,
.black-key.active .key-label {
    color: white !important;
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
    background: #1f2937;
    padding: 8px;
    border-radius: 8px;
}

.white-keys-container {
    display: flex;
    gap: 0;
    height: 100%;
}
</style>

{{-- Multi-instrument player loaded in head --}}

{{-- JavaScript for audio playback --}}
<script>
// Audio context and sample management
let audioContext = null;
let pianoPlayer = null;
let isAudioLoaded = false;


// Initialize audio immediately on page load
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Initialize multi-instrument player immediately
        if (typeof MultiInstrumentPlayer !== 'undefined' && !pianoPlayer) {
            pianoPlayer = new MultiInstrumentPlayer();
            window.pianoPlayer = pianoPlayer; // Make it globally accessible
            console.log('Multi-instrument player initialized on page load');
            
            // Wait a bit for the player to initialize
            setTimeout(() => {
                if (pianoPlayer && pianoPlayer.isLoaded) {
                    console.log('Piano samples loaded successfully');
                    isAudioLoaded = true;
                } else {
                    console.log('Piano samples still loading...');
                }
            }, 1000);
        }
    } catch (error) {
        console.error('Failed to initialize audio on page load:', error);
    }
});

// Make initializeAudio globally accessible
window.initializeAudio = null;

document.addEventListener('livewire:initialized', () => {
    let sequence = null;
    let currentSound = 'piano';
    let audioInitialized = false;
    
    // Request chord state from chord grid when piano player loads
    Livewire.dispatch('request-chord-state');
    
    // Listen for chord updates
    Livewire.on('chordsUpdated', (event) => {
        console.log('JavaScript received chordsUpdated event:', event);
        if (event && event.chords) {
            console.log('Chords from event:', event.chords);
        }
    });

    // Initialize/resume audio context
    async function initializeAudio() {
        if (!audioInitialized || (pianoPlayer && pianoPlayer.audioContext && pianoPlayer.audioContext.state === 'suspended')) {
            try {
                // Initialize if not already done
                if (typeof MultiInstrumentPlayer !== 'undefined' && !pianoPlayer) {
                    pianoPlayer = new MultiInstrumentPlayer();
                    window.pianoPlayer = pianoPlayer;
                    console.log('Multi-instrument player initialized');
                }

                // Resume audio context if suspended
                if (pianoPlayer && pianoPlayer.audioContext && pianoPlayer.audioContext.state === 'suspended') {
                    await pianoPlayer.audioContext.resume();
                    console.log('Audio context resumed');
                }

                audioInitialized = true;
                isAudioLoaded = true;
                console.log('Audio ready for playback');
            } catch (error) {
                console.error('Failed to initialize audio:', error);
            }
        }
    }

    // Make initializeAudio available globally
    window.initializeAudio = initializeAudio;

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
            alert('Audio not initialized. Please click anywhere on the page first, then try playing again.');
            return;
        }

        if (!pianoPlayer.isLoaded) {
            console.error('Piano samples not loaded yet');
            alert('Piano samples are still loading. Please wait a moment and try again.');
            return;
        }

        if (isPlaying) {
            console.log('Starting playback...');
            startPlayback().catch(err => console.error('Error starting playback:', err));
        } else {
            console.log('Pausing playback...');
            stopPlayback();
        }
    });

    Livewire.on('stop-playback', () => {
        stopPlayback();
        // Clear the highlight in chord grid
        Livewire.dispatch('stop-playback');
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
        
        // Stop any currently playing notes and play the new chord for 1.5 seconds
        if (pianoPlayer && pianoPlayer.isLoaded) {
            pianoPlayer.stopAll(); // Stop previous chord
            setTimeout(() => {
                pianoPlayer.playChord(notes, 1.5); // Play chord for 1.5 seconds
                console.log('Playing chord for 1.5 seconds:', notes);
                // Update the piano display with the correct notes
                updateActiveKeys(notes);
            }, 50); // Brief delay for smooth transition
        }
        
        // Update the piano player's current chord display (for UI text only, not piano keys)
        @this.call('setCurrentChord', chord);
    });

    async function startPlayback() {
        // Get current chords from the Livewire component dynamically
        const chords = await @this.get('chords');
        const tempo = await @this.get('tempo');

        console.log('Starting playback with chords:', chords);
        console.log('Chords object keys:', Object.keys(chords));
        console.log('Chords values:', Object.values(chords));
        console.log('Tempo:', tempo);

        // Create chord progression sequence
        const chordNotes = [];
        const chordData = [];
        Object.values(chords).forEach(chord => {
            if (chord.tone) {
                const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
                chordNotes.push(notes);
                chordData.push(chord);
            }
        });

        console.log('Chord notes to play:', chordNotes);
        console.log('Piano player status:', pianoPlayer ? pianoPlayer.getStatus() : 'Not initialized');

        if (chordNotes.length > 0 && pianoPlayer) {
            let chordIndex = 0;
            let beatCount = 0;
            const beatDuration = 60000 / tempo; // Duration of one beat in milliseconds

            function playNextBeat() {
                const currentChordNotes = chordNotes[chordIndex];
                const beat = beatCount % 4; // 4 beats per measure
                const measureBeat = beat + 1; // 1-4 for display
                
                console.log(`Measure ${chordIndex + 1}, Beat ${measureBeat}:`, currentChordNotes);

                // Extract root note name for bass
                const rootNote = currentChordNotes[0];
                const noteMatch = rootNote.match(/([A-G]#?)(\d+)/);
                
                if (noteMatch) {
                    const [, noteName] = noteMatch;
                    
                    // Transpose all chord notes to C4 octave (right hand)
                    const chordInC4 = currentChordNotes.map(note => {
                        const chordNoteMatch = note.match(/([A-G]#?)(\d+)/);
                        if (chordNoteMatch) {
                            const [, chordNoteName] = chordNoteMatch;
                            return chordNoteName + '4'; // Right hand in octave 4
                        }
                        return note;
                    });

                    if (beat === 0) {
                        // Beat 1 of measure: Play bass note (left hand) + chord (right hand)
                        const bassNote = noteName + '2'; // Left hand bass in C2
                        console.log(`Beat 1: Bass ${bassNote} + Chord ${chordInC4.join(',')}`);
                        
                        // Play bass note (sustains for whole measure)
                        pianoPlayer.playNote(bassNote, 4.0); // Sustain for full measure (4 beats)
                        
                        // Play chord on right hand
                        pianoPlayer.playChord(chordInC4, 0.8); // Shorter chord hit
                        
                        // Show both bass and chord on piano
                        console.log(`DEBUG: Setting active keys:`, [bassNote, ...chordInC4]);
                        updateActiveKeys([bassNote, ...chordInC4]);
                        
                    } else {
                        // Beats 2, 3, 4: Play only chord (right hand), bass continues sustaining
                        console.log(`Beat ${measureBeat}: Chord ${chordInC4.join(',')}`);
                        
                        // Play chord hit on right hand
                        pianoPlayer.playChord(chordInC4, 0.8);
                        
                        // Show chord + sustained bass note from beat 1
                        const bassNote = noteName + '2';
                        console.log(`DEBUG: Setting active keys (sustained bass):`, [bassNote, ...chordInC4]);
                        updateActiveKeys([bassNote, ...chordInC4]);
                    }
                }

                // Update the displayed chord info
                @this.call('updateCurrentChord', chordIndex);
                
                // Highlight the chord in the grid
                const chordPosition = chordData[chordIndex].position;
                Livewire.dispatch('highlight-chord-position', { position: chordPosition });

                beatCount++;

                // Move to next chord after 4 beats (end of measure)
                if (beatCount % 4 === 0) {
                    chordIndex++;
                    
                    // Loop back to beginning if we've reached the end
                    if (chordIndex >= chordNotes.length) {
                        console.log('Looping back to start of progression');
                        chordIndex = 0;
                    }
                }

                // Update progress (beat-based)
                @this.set('currentTime', beatCount % 16);

                // Schedule next beat
                sequence = setTimeout(playNextBeat, beatDuration);
            }

            playNextBeat();
        } else {
            console.error('Cannot start playback:', {
                hasChords: chordNotes.length > 0,
                hasPianoPlayer: !!pianoPlayer
            });
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
        @this.set('currentTime', 0);
        
        // Clear all active keys
        document.querySelectorAll('.piano-key.active').forEach(key => {
            key.classList.remove('active', 'pressed');
        });
    }
    
    function updateActiveKeys(notes) {
        // Clear all active keys
        document.querySelectorAll('.piano-key.active').forEach(key => {
            key.classList.remove('active', 'pressed');
        });
        
        console.log(`DEBUG updateActiveKeys: Looking for notes:`, notes);
        
        // Convert flat notes to sharp equivalents for piano key lookup
        const flatToSharp = {
            'Db': 'C#', 'Eb': 'D#', 'Gb': 'F#', 'Ab': 'G#', 'Bb': 'A#'
        };
        
        // Highlight the new chord notes
        notes.forEach(note => {
            // Convert flats to sharps if needed
            let pianoNote = note;
            for (const [flat, sharp] of Object.entries(flatToSharp)) {
                pianoNote = pianoNote.replace(flat, sharp);
            }
            
            const keyId = 'key-' + pianoNote;
            const key = document.getElementById(keyId);
            console.log(`DEBUG: Looking for key ID '${keyId}' (original: ${note}), found:`, !!key);
            if (key) {
                key.classList.add('active', 'pressed');
                console.log(`DEBUG: Activated key ${pianoNote}`);
            } else {
                console.warn(`DEBUG: Key not found for note ${note} (ID: ${keyId})`);
            }
        });
    }

    function getChordNotes(root, type, inversion) {
        // Exact chord voicings matching the provided specification
        const chordVoicings = {
            'C': {
                'root': ['C4', 'E4', 'G4'],
                'first': ['E4', 'G4', 'C5'],
                'second': ['G3', 'C4', 'E4']
            },
            'C#': {
                'root': ['C#4', 'F4', 'G#4'],
                'first': ['F3', 'G#3', 'C#4'],
                'second': ['G#3', 'C#4', 'F4']
            },
            'D': {
                'root': ['D4', 'F#4', 'A4'],
                'first': ['F#3', 'A3', 'D4'],
                'second': ['A3', 'D4', 'F#4']
            },
            'D#': {
                'root': ['D#4', 'G4', 'A#4'],
                'first': ['G3', 'A#3', 'D#4'],
                'second': ['A#3', 'D#4', 'G4']
            },
            'E': {
                'root': ['E4', 'G#4', 'B4'],
                'first': ['G#3', 'B3', 'E4'],
                'second': ['B3', 'E4', 'G#4']
            },
            'F': {
                'root': ['F4', 'A4', 'C5'],
                'first': ['A3', 'C4', 'F4'],
                'second': ['C4', 'F4', 'A4']
            },
            'F#': {
                'root': ['F#3', 'A#3', 'C#4'],
                'first': ['A#3', 'C#4', 'F#4'],
                'second': ['C#4', 'F#4', 'A#4']
            },
            'G': {
                'root': ['G3', 'B3', 'D4'],
                'first': ['B3', 'D4', 'G4'],
                'second': ['D4', 'G4', 'B4']
            },
            'G#': {
                'root': ['G#3', 'C4', 'D#4'],
                'first': ['C4', 'D#4', 'G#4'],
                'second': ['D#4', 'G#4', 'C5']
            },
            'A': {
                'root': ['A3', 'C#4', 'E4'],
                'first': ['C#4', 'E4', 'A4'],
                'second': ['E3', 'A3', 'C#4']
            },
            'A#': {
                'root': ['A#3', 'D4', 'F4'],
                'first': ['D4', 'F4', 'A#4'],
                'second': ['F3', 'A#3', 'D4']
            },
            'B': {
                'root': ['B3', 'D#4', 'F#4'],
                'first': ['D#4', 'F#4', 'B4'],
                'second': ['F#3', 'B3', 'D#4']
            }
        };

        // For minor chords, use exact minor voicings
        if (type === 'minor') {
            const minorChordVoicings = {
                'C': {
                    'root': ['C4', 'Eb4', 'G4'],
                    'first': ['Eb4', 'G4', 'C5'],
                    'second': ['G3', 'C4', 'Eb4']
                },
                'C#': {
                    'root': ['C#4', 'E4', 'G#4'],
                    'first': ['E3', 'G#3', 'C#4'],
                    'second': ['G#3', 'C#4', 'E4']
                },
                'D': {
                    'root': ['D4', 'F4', 'A4'],
                    'first': ['F3', 'A3', 'D4'],
                    'second': ['A3', 'D4', 'F4']
                },
                'D#': {
                    'root': ['D#4', 'F#4', 'A#4'],
                    'first': ['F#3', 'A#3', 'D#4'],
                    'second': ['A#3', 'D#4', 'F#4']
                },
                'E': {
                    'root': ['E4', 'G4', 'B4'],
                    'first': ['G3', 'B3', 'E4'],
                    'second': ['B3', 'E4', 'G4']
                },
                'F': {
                    'root': ['F4', 'Ab4', 'C5'],
                    'first': ['Ab3', 'C4', 'F4'],
                    'second': ['C4', 'F4', 'Ab4']
                },
                'F#': {
                    'root': ['F#3', 'A3', 'C#4'],
                    'first': ['A3', 'C#4', 'F#4'],
                    'second': ['C#4', 'F#4', 'A4']
                },
                'G': {
                    'root': ['G3', 'Bb3', 'D4'],
                    'first': ['Bb3', 'D4', 'G4'],
                    'second': ['D4', 'G4', 'Bb4']
                },
                'G#': {
                    'root': ['G#3', 'B3', 'D#4'],
                    'first': ['B3', 'D#4', 'G#4'],
                    'second': ['D#4', 'G#4', 'B4']
                },
                'A': {
                    'root': ['A3', 'C4', 'E4'],
                    'first': ['C4', 'E4', 'A4'],
                    'second': ['E3', 'A3', 'C4']
                },
                'A#': {
                    'root': ['A#3', 'C#4', 'F4'],
                    'first': ['C#4', 'F4', 'A#4'],
                    'second': ['F3', 'A#3', 'C#4']
                },
                'B': {
                    'root': ['B3', 'D4', 'F#4'],
                    'first': ['D4', 'F#4', 'B4'],
                    'second': ['F#3', 'B3', 'D4']
                }
            };
            
            return minorChordVoicings[root]?.[inversion] || chordVoicings['C']['root'];
        }
        
        // Get the exact voicing for major chords
        let notes = chordVoicings[root]?.[inversion] || chordVoicings['C']['root'];

        // Handle diminished and augmented chords
        if (type === 'diminished') {
            // Lower both third and fifth for diminished
            notes = notes.map((note, index) => {
                // In root position: 0=root, 1=third, 2=fifth
                // In first inversion: 0=third, 1=fifth, 2=root
                // In second inversion: 0=fifth, 1=root, 2=third
                if (inversion === 'root' && (index === 1 || index === 2)) {
                    return lowerNote(note);
                } else if (inversion === 'first' && (index === 0 || index === 1)) {
                    return lowerNote(note);
                } else if (inversion === 'second' && (index === 0 || index === 2)) {
                    return lowerNote(note);
                }
                return note;
            });
        } else if (type === 'augmented') {
            // Raise the fifth for augmented
            notes = notes.map((note, index) => {
                if (inversion === 'root' && index === 2) {
                    return raiseNote(note);
                } else if (inversion === 'first' && index === 1) {
                    return raiseNote(note);
                } else if (inversion === 'second' && index === 0) {
                    return raiseNote(note);
                }
                return note;
            });
        }

        return notes;
    }

    // Helper functions to identify chord intervals
    function getThirdNote(root) {
        const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const rootIndex = noteOrder.indexOf(root);
        const thirdIndex = (rootIndex + 4) % 12; // Major third is 4 semitones up
        return noteOrder[thirdIndex];
    }

    function getFifthNote(root) {
        const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const rootIndex = noteOrder.indexOf(root);
        const fifthIndex = (rootIndex + 7) % 12; // Perfect fifth is 7 semitones up
        return noteOrder[fifthIndex];
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

        // Map keyboard keys to piano notes - using octave 4 for consistency
        const keyMap = {
            'a': 'C4', 'w': 'C#4', 's': 'D4', 'e': 'D#4', 'd': 'E4',
            'f': 'F4', 't': 'F#4', 'g': 'G4', 'y': 'G#4', 'h': 'A4',
            'u': 'A#4', 'j': 'B4', 'k': 'C5'
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