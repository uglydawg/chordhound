<div class="midi-player flex items-center justify-between space-x-4">
    {{-- Transport Controls --}}
    <div class="flex items-center space-x-2">
        {{-- Play/Pause Button --}}
        <button 
            wire:click="togglePlayback"
            class="transport-button group"
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
            class="transport-button group"
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
    
    {{-- Tempo Control --}}
    <div class="flex items-center space-x-3">
        <label class="text-sm text-secondary">BPM</label>
        <input 
            type="number" 
            wire:model.lazy="tempo"
            wire:change="updateTempo($event.target.value)"
            min="60" 
            max="200" 
            class="w-16 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-sm text-primary focus:border-blue-500 focus:outline-none"
        >
        <span class="text-lg font-bold text-primary">{{ $tempo }}</span>
    </div>
    
    {{-- Key Signature --}}
    <div class="flex items-center space-x-2">
        <span class="text-sm text-secondary">Key</span>
        <div class="bg-zinc-800 border border-zinc-700 rounded px-3 py-1">
            <span class="text-primary font-medium">C Major</span>
        </div>
    </div>
</div>

{{-- JavaScript for audio playback --}}
<script>
document.addEventListener('livewire:initialized', () => {
    let synth = null;
    let sequence = null;
    
    // Initialize Tone.js
    if (typeof Tone !== 'undefined') {
        synth = new Tone.PolySynth(Tone.Synth).toDestination();
        synth.set({
            oscillator: { type: 'triangle' },
            envelope: {
                attack: 0.005,
                decay: 0.1,
                sustain: 0.3,
                release: 1
            }
        });
    }
    
    // Listen for playback events
    Livewire.on('toggle-playback', ({ isPlaying }) => {
        if (!synth) return;
        
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
    
    function startPlayback() {
        // Get current chords from the chord selector
        const chords = @this.chords;
        const tempo = @this.tempo;
        
        Tone.Transport.bpm.value = tempo;
        
        // Create chord progression sequence
        const chordNotes = [];
        Object.values(chords).forEach(chord => {
            if (chord.tone) {
                // Convert chord to notes (simplified)
                const notes = getChordNotes(chord.tone, chord.semitone, chord.inversion);
                chordNotes.push(notes);
            }
        });
        
        if (chordNotes.length > 0) {
            let chordIndex = 0;
            sequence = new Tone.Loop((time) => {
                synth.triggerAttackRelease(chordNotes[chordIndex % chordNotes.length], '2n', time);
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
        // Simplified chord to notes conversion
        const noteMap = {
            'C': ['C4', 'E4', 'G4'],
            'D': ['D4', 'F#4', 'A4'],
            'E': ['E4', 'G#4', 'B4'],
            'F': ['F4', 'A4', 'C5'],
            'G': ['G4', 'B4', 'D5'],
            'A': ['A4', 'C#5', 'E5'],
            'B': ['B4', 'D#5', 'F#5']
        };
        
        let notes = noteMap[root] || ['C4', 'E4', 'G4'];
        
        // Apply minor modification
        if (type === 'minor' && notes[1]) {
            // Lower the third by a semitone
            notes[1] = notes[1].replace('#', '').replace(/([A-G])/, (match) => {
                const noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const idx = noteOrder.indexOf(match);
                return noteOrder[idx === 0 ? 11 : idx - 1];
            });
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
});
</script>