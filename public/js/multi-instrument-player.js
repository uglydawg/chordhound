// Multi-Instrument Piano Player for ChordHound
class MultiInstrumentPlayer {
    constructor() {
        this.audioContext = null;
        this.masterGainNode = null;
        this.gainNode = null;
        this.isLoaded = false;
        this.instruments = {};
        this.currentInstrument = 'piano';
        this.currentKey = 'C';
        this.currentOctave = 3;
        this.activeNotes = new Map();
        
        this.availableInstruments = {
            'piano': { 
                path: '/audio/chordchord-cinematic-piano.mp3', 
                meta: '/audio/chordchord-cinematic-piano-meta.json',
                name: 'Cinematic Piano'
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
            
            // Load the default piano instrument
            await this.loadInstrument('piano');
            
            this.isLoaded = true;
            console.log('Multi-Instrument Player initialized successfully with samples');
        } catch (error) {
            console.error('Failed to initialize Multi-Instrument Player:', error);
            // Fallback to synthesis if loading fails
            this.isLoaded = true;
        }
    }

    async loadInstrument(instrumentKey) {
        const instrument = this.availableInstruments[instrumentKey];
        if (!instrument) {
            console.error(`Instrument ${instrumentKey} not found`);
            return;
        }

        try {
            console.log(`Loading instrument: ${instrument.name} from ${instrument.path}`);
            
            // Load metadata
            const metaResponse = await fetch(instrument.meta);
            const metadata = await metaResponse.json();
            
            // Load audio file
            const audioResponse = await fetch(instrument.path);
            const arrayBuffer = await audioResponse.arrayBuffer();
            const audioBuffer = await this.audioContext.decodeAudioData(arrayBuffer);
            
            // Store the instrument data
            this.instruments[instrumentKey] = {
                buffer: audioBuffer,
                metadata: metadata,
                samples: metadata.samples || []
            };
            
            console.log(`Successfully loaded ${instrument.name} with ${metadata.samples.length} samples`);
        } catch (error) {
            console.error(`Failed to load instrument ${instrumentKey}:`, error);
            // Don't throw - allow fallback to synthesis
        }
    }

    async switchInstrument(instrumentKey) {
        if (this.availableInstruments[instrumentKey]) {
            this.currentInstrument = instrumentKey;
            console.log(`Switched to instrument: ${instrumentKey}`);
            
            // Load the instrument if not already loaded
            if (!this.instruments[instrumentKey]) {
                await this.loadInstrument(instrumentKey);
            }
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

        // Try to play with samples first, fallback to synthesis
        const instrument = this.instruments[this.currentInstrument];
        if (instrument && instrument.buffer) {
            this.playSample(note, duration, instrument);
        } else {
            this.playSynthesis(note, duration);
        }
    }

    playSample(note, duration, instrument) {
        const now = this.audioContext.currentTime;
        const noteInfo = this.parseNote(note);
        if (!noteInfo) return;

        // Find the best sample for this note
        const sample = this.findBestSample(noteInfo, instrument.samples);
        if (!sample) {
            console.warn(`No sample found for note ${note}, falling back to synthesis`);
            this.playSynthesis(note, duration);
            return;
        }

        // Create buffer source
        const source = this.audioContext.createBufferSource();
        const noteGain = this.audioContext.createGain();
        
        source.buffer = instrument.buffer;
        
        // Calculate playback rate for pitch shifting
        const semitoneRatio = this.calculateSemitoneRatio(noteInfo, sample);
        source.playbackRate.value = semitoneRatio;
        
        // Apply envelope from metadata
        const envelope = instrument.metadata.envelope || { attack: 0.01, release: 0.5 };
        const volume = (instrument.metadata.volume || 1) / 10; // Scale down volume
        
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(volume, now + envelope.attack);
        noteGain.gain.setValueAtTime(volume * 0.8, now + duration - envelope.release);
        noteGain.gain.exponentialRampToValueAtTime(0.001, now + duration);
        
        // Connect
        source.connect(noteGain);
        noteGain.connect(this.gainNode);
        
        // Play the specific sample segment
        source.start(now, sample.offset, Math.min(sample.duration, duration + 0.5));
        source.stop(now + duration);
        
        // Store for tracking
        this.activeNotes.set(note, { source, gain: noteGain });
        
        // Clean up after note ends
        setTimeout(() => {
            this.activeNotes.delete(note);
        }, duration * 1000);
    }

    playSynthesis(note, duration) {
        const freq = this.noteToFrequency(note);
        if (!freq) return;

        const now = this.audioContext.currentTime;
        
        // Create oscillator (fallback)
        const oscillator = this.audioContext.createOscillator();
        const noteGain = this.audioContext.createGain();
        
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(freq, now);
        
        // ADSR envelope
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(0.3, now + 0.01);
        noteGain.gain.exponentialRampToValueAtTime(0.2, now + 0.1);
        noteGain.gain.setValueAtTime(0.2, now + duration - 0.1);
        noteGain.gain.exponentialRampToValueAtTime(0.01, now + duration);
        
        oscillator.connect(noteGain);
        noteGain.connect(this.gainNode);
        
        oscillator.start(now);
        oscillator.stop(now + duration);
        
        this.activeNotes.set(note, { oscillator, gain: noteGain });
        
        setTimeout(() => {
            this.activeNotes.delete(note);
        }, duration * 1000);
    }

    parseNote(note) {
        const match = note.match(/([A-G]#?)(\d+)/);
        if (!match) return null;
        
        const [, noteName, octave] = match;
        return { noteName, octave: parseInt(octave) };
    }

    findBestSample(noteInfo, samples) {
        // Find the closest sample by root note
        let bestSample = null;
        let bestDistance = Infinity;
        
        const targetSemitone = this.noteToSemitone(noteInfo.noteName + noteInfo.octave);
        
        for (const sample of samples) {
            const sampleSemitone = this.noteToSemitone(sample.root);
            const distance = Math.abs(targetSemitone - sampleSemitone);
            
            if (distance < bestDistance) {
                bestDistance = distance;
                bestSample = sample;
            }
        }
        
        return bestSample;
    }

    calculateSemitoneRatio(noteInfo, sample) {
        const targetSemitone = this.noteToSemitone(noteInfo.noteName + noteInfo.octave);
        const sampleSemitone = this.noteToSemitone(sample.root);
        const semitoneDifference = targetSemitone - sampleSemitone;
        
        // Each semitone is a ratio of 2^(1/12)
        return Math.pow(2, semitoneDifference / 12);
    }

    noteToSemitone(note) {
        const noteMap = {
            'C': 0, 'C#': 1, 'D': 2, 'D#': 3, 'E': 4, 'F': 5,
            'F#': 6, 'G': 7, 'G#': 8, 'A': 9, 'A#': 10, 'B': 11
        };
        
        const match = note.match(/([A-G]#?)(\d+)/);
        if (!match) return 0;
        
        const [, noteName, octave] = match;
        const semitone = noteMap[noteName];
        if (semitone === undefined) return 0;
        
        return semitone + (parseInt(octave) * 12);
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
                if (noteData.oscillator) {
                    noteData.oscillator.stop();
                } else if (noteData.source) {
                    noteData.source.stop();
                }
            } catch (e) {
                // Note might have already stopped
            }
        });
        this.activeNotes.clear();
    }

    // Debug method to check loading status
    getStatus() {
        const status = {
            isLoaded: this.isLoaded,
            currentInstrument: this.currentInstrument,
            loadedInstruments: Object.keys(this.instruments),
            audioContextState: this.audioContext?.state
        };

        if (this.instruments[this.currentInstrument]) {
            const instrument = this.instruments[this.currentInstrument];
            status.currentInstrumentData = {
                hasBuffer: !!instrument.buffer,
                bufferDuration: instrument.buffer?.duration,
                sampleCount: instrument.samples?.length || 0,
                samples: instrument.samples?.map(s => s.root) || []
            };
        }

        return status;
    }
}

// Export for use in other scripts
window.MultiInstrumentPlayer = MultiInstrumentPlayer;