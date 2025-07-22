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
            
            {{-- Piano Keyboard - ChordChord Style --}}
            <div class="full-piano-container bg-zinc-800 rounded-lg p-4 overflow-x-auto" id="piano-keyboard">
                <div class="full-piano-keys relative" style="height: 120px; min-width: 1000px;">
                    {{-- White Keys Container --}}
                    <div class="all-white-keys flex h-full relative">
                    
                        {{-- Generate white keys --}}
                        @php
                            $whiteKeyPattern = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
                            $octaves = [1, 2, 3, 4, 5];
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
                                    class="piano-key full-piano-white flex-1 relative bg-white hover:bg-gray-100 active:bg-blue-400 transition-all duration-100 border border-gray-300 rounded-b cursor-pointer mr-1 {{ $isActive ? 'pressed' : '' }} {{ $isC ? 'c-key' : '' }}"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                >
                                    @if($showLabels || $isC)
                                        <span class="key-label absolute bottom-2 left-1/2 transform -translate-x-1/2 text-xs text-gray-600 font-medium">{{ $noteWithOctave }}</span>
                                    @endif
                                    @if($isC)
                                        <span class="octave-marker absolute top-2 left-1/2 transform -translate-x-1/2 text-blue-600 font-bold text-sm">C{{ $octave }}</span>
                                    @endif
                                </button>
                            @endforeach
                        @endforeach
                    </div>
                    
                    {{-- Black Keys Container --}}
                    <div class="all-black-keys absolute top-0 w-full h-3/5 pointer-events-none">
                        @php
                            $blackKeyPattern = ['C#', 'D#', null, 'F#', 'G#', 'A#', null];
                            $blackKeyOffsets = [
                                0 => 0.65,  // C# - closer to C
                                1 => 1.35,  // D# - closer to E
                                3 => 3.65,  // F# - closer to F
                                4 => 4.5,   // G# - centered
                                5 => 5.35   // A# - closer to B
                            ];
                            $totalWhiteKeys = count($octaves) * count($whiteKeyPattern);
                        @endphp
                        
                        @foreach($octaves as $octaveIndex => $octave)
                            @foreach($blackKeyPattern as $positionInOctave => $note)
                                @if($note)
                                    @php
                                        $noteWithOctave = $note . $octave;
                                        $isActive = isset($activeNotes) && in_array($noteWithOctave, $activeNotes);
                                        $keyPosition = ($octaveIndex * 7) + ($blackKeyOffsets[$positionInOctave] ?? 0);
                                        $leftPercentage = ($keyPosition / $totalWhiteKeys) * 100;
                                    @endphp
                                    <button 
                                        class="piano-key full-piano-black absolute bg-black hover:bg-gray-800 active:bg-blue-600 transition-all duration-100 border border-gray-800 rounded-b cursor-pointer pointer-events-auto z-10 {{ $isActive ? 'pressed' : '' }}"
                                        data-note="{{ $noteWithOctave }}"
                                        id="key-{{ $noteWithOctave }}"
                                        style="width: 1.8%; height: 100%; left: {{ $leftPercentage }}%; transform: translateX(-50%); box-shadow: 0 4px 8px rgba(0,0,0,0.3), inset 0 1px 2px rgba(255,255,255,0.1);"
                                    >
                                        @if($showLabels)
                                            <span class="key-label absolute bottom-1 left-1/2 transform -translate-x-1/2 text-xs text-gray-300 font-medium" style="font-size: 8px;">{{ $noteWithOctave }}</span>
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
        <p class="text-xs text-zinc-500 mt-1">Click to test if audio is working</p>
        <p class="text-xs text-green-400 mt-1">
            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Audio samples imported from ChordChord
        </p>
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
                @if(in_array($selectedSound, ['cinematic', 'jazz']))
                    <span class="text-xs text-green-400" title="Using high-quality audio samples">
                        <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <style>
/* ChordChord-style piano key styling */
.full-piano-white {
    background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%);
    border-radius: 0 0 4px 4px;
    position: relative;
    min-width: 20px;
}

