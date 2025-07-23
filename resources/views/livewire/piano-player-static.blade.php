{{-- Static Piano Player (No Livewire) --}}
<div class="piano-player space-y-4">
    {{-- Piano Display --}}
    <div class="bg-zinc-950 rounded-lg p-4 shadow-inner">
        {{-- Full Piano Layout (C1 - C5) --}}
        <div class="bg-zinc-900 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="inline-flex items-center gap-2">
                    <span class="text-sm text-zinc-400">Now Playing:</span>
                    <span id="current-chord-display" class="text-lg font-medium text-zinc-500">No chord selected</span>
                </div>
            </div>

            {{-- Piano Keyboard - Realistic Layout --}}
            <div class="piano-container bg-zinc-800 rounded-lg p-4 overflow-x-auto" id="piano-keyboard">
                <div class="piano-keys relative" style="height: 150px; width: 100%; min-width: 600px; max-width: 900px; margin: 0 auto;">
                    {{-- White Keys Container --}}
                    <div class="white-keys-container absolute inset-0 flex gap-0">
                        @php
                            $octaves = [2, 3, 4];
                            $whiteKeyPattern = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
                            $totalWhiteKeys = count($octaves) * count($whiteKeyPattern);
                            $whiteKeyIndex = 0;
                        @endphp

                        @foreach($octaves as $octave)
                            @foreach($whiteKeyPattern as $note)
                                @php
                                    $noteWithOctave = $note . $octave;
                                    $isC = $note === 'C';
                                    $whiteKeyIndex++;
                                @endphp
                                <button
                                    class="piano-key white-key relative"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="flex: 1; height: 100%; position: relative;"
                                >
                                    <div class="h-full flex flex-col justify-between items-center p-1">
                                        @if($isC)
                                            <span class="octave-marker text-blue-600 font-bold text-xs">C{{ $octave }}</span>
                                        @endif
                                        <div class="flex-1"></div>
                                        @if($isC)
                                            <span class="key-label text-xs text-gray-600 font-medium">{{ $noteWithOctave }}</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        @endforeach
                    </div>

                    {{-- Black Keys Container --}}
                    <div class="black-keys-container absolute top-0 w-full pointer-events-none" style="height: 70%;">
                        @php
                            $whiteKeyWidth = 100 / $totalWhiteKeys;
                            $blackKeyWidth = $whiteKeyWidth * 0.65;
                            $blackKeys = [
                                ['note' => 'C#', 'afterWhiteKey' => 0],
                                ['note' => 'D#', 'afterWhiteKey' => 1],
                                ['note' => 'F#', 'afterWhiteKey' => 3],
                                ['note' => 'G#', 'afterWhiteKey' => 4],
                                ['note' => 'A#', 'afterWhiteKey' => 5],
                            ];
                        @endphp

                        @foreach($octaves as $octaveIndex => $octave)
                            @foreach($blackKeys as $blackKey)
                                @php
                                    $note = $blackKey['note'];
                                    $noteWithOctave = $note . $octave;
                                    $whiteKeyIndex = ($octaveIndex * 7) + $blackKey['afterWhiteKey'];
                                    $leftPosition = ($whiteKeyIndex + 1) * $whiteKeyWidth - ($blackKeyWidth * 0.5);
                                @endphp
                                <button
                                    class="piano-key black-key absolute"
                                    data-note="{{ $noteWithOctave }}"
                                    id="key-{{ $noteWithOctave }}"
                                    style="width: {{ $blackKeyWidth }}%; height: 100%; left: {{ $leftPosition }}%; z-index: 20;"
                                >
                                </button>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transport Controls --}}
    <div class="flex items-center justify-between space-x-4 bg-zinc-900 border border-zinc-800 rounded-lg p-4">
        <div class="flex items-center space-x-2">
            <button
                id="play-pause-button"
                class="transport-button group flex items-center justify-center"
                title="Play/Pause"
            >
                <svg id="play-icon" class="w-6 h-6 text-secondary group-hover:text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
                <svg id="pause-icon" class="w-6 h-6 text-secondary group-hover:text-primary hidden" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="6" y="4" width="4" height="16" />
                    <rect x="14" y="4" width="4" height="16" />
                </svg>
            </button>
        </div>

        <div class="flex-1 max-w-md">
            <div class="relative h-2 bg-zinc-800 rounded-full overflow-hidden">
                <div id="progress-bar" class="absolute h-full bg-blue-500 transition-all duration-100" style="width: 0%"></div>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <button
                id="labels-toggle"
                class="transport-button group flex items-center space-x-1"
                title="Toggle Labels"
            >
                <svg class="w-4 h-4 text-secondary group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2.001 2.001 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-xs text-secondary group-hover:text-primary">Labels</span>
            </button>

            <div class="flex items-center space-x-2">
                <label class="text-sm text-secondary">BPM</label>
                <input
                    type="number"
                    id="tempo-input"
                    value="120"
                    min="60"
                    max="200"
                    class="w-16 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-sm text-primary focus:border-blue-500 focus:outline-none"
                >
            </div>
        </div>
    </div>
