<div class="piano-player">
    {{-- Play Controls Section --}}
    <div class="mb-4 px-6 space-y-4">
        {{-- Rhythm, Time, BPM, and Bass Line Dropdowns --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Rhythm Control --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Rhythm</label>
                <select wire:model.live="selectedRhythm" class="w-full rounded-md border-gray-600 bg-zinc-700 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($rhythmPatterns as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Bass Line Control --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Bass Line</label>
                <select wire:model.live="selectedBassLine" class="w-full rounded-md border-gray-600 bg-zinc-700 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($bassLinePatterns as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Time Signature Control --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Time</label>
                <select wire:model.live="timeSignature" class="w-full rounded-md border-gray-600 bg-zinc-700 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($timeSignatures as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- BPM Control --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">BPM</label>
                <select wire:model.live="tempo" wire:change="updateTempo($event.target.value)" class="w-full rounded-md border-gray-600 bg-zinc-700 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($bpmPresets as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Play/Pause Button and Timeline --}}
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

            @if($isPlaying)
                <span class="text-sm text-gray-300">
                    Playing: {{ $rhythmPatterns[$selectedRhythm] }} in {{ $timeSignature }} @ {{ $tempo }} BPM
                </span>
            @endif

            {{-- Timeline Progress --}}
            <div class="flex-1">
                <div class="relative h-2 bg-zinc-800 rounded-full overflow-hidden">
                    <div
                        class="absolute h-full bg-blue-500 transition-all duration-100"
                        style="width: {{ ($currentTime / $duration) * 100 }}%"
                    ></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Piano Display - Canvas 2D (High Performance) --}}
    <div class="bg-zinc-950 rounded-lg shadow-inner space-y-4">
        {{-- Canvas Piano Layout (C2 - C6) --}}
        <div class="bg-zinc-900 rounded-lg p-6">
            {{-- Volume Control --}}
            <div class="flex items-center gap-4 mb-4" x-data="{ volumeValue: 70 }">
                <label for="volume-slider" class="text-sm font-medium text-gray-300 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                    Volume
                </label>
                <input
                    type="range"
                    id="volume-slider"
                    min="0"
                    max="100"
                    x-model="volumeValue"
                    class="flex-1 h-2 bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                    @input="
                        const volume = volumeValue / 100;
                        if (window.pianoPlayer) {
                            window.pianoPlayer.setVolume(volume);
                        }
                        if (window.pianoAudio) {
                            window.pianoAudio.setVolume(volume);
                        }
                    "
                >
                <span class="text-sm text-gray-400 min-w-[3rem] text-right" x-text="volumeValue + '%'">70%</span>
            </div>

            <div class="piano-container bg-zinc-800 rounded-lg p-4" id="piano-keyboard">
                <canvas
                    id="piano-canvas"
                    class="w-full"
                    style="height: 180px;"
                    x-data="pianoPlayerCanvas()"
                    x-init="init()"
                    wire:ignore
                ></canvas>
            </div>
        </div>

        {{-- Note Display Component --}}
        <div class="pb-4">
            <livewire:note-display :activeNotes="$activeNotes" :highlightAccidentals="true" />
        </div>
    </div>

    @script
    <script>
        Alpine.data('pianoPlayerCanvas', () => ({
            piano: null,

            init() {
                console.log('🎹 Initializing Canvas Piano...');

                // Initialize Canvas piano
                this.piano = new window.PianoCanvas(this.$el, {
                    startNote: 'C2',
                    endNote: 'C6',
                    whiteKeyWidth: 40,
                    whiteKeyHeight: 150,
                    blackKeyWidth: 24,
                    blackKeyHeight: 100,
                    activeColor: '#60A5FA',
                    activeBlackColor: '#3B82F6',
                });

                console.log('✅ Canvas Piano initialized');

                // Listen for active notes updates from Livewire
                Livewire.on('update-active-notes', (event) => {
                    console.log('🎵 update-active-notes event received:', event);
                    const notes = event.notes || [];
                    console.log('Setting active notes:', notes);
                    this.piano.setActiveNotes(notes);
                });

                console.log('✅ Listening for update-active-notes events');

                // Handle piano key clicks - dispatch to Livewire
                this.$el.addEventListener('piano-key-click', (e) => {
                    const note = e.detail.note;
                    console.log('🎹 Piano key clicked:', note);
                    // Play the note using existing piano audio
                    if (window.pianoAudio) {
                        window.pianoAudio.playNote(note);
                    }
                });
            },

            destroy() {
                if (this.piano) {
                    this.piano.destroy();
                }
            }
        }));
    </script>
    @endscript

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
let sustainInterval = null;
let currentSustainedChord = null;

