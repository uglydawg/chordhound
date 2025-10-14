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

        // Check if already loading to prevent duplicate requests
        if (this.loadingPromises && this.loadingPromises[instrumentKey]) {
            console.log(`Already loading ${instrumentKey}, waiting...`);
            return this.loadingPromises[instrumentKey];
        }

        if (!this.loadingPromises) {
            this.loadingPromises = {};
        }

        // Create a loading promise
        this.loadingPromises[instrumentKey] = this._loadInstrumentData(instrumentKey, instrument);
        
        try {
            await this.loadingPromises[instrumentKey];
        } finally {
            delete this.loadingPromises[instrumentKey];
        }
    }
    
    async _loadInstrumentData(instrumentKey, instrument) {
        try {
            console.log(`Loading instrument: ${instrument.name} from ${instrument.path}`);
            
            // Try to use cached data first
            const cacheAvailable = 'caches' in window;
            let metaResponse, audioResponse;
            
            if (cacheAvailable) {
                const cache = await caches.open('chordhound-audio-v1');
                
                // Try cache first for metadata
                const cachedMeta = await cache.match(instrument.meta);
                metaResponse = cachedMeta || await fetch(instrument.meta);
                if (!cachedMeta && metaResponse.ok) {
                    cache.put(instrument.meta, metaResponse.clone());
                }
                
                // Try cache first for audio
                const cachedAudio = await cache.match(instrument.path);
                audioResponse = cachedAudio || await fetch(instrument.path);
                if (!cachedAudio && audioResponse.ok) {
                    cache.put(instrument.path, audioResponse.clone());
                }
            } else {
                // Fallback to regular fetch if cache API not available
                metaResponse = await fetch(instrument.meta);
                audioResponse = await fetch(instrument.path);
            }
            
            const metadata = await metaResponse.json();
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
        
        // Ensure all time values are positive and properly ordered
        const attackTime = now + envelope.attack;
        const sustainTime = Math.max(attackTime + 0.01, now + duration - envelope.release);
        const endTime = now + duration;
        
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(volume, attackTime);
        noteGain.gain.setValueAtTime(volume * 0.8, sustainTime);
        noteGain.gain.exponentialRampToValueAtTime(0.001, endTime);
        
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
        
        // ADSR envelope with safe time calculations
        const attackTime = now + 0.01;
        const decayTime = now + 0.1;
        const sustainTime = Math.max(decayTime + 0.01, now + duration - 0.1);
        const endTime = now + duration;
        
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(0.3, attackTime);
        noteGain.gain.exponentialRampToValueAtTime(0.2, decayTime);
        noteGain.gain.setValueAtTime(0.2, sustainTime);
        noteGain.gain.exponentialRampToValueAtTime(0.01, endTime);
        
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

    // Play chord with sostenuto pedal behavior (notes continue until stopped)
    playChordWithSostenuto(notes) {
        notes.forEach(note => this.playNoteWithSostenuto(note));
    }

    // Play a single note with sostenuto behavior (no automatic stop)
    playNoteWithSostenuto(note) {
        if (!this.audioContext) return;

        // Try to play with samples first, fallback to synthesis
        const instrument = this.instruments[this.currentInstrument];
        if (instrument && instrument.buffer) {
            this.playSampleWithSostenuto(note, instrument);
        } else {
            this.playSynthesisWithSostenuto(note);
        }
    }

    playSampleWithSostenuto(note, instrument) {
        const now = this.audioContext.currentTime;
        const noteInfo = this.parseNote(note);
        if (!noteInfo) return;

        // Find the best sample for this note
        const sample = this.findBestSample(noteInfo, instrument.samples);
        if (!sample) {
            console.warn(`No sample found for note ${note}, falling back to synthesis`);
            this.playSynthesisWithSostenuto(note);
            return;
        }

        // Create buffer source
        const source = this.audioContext.createBufferSource();
        const noteGain = this.audioContext.createGain();
        
        source.buffer = instrument.buffer;
        source.loop = true; // Enable looping for sostenuto
        
        // Calculate playback rate for pitch shifting
        const semitoneRatio = this.calculateSemitoneRatio(noteInfo, sample);
        source.playbackRate.value = semitoneRatio;
        
        // Apply envelope from metadata with sustained level
        const envelope = instrument.metadata.envelope || { attack: 0.01, release: 0.5 };
        const volume = (instrument.metadata.volume || 1) / 10; // Scale down volume
        
        // Attack phase
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(volume, now + envelope.attack);
        // Sustain at 80% volume indefinitely (no automatic release)
        noteGain.gain.setValueAtTime(volume * 0.8, now + envelope.attack + 0.01);
        
        // Connect
        source.connect(noteGain);
        noteGain.connect(this.gainNode);
        
        // Start the looped sample
        source.start(now, sample.offset, sample.duration);
        
        // Store for tracking (no automatic cleanup)
        this.activeNotes.set(note, { source, gain: noteGain, isSostenuto: true });
    }

    playSynthesisWithSostenuto(note) {
        const freq = this.noteToFrequency(note);
        if (!freq) return;

        const now = this.audioContext.currentTime;
        
        // Create oscillator for sustained playback
        const oscillator = this.audioContext.createOscillator();
        const noteGain = this.audioContext.createGain();
        
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(freq, now);
        
        // Sostenuto envelope - attack then sustain indefinitely
        noteGain.gain.setValueAtTime(0, now);
        noteGain.gain.linearRampToValueAtTime(0.3, now + 0.01);
        noteGain.gain.exponentialRampToValueAtTime(0.2, now + 0.1);
        // Hold at sustain level (no automatic release)
        
        oscillator.connect(noteGain);
        noteGain.connect(this.gainNode);
        
        oscillator.start(now);
        // No automatic stop - will be stopped manually
        
        this.activeNotes.set(note, { oscillator, gain: noteGain, isSostenuto: true });
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
        const now = this.audioContext ? this.audioContext.currentTime : 0;
        
        this.activeNotes.forEach((noteData, note) => {
            try {
                if (noteData.isSostenuto) {
                    // Apply smooth release for sostenuto notes
                    if (noteData.gain) {
                        const currentGain = noteData.gain.gain.value;
                        noteData.gain.gain.cancelScheduledValues(now);
                        noteData.gain.gain.setValueAtTime(currentGain, now);
                        noteData.gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    }
                    
                    // Stop the audio source after release
                    setTimeout(() => {
                        if (noteData.oscillator) {
                            noteData.oscillator.stop();
                        } else if (noteData.source) {
                            noteData.source.stop();
                        }
                    }, 300);
                } else {
                    // Immediate stop for regular notes
                    if (noteData.oscillator) {
                        noteData.oscillator.stop();
                    } else if (noteData.source) {
                        noteData.source.stop();
                    }
                }
            } catch (e) {
                // Note might have already stopped
                console.warn('Error stopping note:', e);
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