</div>

<style>
/* Piano key styling */
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
    background: #60a5fa !important;
}

.black-key {
    background: #000;
    border: none;
    border-radius: 0 0 4px 4px;
    cursor: pointer;
    transition: background-color 0.1s ease;
    position: absolute;
    top: 0;
    pointer-events: auto;
}

.black-key:hover {
    background: #333;
}

.black-key.pressed,
.black-key.active {
    background: #3b82f6 !important;
}

.transport-button {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #374151;
    background: #1f2937;
    color: #9ca3af;
    cursor: pointer;
    transition: all 0.2s;
}

.transport-button:hover {
    background: #374151;
    color: #f3f4f6;
}
</style>

{{-- Load audio player and handle interactions --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('Setting up static piano player...');
    
    let pianoPlayer = null;
    let isAudioInitialized = false;
    let isPlaying = false;
    let tempo = 120;
    let currentChords = [];
    
    // Initialize audio
    async function initializeAudio() {
        if (!isAudioInitialized) {
            try {
                if (typeof MultiInstrumentPlayer !== 'undefined' && !pianoPlayer) {
                    pianoPlayer = new MultiInstrumentPlayer();
                    window.pianoPlayer = pianoPlayer;
                    console.log('Static piano player initialized');
                }
                
                if (pianoPlayer && pianoPlayer.audioContext && pianoPlayer.audioContext.state === 'suspended') {
                    await pianoPlayer.audioContext.resume();
                    console.log('Audio context resumed');
                }
                
                isAudioInitialized = true;
                console.log('Audio ready for static piano player');
            } catch (error) {
                console.error('Failed to initialize audio:', error);
            }
        }
    }
    
    // Initialize on first user interaction
    document.addEventListener('click', initializeAudio, { once: true });
    
    // Piano key click handlers
    document.addEventListener('click', async (e) => {
        const target = e.target.closest('.piano-key');
        if (target) {
            await initializeAudio();
            if (!isAudioInitialized) return;

            const note = target.getAttribute('data-note');
            if (note && pianoPlayer && pianoPlayer.isLoaded) {
                pianoPlayer.playNote(note, 0.5);
                console.log('Playing note:', note);
                
                // Visual feedback
                target.classList.add('pressed', 'active');
                setTimeout(() => {
                    target.classList.remove('pressed', 'active');
                }, 200);
            }
        }
    });
    
    // Listen for chord updates from the main chord grid
    document.addEventListener('chordGridUpdate', (event) => {
        currentChords = event.detail.chords || [];
        console.log('Static piano received chord update:', currentChords);
    });
    
    // Listen for chord play events
    document.addEventListener('playChord', async (event) => {
        await initializeAudio();
        if (!isAudioInitialized || !pianoPlayer || !pianoPlayer.isLoaded) return;
        
        const { chord, notes } = event.detail;
        console.log('Static piano playing chord:', chord, notes);
        
        // Update display
        const display = document.getElementById('current-chord-display');
        if (display && chord) {
            const chordText = chord.tone + (chord.semitone === 'minor' ? 'm' : (chord.semitone === 'diminished' ? 'dim' : ''));
            display.textContent = chordText;
            display.className = 'text-lg font-bold text-white';
        }
        
        // Play chord
        if (notes && notes.length > 0) {
            pianoPlayer.stopAll();
            setTimeout(() => {
                pianoPlayer.playChordWithSostenuto(notes);
                
                // Highlight keys
                document.querySelectorAll('.piano-key.active').forEach(key => {
                    key.classList.remove('active', 'pressed');
                });
                
                notes.forEach(note => {
                    const key = document.getElementById('key-' + note);
                    if (key) {
                        key.classList.add('active', 'pressed');
                    }
                });
            }, 50);
        }
    });
    
    console.log('Static piano player setup complete');
});
</script>