.full-piano-white:hover {
    background: linear-gradient(180deg, #f0f0f0 0%, #e8e8e8 100%);
    transform: translateY(1px);
}

.full-piano-white.pressed,
.full-piano-white:active {
    background: linear-gradient(180deg, #3f9bff 0%, #2980ff 100%);
    transform: translateY(3px);
    box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
}

.full-piano-white.pressed .key-label {
    color: white !important;
    font-weight: 600;
}

.full-piano-black {
    background: linear-gradient(180deg, #1a1a1a 0%, #000000 100%);
    border-radius: 0 0 3px 3px;
}

.full-piano-black:hover {
    background: linear-gradient(180deg, #333 0%, #111 100%);
    transform: translateY(1px);
}

.full-piano-black.pressed,
.full-piano-black:active {
    background: linear-gradient(180deg, #3f9bff 0%, #2980ff 100%);
    transform: translateY(2px);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.5);
}

.full-piano-black.pressed .key-label {
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
</style>

{{-- JavaScript for audio playback --}}
<script>
// Audio context and sample management
let audioContext = null;
let pianoSampler = null;
let isAudioLoaded = false;

// Global test function
window.testAudio = async function() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    
    try {
        // Resume audio context if suspended
        if (audioContext.state === 'suspended') {
            await audioContext.resume();
        }
        
        // Use loaded samples if available
        if (pianoSampler && isAudioLoaded) {
            pianoSampler.triggerAttackRelease('C4', '8n');
            console.log('Test sound played using samples');
        } else if (typeof Tone !== 'undefined') {
            // Fallback to Tone.js synthesis
            await Tone.start();
            const synth = new Tone.Synth().toDestination();
            synth.triggerAttackRelease('C4', '8n');
            console.log('Test sound played using synthesis');
        } else {
            alert('Audio not loaded. Please wait a moment and try again.');
        }
    } catch (error) {
        alert('Audio error: ' + error.message);
        console.error('Audio test failed:', error);
    }
};

document.addEventListener('livewire:initialized', () => {
    let synth = null;
    let sequence = null;
    let currentSound = 'piano';
    let audioInitialized = false;
    let multiPlayer = null;
    
    // Sound presets for different piano types with enhanced audio quality
    const soundPresets = {
        piano: {
            oscillator: { 
                type: 'triangle8',
                partialCount: 16
            },
            envelope: { 
                attack: 0.005, 
                decay: 0.1, 
                sustain: 0.3, 
                release: 1.5,
                attackCurve: 'exponential'
            },
            filterEnvelope: {
                attack: 0.001,
                decay: 0.2,
                sustain: 0.5,
                release: 1.5,
                baseFrequency: 2000,
                octaves: 2
            }
        },
        cinematic: {
            oscillator: { 
                type: 'triangle',
                partialCount: 24,
                phase: 90
            },
            envelope: { 
                attack: 0.02, 
                decay: 0.5, 
                sustain: 0.4, 
                release: 2.0,
                attackCurve: 'cosine'
            },
            filterEnvelope: {
                attack: 0.01,
                decay: 0.3,
                sustain: 0.6,
                release: 2.0,
                baseFrequency: 3000,
                octaves: 3
            }
        },
        jazz: {
            oscillator: { 
                type: 'sine',
                modulationType: 'sine',
                modulationIndex: 1.5,
                harmonicity: 0.5
            },
            envelope: { 
                attack: 0.01, 
                decay: 0.3, 
                sustain: 0.3, 
                release: 1.2 
            },
            filterEnvelope: {
                attack: 0.02,
                decay: 0.2,
                sustain: 0.4,
                release: 1.0,
                baseFrequency: 1800,
                octaves: 1.5
            }
        },
        electric: {
            oscillator: { 
                type: 'pulse',
                width: 0.2,
                modulationType: 'sine',
                modulationIndex: 2
            },
            envelope: { 
                attack: 0.02, 
                decay: 0.3, 
                sustain: 0.4, 
                release: 0.8 
            }
        },
        synth: {
            oscillator: { 
                type: 'fattriangle',
                spread: 20,
                count: 3
            },
            envelope: { 
                attack: 0.02, 
                decay: 0.2, 
                sustain: 0.5, 
                release: 0.5 
            },
            filter: {
                type: 'lowpass',
                frequency: 1200,
                rolloff: -24,
                Q: 2
            }
        },
        rhodes: {
            oscillator: { 
                type: 'fmtriangle',
                modulationType: 'square',
                modulationIndex: 2,
                harmonicity: 0.5
            },
            envelope: { 
                attack: 0.02, 
                decay: 0.5, 
                sustain: 0.2, 
                release: 1.5,
                attackCurve: 'linear'
            }
        },
        organ: {
            oscillator: { 
                type: 'fatsquare',
                spread: 40,
                count: 5
            },
            envelope: { 
                attack: 0.001, 
                decay: 0.01, 
                sustain: 0.95, 
                release: 0.01 
            }
        }
    };
    
    // Initialize audio with piano samples
    async function initializeAudio() {
        if (!audioInitialized) {
            try {
                // Initialize multi-instrument player if available
                if (typeof MultiInstrumentPlayer !== 'undefined' && !multiPlayer) {
                    multiPlayer = new MultiInstrumentPlayer();
                    window.pianoPlayer = multiPlayer; // Make it globally accessible
                    console.log('Multi-instrument player initialized');
                }
                
                // Initialize Tone.js as fallback or additional synthesis
                if (typeof Tone !== 'undefined') {
                    // Start the audio context
                    await Tone.start();
                    console.log('Tone.js audio context started');
                    
                    // Create a more sophisticated piano synth with effects chain
                    const reverb = new Tone.Reverb({
                        decay: 2.5,
                        preDelay: 0.01,
                        wet: 0.2
                    }).toDestination();
                
                const chorus = new Tone.Chorus({
                    frequency: 1.5,
                    delayTime: 3.5,
                    depth: 0.7,
                    type: 'triangle',
                    spread: 180,
                    wet: 0.1
                }).connect(reverb);
                
                // Create the synth with better piano characteristics
                synth = new Tone.PolySynth(Tone.Synth, {
                    maxPolyphony: 32,
                    voice: 12,
                    options: {
                        oscillator: {
                            type: 'triangle8'
                        },
                        envelope: {
                            attack: 0.005,
                            decay: 0.1,
                            sustain: 0.3,
                            release: 1.5,
                            attackCurve: 'exponential'
                        },
                        volume: -8
                    }
                }).connect(chorus);
                
                // Add a compressor for more consistent volume
                const compressor = new Tone.Compressor({
                    ratio: 3,
                    threshold: -24,
                    release: 0.25,
                    attack: 0.003,
                    knee: 5
                });
                synth.chain(compressor, chorus);
                
                // Apply initial sound preset
                synth.set(soundPresets.piano);
                
                // Note about available audio samples
                console.log('High-quality audio samples available at /audio/instruments/');
                console.log('Available instruments:', [
                    'cinematic-piano.mp3',
                    'piano-jazz.mp3',
                    'electric-guitar.mp3',
                    'strings.mp3',
                    'synthwave.mp3'
                ]);
                
                    audioInitialized = true;
                    isAudioLoaded = true;
                    console.log('Audio initialized successfully with effects chain');
                } // End of Tone.js initialization
                
                audioInitialized = true;
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
        if (!synth) {
            console.error('Synth not initialized');
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
        if (Tone.Transport.state === 'started') {
            Tone.Transport.bpm.value = tempo;
        }
    });
    
    Livewire.on('sound-changed', async ({ sound }) => {
        currentSound = sound;
        
        // Update multi-instrument player
        if (multiPlayer) {
            const instrumentMap = {
                'piano': 'cinematic-piano',
                'cinematic': 'cinematic-piano',
                'jazz': 'piano-jazz',
                'synth': 'synthwave',
                'electric': 'electric-guitar',
                'rhodes': 'cinematic-piano', // Use cinematic as fallback
                'organ': 'cinematic-piano'    // Use cinematic as fallback
            };
            
            if (instrumentMap[sound]) {
                await multiPlayer.switchInstrument(instrumentMap[sound]);
                console.log(`Multi-instrument player switched to ${instrumentMap[sound]}`);
            }
        }
        
        // Update Tone.js synth
        if (synth && soundPresets[sound]) {
            console.log(`Switching Tone.js to ${sound} sound`);
            
            // Apply the preset
            synth.set(soundPresets[sound]);
            
            // Additional processing for sample-based sounds
            if (sound === 'cinematic' || sound === 'jazz') {
                console.log(`Using enhanced ${sound} piano preset`);
            }
            
            // Play a test note to demonstrate the new sound
            if (audioInitialized) {
                if (multiPlayer) {
                    multiPlayer.playNote('C4', 0.5);
                } else {
                    synth.triggerAttackRelease('C4', '8n');
                }
            }
        }
    });
    
    function startPlayback() {
        // Get current chords from the component
        const chords = @js($chords);
        const tempo = @this.tempo;
        
        Tone.Transport.bpm.value = tempo;
        
        // Create chord progression sequence
        const chordNotes = [];
        Object.values(chords).forEach(chord => {
            if (chord.tone) {
                const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
                chordNotes.push(notes);
            }
        });
        
        if (chordNotes.length > 0) {
            let chordIndex = 0;
            sequence = new Tone.Loop((time) => {
                synth.triggerAttackRelease(chordNotes[chordIndex % chordNotes.length], '2n', time);
                
                // Update the displayed chord
                @this.updateCurrentChord(chordIndex % chordNotes.length);
                
                chordIndex++;
                
                // Update progress
                @this.currentTime = (chordIndex * 2) % 8;
            }, '2n');
            
            sequence.start(0);
            Tone.Transport.start();
        }
    }
    
    function stopPlayback() {
        if (sequence) {
            sequence.stop();
            sequence.dispose();
            sequence = null;
        }
        Tone.Transport.stop();
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
        if (!synth || !audioInitialized) return;
        
        // Map keyboard keys to piano notes
        const keyMap = {
            'a': 'C4', 'w': 'C#4', 's': 'D4', 'e': 'D#4', 'd': 'E4',
            'f': 'F4', 't': 'F#4', 'g': 'G4', 'y': 'G#4', 'h': 'A4',
            'u': 'A#4', 'j': 'B4', 'k': 'C5'
        };
        
        const note = keyMap[e.key.toLowerCase()];
        if (note) {
            synth.triggerAttackRelease(note, '8n');
        }
    });
    
    // Add click handlers for piano keys (both old SVG and new HTML buttons)
    document.addEventListener('click', async (e) => {
        const target = e.target.closest('.piano-key, .full-piano-white, .full-piano-black');
        if (target) {
            await initializeAudio();
            if (!audioInitialized) return;
            
            const note = target.getAttribute('data-note');
            if (note) {
                // Use multi-instrument player if available, otherwise use Tone.js
                if (multiPlayer && multiPlayer.isLoaded) {
                    multiPlayer.playNote(note, 0.5);
                    console.log('Playing note with multi-instrument player:', note);
                } else if (synth) {
                    synth.triggerAttackRelease(note, '8n');
                    console.log('Playing note with Tone.js:', note);
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