// Helper functions that will be populated later
let getChordNotes = null;
let updateActiveKeys = null;
let initializeAudio = null;

// Global functions for chord sustain (accessible from Alpine.js)
window.startChordSustain = async function(position, chord) {
    console.log('startChordSustain called', { position, chord });

    if (initializeAudio) {
        await initializeAudio();
    }

    if (!chord || !chord.tone || !pianoPlayer || !pianoPlayer.isLoaded) {
        console.log('Cannot start sustain - missing data or player not ready', {
            hasChord: !!chord,
            hasTone: chord?.tone,
            hasPianoPlayer: !!pianoPlayer,
            isLoaded: pianoPlayer?.isLoaded
        });
        return;
    }

    // Stop any existing sustain first
    if (sustainInterval) {
        clearInterval(sustainInterval);
        sustainInterval = null;
    }

    // Store the chord being sustained
    currentSustainedChord = chord;

    // Get chord notes - use the outer scope variable
    console.log('Getting chord notes, getChordNotes type:', typeof getChordNotes);
    if (!getChordNotes) {
        console.error('getChordNotes is not available yet!');
        return;
    }
    const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
    console.log('Starting sustained chord with notes:', notes);

    // Get the selected bass line pattern from Livewire
    let bassLine = 'root-fifth'; // Default
    if (typeof @this !== 'undefined' && @this) {
        try {
            bassLine = await @this.get('selectedBassLine');
        } catch (e) {
            console.log('Could not get bass line setting, using default:', e);
        }
    }

    // Calculate bass notes based on bass line pattern
    const rootNote = notes[0];
    const noteMatch = rootNote.match(/([A-G]#?)(\d+)/);
    let allNotes = [...notes];

    if (noteMatch && bassLine !== 'none') {
        const [, noteName] = noteMatch;
        const bassNote = noteName + '2';

        // Add bass notes based on pattern
        switch (bassLine) {
            case 'root-only':
                allNotes = [bassNote, ...notes];
                break;

            case 'root-octave':
                const bassOctaveUp = noteName + '3';
                allNotes = [bassNote, bassOctaveUp, ...notes];
                break;

            case 'root-fifth':
            case 'root-fifth-alt':
                // Calculate fifth
                const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const rootIndex = noteOrder.indexOf(noteName);
                const fifthIndex = (rootIndex + 7) % 12;
                const fifthNoteName = noteOrder[fifthIndex];
                let fifthOctave = 2;
                if (fifthIndex < rootIndex) {
                    fifthOctave = 3;
                }
                const fifthNote = fifthNoteName + fifthOctave;
                allNotes = [bassNote, fifthNote, ...notes];
                break;

            case 'walking':
                // For walking bass on sustain, just play root and fifth
                const noteOrder2 = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const rootIndex2 = noteOrder2.indexOf(noteName);
                const fifthIndex2 = (rootIndex2 + 7) % 12;
                const fifthNoteName2 = noteOrder2[fifthIndex2];
                let fifthOctave2 = 2;
                if (fifthIndex2 < rootIndex2) {
                    fifthOctave2 = 3;
                }
                const fifthNote2 = fifthNoteName2 + fifthOctave2;
                allNotes = [bassNote, fifthNote2, ...notes];
                break;

            default:
                allNotes = [bassNote, ...notes];
        }
    }

    console.log('Playing sustained chord with bass line:', bassLine, 'Notes:', allNotes);

    // Play the chord with sostenuto (indefinite sustain until stopAll is called)
    console.log('Playing chord with sostenuto...');
    pianoPlayer.playChordWithSostenuto(allNotes);
    console.log('Updating active keys...');
    updateActiveKeys(allNotes);
};

window.stopChordSustain = function() {
    console.log('stopChordSustain called');

    if (sustainInterval) {
        console.log('Clearing sustain interval');
        clearInterval(sustainInterval);
        sustainInterval = null;
    }

    currentSustainedChord = null;

    // Stop all currently playing notes
    console.log('Stopping all piano notes, pianoPlayer exists:', !!pianoPlayer);
    if (pianoPlayer && pianoPlayer.stopAll) {
        console.log('Calling pianoPlayer.stopAll()');
        pianoPlayer.stopAll();
        console.log('pianoPlayer.stopAll() completed');
    } else {
        console.error('pianoPlayer or stopAll method not available');
    }

    // Clear Canvas piano key highlights
    console.log('🎹 Clearing Canvas piano active notes');
    Livewire.dispatch('update-active-notes', { notes: [] });

    console.log('stopChordSustain completed');
};

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
    async function initializeAudioInternal() {
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
    window.initializeAudio = initializeAudioInternal;
    initializeAudio = initializeAudioInternal;

    // Initialize on first user interaction
    document.addEventListener('click', async () => {
        await initializeAudioInternal();
    }, { once: true });
    document.addEventListener('keydown', async () => {
        await initializeAudioInternal();
    }, { once: true });

    // Listen for playback events
    Livewire.on('toggle-playback', async ({ isPlaying }) => {
        await initializeAudioInternal(); // Ensure audio is initialized
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

    Livewire.on('tempo-changed', async ({ tempo }) => {
        console.log('Tempo changed to:', tempo);
        // If playing, restart playback from first chord with new tempo
        if (typeof @this !== 'undefined' && @this) {
            const isPlaying = await @this.get('isPlaying');
            if (isPlaying) {
                stopPlayback();
                await @this.set('currentChordIndex', 0);
                setTimeout(() => {
                    @this.call('togglePlayback');
                }, 100);
            }
        }
    });

    // Listen for rhythm changes
    Livewire.on('rhythm-changed', async ({ rhythm }) => {
        console.log('Rhythm changed to:', rhythm);
        // If playing, restart playback from first chord with new rhythm
        if (typeof @this !== 'undefined' && @this) {
            const isPlaying = await @this.get('isPlaying');
            if (isPlaying) {
                stopPlayback();
                await @this.set('currentChordIndex', 0);
                setTimeout(() => {
                    @this.call('togglePlayback');
                }, 100);
            }
        }
    });

    // Listen for time signature changes
    Livewire.on('time-signature-changed', async ({ timeSignature }) => {
        console.log('Time signature changed to:', timeSignature);
        // If playing, restart playback from first chord with new time signature
        if (typeof @this !== 'undefined' && @this) {
            const isPlaying = await @this.get('isPlaying');
            if (isPlaying) {
                stopPlayback();
                await @this.set('currentChordIndex', 0);
                setTimeout(() => {
                    @this.call('togglePlayback');
                }, 100);
            }
        }
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
        const notes = getChordNotesInternal(chord.tone, chord.semitone, chord.inversion);
        
        // Stop any currently playing notes and play the new chord for 1.5 seconds
        if (pianoPlayer && pianoPlayer.isLoaded) {
            pianoPlayer.stopAll(); // Stop previous chord
            setTimeout(() => {
                pianoPlayer.playChord(notes, 1.5); // Play chord for 1.5 seconds
                console.log('Playing chord for 1.5 seconds:', notes);
                // Update the piano display with the correct notes
                updateActiveKeysInternal(notes);
            }, 50); // Brief delay for smooth transition
        }
        
        // Update the piano player's current chord display (for UI text only, not piano keys)
        if (typeof @this !== 'undefined' && @this) {
            @this.call('setCurrentChord', chord);
        }
    });

    async function startPlayback() {
        // Check if @this (Livewire component) is available
        if (typeof @this === 'undefined' || !@this) {
            console.warn('Piano player startPlayback called but Livewire component not available (visualization-only mode)');
            return;
        }

        // Get current chords and settings from the Livewire component dynamically
        const chords = await @this.get('chords');
        const tempo = await @this.get('tempo');
        const rhythm = await @this.get('selectedRhythm');
        const timeSignature = await @this.get('timeSignature');
        const bassLine = await @this.get('selectedBassLine');

        console.log('Starting playback with chords:', chords);
        console.log('Chords object keys:', Object.keys(chords));
        console.log('Chords values:', Object.values(chords));
        console.log('Tempo:', tempo);
        console.log('Rhythm:', rhythm);
        console.log('Time Signature:', timeSignature);
        console.log('Bass Line:', bassLine);

        // Create chord progression sequence
        const chordNotes = [];
        const chordData = [];
        Object.values(chords).forEach(chord => {
            if (chord.tone) {
                const notes = getChordNotesInternal(chord.tone, chord.semitone, chord.inversion);
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

            // Parse time signature to get beats per measure
            const [beatsPerMeasure] = timeSignature.split('/').map(Number);
            const measureDuration = beatDuration * beatsPerMeasure; // Full measure in ms

            function playRhythmPattern(originalChordNotes, bassNote, rhythm, fifthNote, bassOctaveUp, chordIndex) {
                // Don't transpose - use the original chord notes as-is
                const chordNotes = originalChordNotes;

                console.log('Rhythm pattern - Playing:', rhythm, 'Bass:', bassNote, 'Fifth:', fifthNote, 'Chord:', chordNotes, 'BassLine:', bassLine);

                // Apply bass line pattern based on selectedBassLine
                const playBassPattern = () => {
                    switch (bassLine) {
                        case 'root-only':
                            // Just root on beat 1
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, measureDuration / 1000);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                            }
                            break;

                        case 'root-octave':
                            // Root on 1, octave on 3
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, measureDuration / 1000);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                                setTimeout(() => {
                                    pianoPlayer.playNote(bassOctaveUp, 0.5);
                                    updateActiveKeysInternal([bassOctaveUp, ...chordNotes]);
                                }, beatDuration * 2);
                            }
                            break;

                        case 'root-fifth':
                            // Root on 1, fifth on 3 (most common)
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, measureDuration / 1000);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...chordNotes]);
                                }, beatDuration * 2);
                            }
                            break;

                        case 'root-fifth-alt':
                            // Root on 1&3, fifth on 2&4 (alternating)
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, 0.5);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...chordNotes]);
                                }, beatDuration);
                                setTimeout(() => {
                                    pianoPlayer.playNote(bassNote, 0.5);
                                    updateActiveKeysInternal([bassNote, ...chordNotes]);
                                }, beatDuration * 2);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...chordNotes]);
                                }, beatDuration * 3);
                            }
                            break;

                        case 'walking':
                            // Walking bass: root, third, fifth, approach note to next chord
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, 0.5);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                                const thirdNote = chordNotes[0];
                                setTimeout(() => {
                                    pianoPlayer.playNote(thirdNote, 0.5);
                                    updateActiveKeysInternal([thirdNote, ...chordNotes]);
                                }, beatDuration);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...chordNotes]);
                                }, beatDuration * 2);
                                // Approach note to next chord on beat 4
                                const nextChordIndex = (chordIndex + 1) % chordNotes.length;
                                const nextRoot = chordNotes[nextChordIndex] ? chordNotes[nextChordIndex][0] : bassNote;
                                const walkingApproachNote = getWalkingNote(bassNote, nextRoot);
                                setTimeout(() => {
                                    pianoPlayer.playNote(walkingApproachNote, 0.5);
                                    updateActiveKeysInternal([walkingApproachNote, ...chordNotes]);
                                }, beatDuration * 3);
                            }
                            break;

                        case 'none':
                            // No bass line
                            updateActiveKeysInternal([...chordNotes]);
                            break;

                        default:
                            // Default to root-fifth
                            if (bassNote) {
                                pianoPlayer.playNote(bassNote, measureDuration / 1000);
                                updateActiveKeysInternal([bassNote, ...chordNotes]);
                            }
                    }
                };

                switch (rhythm) {
                    case 'alberti':
                        // Alberti bass: 5th-3rd-5th pattern (16th notes)
                        if (chordNotes.length >= 2) {
                            const fifth = chordNotes[1] || chordNotes[0];
                            const third = chordNotes[0];

                            // Beat 1: Play bass pattern + alberti pattern
                            setTimeout(() => {
                                playBassPattern();
                                pianoPlayer.playNote(fifth, 0.3);
                            }, 0);

                            // 16th note 2: third
                            setTimeout(() => {
                                pianoPlayer.playNote(third, 0.3);
                            }, beatDuration / 4);

                            // 16th note 3: fifth
                            setTimeout(() => {
                                pianoPlayer.playNote(fifth, 0.3);
                            }, beatDuration / 2);

                            // 16th note 4: third
                            setTimeout(() => {
                                pianoPlayer.playNote(third, 0.3);
                            }, (beatDuration * 3) / 4);
                        }
                        break;

                    case 'waltz':
                        // Waltz: bass pattern on 1, chord on 2 and 3
                        setTimeout(() => {
                            playBassPattern();
                        }, 0);

                        setTimeout(() => {
                            pianoPlayer.playChord(chordNotes, 0.4);
                            updateActiveKeysInternal([bassNote, ...chordNotes]);
                        }, beatDuration);

                        setTimeout(() => {
                            pianoPlayer.playChord(chordNotes, 0.4);
                            updateActiveKeysInternal([bassNote, ...chordNotes]);
                        }, beatDuration * 2);
                        break;

                    case 'broken':
                        // Broken chord: sequential notes with bass pattern
                        setTimeout(() => {
                            playBassPattern();
                        }, 0);

                        chordNotes.forEach((note, i) => {
                            setTimeout(() => {
                                pianoPlayer.playNote(note, 0.4);
                            }, i * beatDuration);
                        });
                        break;

                    case 'arpeggio':
                        // Arpeggio: fast sequential notes with bass pattern
                        setTimeout(() => {
                            playBassPattern();
                        }, 0);

                        chordNotes.forEach((note, i) => {
                            setTimeout(() => {
                                pianoPlayer.playNote(note, 0.3);
                            }, i * (beatDuration / 2));
                        });
                        break;

                    case 'march':
                        // March: strong-weak chord pattern with bass pattern
                        setTimeout(() => {
                            playBassPattern();
                            pianoPlayer.playChord(chordNotes, 0.6);
                        }, 0);

                        setTimeout(() => {
                            pianoPlayer.playChord(chordNotes, 0.4);
                            updateActiveKeysInternal([bassNote, ...chordNotes]);
                        }, beatDuration);
                        break;

                    case 'ballad':
                        // Ballad: sustained bass pattern and chord
                        setTimeout(() => {
                            playBassPattern();
                            pianoPlayer.playChord(chordNotes, measureDuration / 1000);
                        }, 0);
                        break;

                    case 'ragtime':
                        // Ragtime: syncopated chord pattern with bass pattern
                        setTimeout(() => {
                            playBassPattern();
                        }, 0);

                        setTimeout(() => {
                            pianoPlayer.playChord(chordNotes, 0.3);
                            updateActiveKeysInternal([bassNote, ...chordNotes]);
                        }, beatDuration * 1.5);

                        setTimeout(() => {
                            pianoPlayer.playChord(chordNotes, 0.3);
                            updateActiveKeysInternal([bassNote, ...chordNotes]);
                        }, beatDuration * 2.5);
                        break;
                }
            }

            // Helper function to get the fifth note
            function getFifthNote(rootNote) {
                const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const noteMatch = rootNote.match(/([A-G]#?)(\d+)/);
                if (!noteMatch) return rootNote;

                const [, noteName, octave] = noteMatch;
                const rootIndex = noteOrder.indexOf(noteName);
                const fifthIndex = (rootIndex + 7) % 12; // Perfect fifth is 7 semitones up
                const fifthNote = noteOrder[fifthIndex];

                // Adjust octave if we wrapped around
                let fifthOctave = parseInt(octave);
                if (fifthIndex < rootIndex) {
                    fifthOctave++;
                }

                return fifthNote + fifthOctave;
            }

            // Helper function to get walking bass note (chromatic approach to next chord)
            function getWalkingNote(currentRoot, nextRoot) {
                const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const currentMatch = currentRoot.match(/([A-G]#?)(\d+)/);
                const nextMatch = nextRoot.match(/([A-G]#?)(\d+)/);

                if (!currentMatch || !nextMatch) return currentRoot;

                const [, currentNote, currentOctave] = currentMatch;
                const [, nextNote] = nextMatch;
                const currentIndex = noteOrder.indexOf(currentNote);
                const nextIndex = noteOrder.indexOf(nextNote);

                // Chromatic approach note (one semitone below target)
                let approachIndex = (nextIndex - 1 + 12) % 12;
                let approachOctave = parseInt(currentOctave);

                // If approach note is higher in the scale, might need to adjust octave
                if (approachIndex === 11 && nextIndex === 0) {
                    approachOctave++;
                }

                return noteOrder[approachIndex] + approachOctave;
            }

            function playNextBeat() {
                const currentChordNotes = chordNotes[chordIndex];
                const beat = beatCount % beatsPerMeasure;
                const measureBeat = beat + 1;

                console.log(`Measure ${chordIndex + 1}, Beat ${measureBeat}:`, currentChordNotes, 'Rhythm:', rhythm, 'Bass Line:', bassLine);

                // Extract root note name for bass
                const rootNote = currentChordNotes[0];
                const noteMatch = rootNote.match(/([A-G]#?)(\d+)/);

                if (noteMatch) {
                    const [, noteName] = noteMatch;
                    const bassNote = noteName + '2';
                    const bassOctaveUp = noteName + '3';
                    const fifthNote = getFifthNote(bassNote);

                    console.log('Playing notes - Bass:', bassNote, 'Fifth:', fifthNote, 'Chord:', currentChordNotes);

                    // Determine which bass notes to play based on bass line pattern
                    let bassNotesToPlay = [];
                    let bassNotesToVisualize = [];

                    if (beat === 0) {
                        // Start of new measure - determine bass pattern
                        switch (bassLine) {
                            case 'root-only':
                                // Just root on beat 1
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                                break;

                            case 'root-octave':
                                // Root on 1, octave on 3
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                                setTimeout(() => {
                                    pianoPlayer.playNote(bassOctaveUp, 0.5);
                                    updateActiveKeysInternal([bassOctaveUp, ...currentChordNotes]);
                                }, beatDuration * 2);
                                break;

                            case 'root-fifth':
                                // Root on 1, fifth on 3 (most common)
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...currentChordNotes]);
                                }, beatDuration * 2);
                                break;

                            case 'root-fifth-alt':
                                // Root on 1&3, fifth on 2&4 (alternating)
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...currentChordNotes]);
                                }, beatDuration);
                                setTimeout(() => {
                                    pianoPlayer.playNote(bassNote, 0.5);
                                    updateActiveKeysInternal([bassNote, ...currentChordNotes]);
                                }, beatDuration * 2);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...currentChordNotes]);
                                }, beatDuration * 3);
                                break;

                            case 'walking':
                                // Walking bass: root, third, fifth, approach note to next chord
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                                const thirdNote = currentChordNotes[0]; // Use chord's actual third
                                setTimeout(() => {
                                    pianoPlayer.playNote(thirdNote, 0.5);
                                    updateActiveKeysInternal([thirdNote, ...currentChordNotes]);
                                }, beatDuration);
                                setTimeout(() => {
                                    pianoPlayer.playNote(fifthNote, 0.5);
                                    updateActiveKeysInternal([fifthNote, ...currentChordNotes]);
                                }, beatDuration * 2);
                                // Approach note to next chord on beat 4
                                const nextChordIndex = (chordIndex + 1) % chordNotes.length;
                                const nextRoot = chordNotes[nextChordIndex][0];
                                const walkingApproachNote = getWalkingNote(bassNote, nextRoot);
                                setTimeout(() => {
                                    pianoPlayer.playNote(walkingApproachNote, 0.5);
                                    updateActiveKeysInternal([walkingApproachNote, ...currentChordNotes]);
                                }, beatDuration * 3);
                                break;

                            case 'none':
                                // No bass line
                                bassNotesToPlay = [];
                                bassNotesToVisualize = [];
                                break;

                            default:
                                bassNotesToPlay = [bassNote];
                                bassNotesToVisualize = [bassNote];
                        }

                        // Play rhythm pattern or block chords
                        if (rhythm !== 'block') {
                            // For rhythm patterns, pass all needed parameters including bass line info
                            playRhythmPattern(currentChordNotes, bassNote, rhythm, fifthNote, bassOctaveUp, chordIndex);
                        } else {
                            // Block chords: play bass and chord together
                            setTimeout(() => {
                                if (bassNotesToPlay.length > 0) {
                                    bassNotesToPlay.forEach(note => {
                                        pianoPlayer.playNote(note, beatsPerMeasure * (beatDuration / 1000));
                                    });
                                }
                                pianoPlayer.playChord(currentChordNotes, 0.8);
                                console.log('Visualizing keys:', [...bassNotesToVisualize, ...currentChordNotes]);
                                updateActiveKeysInternal([...bassNotesToVisualize, ...currentChordNotes]);
                            }, 0);
                        }
                    } else if (rhythm === 'block') {
                        // Block chords: chord on beats 2, 3, 4, etc. (bass sustains)
                        // Need to show all bass notes that are sustaining
                        let sustainingBassNotes = [];

                        switch (bassLine) {
                            case 'root-only':
                                sustainingBassNotes = [bassNote];
                                break;
                            case 'root-octave':
                                sustainingBassNotes = [bassNote, bassOctaveUp];
                                break;
                            case 'root-fifth':
                            case 'root-fifth-alt':
                            case 'walking':
                                sustainingBassNotes = [bassNote, fifthNote];
                                break;
                            case 'none':
                                sustainingBassNotes = [];
                                break;
                            default:
                                sustainingBassNotes = [bassNote];
                        }

                        setTimeout(() => {
                            pianoPlayer.playChord(currentChordNotes, 0.8);
                            console.log('Visualizing keys (beat ' + measureBeat + '):', [...sustainingBassNotes, ...currentChordNotes]);
                            updateActiveKeysInternal([...sustainingBassNotes, ...currentChordNotes]);
                        }, 0);
                    }
                }

                // Update the displayed chord info
                if (typeof @this !== 'undefined' && @this) {
                    @this.call('updateCurrentChord', chordIndex);
                }

                // Highlight the chord in the grid
                const chordPosition = chordData[chordIndex].position;
                Livewire.dispatch('highlight-chord-position', { position: chordPosition });

                beatCount++;

                // Move to next chord after completing measure
                if (beatCount % beatsPerMeasure === 0) {
                    chordIndex++;

                    // Loop back to beginning if we've reached the end
                    if (chordIndex >= chordNotes.length) {
                        console.log('Looping back to start of progression');
                        chordIndex = 0;
                    }
                }

                // Update progress (beat-based)
                if (typeof @this !== 'undefined' && @this) {
                    @this.set('currentTime', beatCount % (beatsPerMeasure * chordNotes.length));
                }

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
        if (typeof @this !== 'undefined' && @this) {
            @this.set('currentTime', 0);
        }

        // Clear Canvas piano key highlights
        console.log('🎹 Clearing Canvas piano on playback stop');
        Livewire.dispatch('update-active-notes', { notes: [] });
    }

    function updateActiveKeysInternal(notes) {
        console.log(`🎹 updateActiveKeys: Updating Canvas piano with notes:`, notes);

        // Convert flat notes to sharp equivalents for piano key lookup
        const flatToSharp = {
            'Db': 'C#', 'Eb': 'D#', 'Gb': 'F#', 'Ab': 'G#', 'Bb': 'A#'
        };

        // Convert flats to sharps
        const pianoNotes = notes.map(note => {
            let pianoNote = note;
            for (const [flat, sharp] of Object.entries(flatToSharp)) {
                pianoNote = pianoNote.replace(flat, sharp);
            }
            return pianoNote;
        });

        console.log(`🎵 Converted notes for Canvas:`, pianoNotes);

        // Dispatch Livewire event to update Canvas piano
        Livewire.dispatch('update-active-notes', { notes: pianoNotes });
    }

    function getChordNotesInternal(root, type, inversion) {
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

    // NOW assign the helper functions to the outer scope (after they're defined)
    getChordNotes = getChordNotesInternal;
    updateActiveKeys = updateActiveKeysInternal;
    console.log('Helper functions assigned:', {
        getChordNotes: typeof getChordNotes,
        updateActiveKeys: typeof updateActiveKeys
    });

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
            await initializeAudioInternal();
            if (!audioInitialized) return;

            const note = target.getAttribute('data-note');
            if (note) {
                // Use MultiInstrumentPlayer for note playback
                const duration = 0.5; // seconds
                if (pianoPlayer && pianoPlayer.isLoaded) {
                    pianoPlayer.playNote(note, duration);
                    console.log('Playing note with multi-instrument player:', note);
                }

                // Visual feedback - keep key highlighted for duration of sound
                target.classList.add('pressed', 'active');
                setTimeout(() => {
                    target.classList.remove('pressed', 'active');
                }, duration * 1000); // Convert to milliseconds
            }
        }
    });

    // Track which button is currently being held
    let currentlyHeldButton = null;

    // Add mousedown/mouseup handlers for chord buttons (for sustain)
    console.log('Setting up chord sustain mousedown listener');
    document.addEventListener('mousedown', async (e) => {
        console.log('Global mousedown detected on:', e.target);

        // Don't trigger sustain if clicking on inversion buttons or clear button
        if (e.target.matches('button') && !e.target.classList.contains('chord-sustain-button')) {
            console.log('Clicked on internal button (inversion/clear), ignoring');
            return;
        }

        const chordButton = e.target.closest('.chord-sustain-button');
        console.log('Found chord button:', chordButton);

        if (chordButton) {
            const tone = chordButton.getAttribute('data-chord-tone');
            console.log('Chord tone:', tone);
            if (!tone) {
                console.log('No tone, returning (empty chord slot)');
                return; // Empty chord slot
            }

            console.log('Chord button mousedown detected!');

            // Stop any previously sustained chord when clicking a new one
            if (currentlyHeldButton && currentlyHeldButton !== chordButton) {
                console.log('Stopping previous chord before starting new one');
                currentlyHeldButton.classList.remove('translate-y-1', 'border-b-2');
                if (window.stopChordSustain) {
                    window.stopChordSustain();
                }
            }

            // Track this button
            currentlyHeldButton = chordButton;

            chordButton.classList.add('translate-y-1', 'border-b-2');

            const chord = {
                position: parseInt(chordButton.getAttribute('data-chord-pos')),
                tone: tone,
                semitone: chordButton.getAttribute('data-chord-semitone') || 'major',
                inversion: chordButton.getAttribute('data-chord-inversion') || 'root'
            };

            console.log('Starting chord sustain with:', chord);
            if (window.startChordSustain) {
                await window.startChordSustain(chord.position, chord);
            } else {
                console.error('window.startChordSustain not available!');
            }
        }
    }, true); // Use capture phase

    // Handle click to select chord (since we removed wire:click)
    document.addEventListener('click', (e) => {
        const chordButton = e.target.closest('.chord-sustain-button');
        if (chordButton) {
            const position = parseInt(chordButton.getAttribute('data-chord-pos'));
            if (position && typeof Livewire !== 'undefined') {
                console.log('Selecting chord at position:', position);
                Livewire.dispatch('selectChord', { position: position });
            }
        }
    });

    document.addEventListener('mouseup', (e) => {
        console.log('Global mouseup detected');
        if (currentlyHeldButton) {
            console.log('Stopping sustained chord');
            currentlyHeldButton.classList.remove('translate-y-1', 'border-b-2');
            if (window.stopChordSustain) {
                window.stopChordSustain();
            }
            currentlyHeldButton = null;
        }
    });

    // Also stop on any mouseleave from the chord button
    document.addEventListener('mouseout', (e) => {
        if (currentlyHeldButton && e.target === currentlyHeldButton) {
            console.log('Mouse left the chord button');
            currentlyHeldButton.classList.remove('translate-y-1', 'border-b-2');
            if (window.stopChordSustain) {
                window.stopChordSustain();
            }
            currentlyHeldButton = null;
        }
    }, true);
});
</script>
</div> {{-- Close the root piano-player div --}}