// Multi-Instrument Piano Player for ChordHound
class MultiInstrumentPlayer {
    constructor() {
        this.audioContext = null;
        this.masterGainNode = null;
        this.gainNode = null;
        this.isLoaded = false;
        this.instruments = {};
        this.currentInstrument = 'cinematic-piano';
        this.currentKey = 'C';
        this.currentOctave = 3;
        this.activeNotes = new Map();
        
        this.availableInstruments = {
            'cinematic-piano': { 
                path: '/audio/instruments/cinematic-piano.mp3', 
                meta: '/audio/instruments/cinematic-piano-meta.json',
                name: 'Cinematic Piano'
            },
            'piano-jazz': { 
                path: '/audio/instruments/piano-jazz.mp3', 
                meta: '/audio/instruments/piano-jazz-meta.json',
                name: 'Jazz Piano'
            },
            'synthwave': { 
                path: '/audio/instruments/synthwave.mp3', 
                meta: '/audio/instruments/synthwave-meta.json',
                name: 'Synthwave'
            },
            'electric-guitar': { 
                path: '/audio/instruments/electric-guitar.mp3', 
                meta: '/audio/instruments/electric-guitar-meta.json',
                name: 'Electric Guitar'
            },
            'strings': { 
                path: '/audio/instruments/strings.mp3', 
                meta: '/audio/instruments/strings-meta.json',
                name: 'Strings'
            }
        };
        
        this.init();
    }

    async init() {
        try {
            // Create audio context
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Create master gain node
            this.masterGainNode = this.audioContext.createGain();
            this.masterGainNode.gain.value = 0.7;
            this.masterGainNode.connect(this.audioContext.destination);
            
            // Create instrument gain node
            this.gainNode = this.audioContext.createGain();
            this.gainNode.gain.value = 0.7;
            this.gainNode.connect(this.masterGainNode);
            
            // For now, we'll use the Web Audio API synthesis
            // In a full implementation, we'd load and parse the audio files
            this.isLoaded = true;
            console.log('Multi-Instrument Player initialized successfully');
        } catch (error) {
            console.error('Failed to initialize Multi-Instrument Player:', error);
        }
    }

    async switchInstrument(instrumentKey) {
        if (this.availableInstruments[instrumentKey]) {
            this.currentInstrument = instrumentKey;
            console.log(`Switched to instrument: ${instrumentKey}`);
            // In a full implementation, we'd load the instrument samples here
        }
    }

    setKey(key) {
        this.currentKey = key;
    }

    setOctave(octave) {
        this.currentOctave = octave;
    }

    // Play a single note
    playNote(note, duration = 0.5) {
        if (!this.audioContext) return;

        const freq = this.noteToFrequency(note);
        if (!freq) return;

        const now = this.audioContext.currentTime;
        
        // Create oscillator
        const oscillator = this.audioContext.createOscillator();
        const noteGain = this.audioContext.createGain();
        
        // Configure based on instrument
        switch (this.currentInstrument) {
            case 'piano-jazz':
                oscillator.type = 'triangle';
                break;
            case 'synthwave':
                oscillator.type = 'sawtooth';
                break;
            case 'electric-guitar':
                oscillator.type = 'square';
                break;
            default:
                oscillator.type = 'sine';
        }
        
        oscillator.frequency.setValueAtTime(freq, now);
        
        // ADSR envelope
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(0.3, now + 0.01); // Attack
        noteGain.gain.exponentialRampToValueAtTime(0.2, now + 0.1); // Decay
        noteGain.gain.setValueAtTime(0.2, now + duration - 0.1); // Sustain
        noteGain.gain.exponentialRampToValueAtTime(0.01, now + duration); // Release
        
        // Connect
        oscillator.connect(noteGain);
        noteGain.connect(this.gainNode);
        
        // Play
        oscillator.start(now);
        oscillator.stop(now + duration);
        
        // Store for tracking
        this.activeNotes.set(note, { oscillator, gain: noteGain });
        
        // Clean up after note ends
        setTimeout(() => {
            this.activeNotes.delete(note);
        }, duration * 1000);
    }

    // Play a chord (multiple notes)
    playChord(notes, duration = 1.0) {
        notes.forEach(note => this.playNote(note, duration));
    }

    // Play chord with extended sustain
    playChordWithSustain(notes, sustainDuration = 4.0) {
        notes.forEach(note => this.playNote(note, sustainDuration));
    }

    // Convert note name to frequency
    noteToFrequency(note) {
        const noteMap = {
            'C': -9, 'C#': -8, 'D': -7, 'D#': -6, 'E': -5, 'F': -4,
            'F#': -3, 'G': -2, 'G#': -1, 'A': 0, 'A#': 1, 'B': 2
        };
        
        const match = note.match(/([A-G]#?)(\d+)/);
        if (!match) return null;
        
        const [, noteName, octave] = match;
        const semitone = noteMap[noteName];
        if (semitone === undefined) return null;
        
        // A4 = 440Hz, calculate frequency
        const a4 = 440;
        const halfSteps = semitone + (parseInt(octave) - 4) * 12;
        return a4 * Math.pow(2, halfSteps / 12);
    }

    // Stop all playing notes
    stopAll() {
        this.activeNotes.forEach((noteData, note) => {
            try {
                noteData.oscillator.stop();
            } catch (e) {
                // Note might have already stopped
            }
        });
        this.activeNotes.clear();
    }
}

// Export for use in other scripts
window.MultiInstrumentPlayer = MultiInstrumentPlayer;