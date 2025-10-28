// Multi-Instrument Piano Player for ChordHound
// Uses Tone.js with locally hosted piano samples from tonejs-instruments
class MultiInstrumentPlayer {
    constructor() {
        this.sampler = null;
        this.isLoaded = false;
        this.activeNotes = new Map();

        this.init();
    }

    async init() {
        try {
            console.log('Initializing Multi-Instrument Player with local piano samples...');

            // Build sample map for Tone.Sampler
            // tonejs-instruments uses 'As', 'Cs', 'Ds', 'Fs', 'Gs' for sharps
            const sampleMap = {};

            // Natural notes
            const naturalNotes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
            // Sharp notes (using 's' suffix)
            const sharpNotes = ['Cs', 'Ds', 'Fs', 'Gs', 'As'];

            // C1 to C8 for natural notes
            for (const note of naturalNotes) {
                const startOctave = note === 'C' ? 1 : 1;
                const endOctave = note === 'C' ? 8 : 7;

                for (let octave = startOctave; octave <= endOctave; octave++) {
                    // Convert Cs4 format to C#4 format for Tone.js
                    const toneNote = `${note}${octave}`;
                    sampleMap[toneNote] = `/audio/piano/${note}${octave}.mp3`;
                }
            }

            // Sharp notes (C#1 to G#7)
            for (const note of sharpNotes) {
                for (let octave = 1; octave <= 7; octave++) {
                    // Convert from tonejs-instruments format (Cs4) to Tone.js format (C#4)
                    const baseNote = note[0];
                    const toneNote = `${baseNote}#${octave}`;
                    sampleMap[toneNote] = `/audio/piano/${note}${octave}.mp3`;
                }
            }

            // Create Tone.js Sampler with all piano samples
            this.sampler = new Tone.Sampler({
                urls: sampleMap,
                onload: () => {
                    this.isLoaded = true;
                    console.log('Piano samples loaded successfully!');
                }
            }).toDestination();

        } catch (error) {
            console.error('Failed to initialize Multi-Instrument Player:', error);
            this.isLoaded = false;
        }
    }

    // Play a single note
    playNote(note, duration = 0.5) {
        if (!this.isLoaded || !this.sampler) {
            console.warn('Piano not loaded yet');
            return;
        }

        try {
            // Tone.Sampler.triggerAttackRelease(note, duration)
            this.sampler.triggerAttackRelease(note, duration);

            // Track the note
            const noteId = `${note}-${Date.now()}`;
            this.activeNotes.set(noteId, note);

            // Remove from tracking after duration
            setTimeout(() => {
                this.activeNotes.delete(noteId);
            }, duration * 1000);

        } catch (error) {
            console.error('Error playing note:', error);
        }
    }

    // Play a chord (multiple notes simultaneously)
    playChord(notes, duration = 1.0) {
        if (!this.isLoaded || !this.sampler) {
            console.warn('Piano not loaded yet');
            return;
        }

        if (!Array.isArray(notes) || notes.length === 0) {
            console.warn('Invalid notes array for chord');
            return;
        }

        try {
            // Play all notes at once
            notes.forEach(note => {
                this.sampler.triggerAttackRelease(note, duration);

                // Track the note
                const noteId = `${note}-${Date.now()}`;
                this.activeNotes.set(noteId, note);
            });

            // Clean up tracking after duration
            setTimeout(() => {
                for (const [id, n] of this.activeNotes.entries()) {
                    if (notes.includes(n)) {
                        this.activeNotes.delete(id);
                    }
                }
            }, duration * 1000);

        } catch (error) {
            console.error('Error playing chord:', error);
        }
    }

    // Play chord with sustain (longer duration)
    playChordWithSustain(notes, duration = 2.0) {
        this.playChord(notes, duration);
    }

    // Play chord with sostenuto (indefinite sustain)
    playChordWithSostenuto(notes) {
        if (!this.isLoaded || !this.sampler) {
            console.warn('Piano not loaded yet');
            return;
        }

        if (!Array.isArray(notes) || notes.length === 0) {
            console.warn('Invalid notes array for chord');
            return;
        }

        try {
            // Trigger attack only (no release)
            const now = Tone.now();
            notes.forEach(note => {
                this.sampler.triggerAttack(note, now);
                this.activeNotes.set(`${note}-sostenuto`, note);
            });

        } catch (error) {
            console.error('Error playing chord with sostenuto:', error);
        }
    }

    // Stop all currently playing notes
    stopAll() {
        if (!this.sampler) return;

        try {
            // Release all notes
            const uniqueNotes = new Set(this.activeNotes.values());
            uniqueNotes.forEach(note => {
                this.sampler.triggerRelease(note);
            });
            this.activeNotes.clear();

        } catch (error) {
            console.error('Error stopping all notes:', error);
        }
    }

    // Get current player status
    getStatus() {
        return {
            isLoaded: this.isLoaded,
            currentInstrument: 'piano',
            loadedInstruments: ['piano'],
            audioContextState: Tone.context.state,
            currentInstrumentData: {
                name: 'Acoustic Piano',
                source: 'tonejs-instruments (local)',
                sampleCount: 85
            }
        };
    }
}

// Make it available globally
window.MultiInstrumentPlayer = MultiInstrumentPlayer;
