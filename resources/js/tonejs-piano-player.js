// ToneJS Piano Player using @tonejs/piano (Salamander Grand Piano samples)
import { Piano } from '@tonejs/piano';
import * as Tone from 'tone';

class ToneJsPianoPlayer {
    constructor() {
        this.piano = null;
        this.isLoaded = false;
        this.activeNotes = new Map(); // Track active notes for stopAll()
        this.sustainedNotes = new Set(); // Notes held by sustain pedal
        this.sustainActive = false;

        this.init();
    }

    async init() {
        try {
            console.log('Initializing ToneJS Piano with Salamander Grand Piano samples...');

            // Create piano with 5 velocity levels for good quality/performance balance
            this.piano = new Piano({
                velocities: 5
            });

            // Connect to audio destination
            this.piano.toDestination();

            // Load the piano samples
            await this.piano.load();

            this.isLoaded = true;
            console.log('ToneJS Piano loaded successfully!');
        } catch (error) {
            console.error('Failed to initialize ToneJS Piano:', error);
            this.isLoaded = false;
        }
    }

    /**
     * Play a single note
     * @param {string} note - Note to play (e.g., "C4", "F#3")
     * @param {number} duration - Duration in seconds
     * @param {number} velocity - Velocity (0-1), defaults to 0.8
     */
    playNote(note, duration = 1.0, velocity = 0.8) {
        if (!this.isLoaded || !this.piano) {
            console.warn('Piano not loaded yet');
            return;
        }

        try {
            // Start the note
            this.piano.keyDown({ note, velocity, time: Tone.now() });

            // Track the note
            const noteId = `${note}-${Date.now()}`;
            this.activeNotes.set(noteId, note);

            // Schedule note off after duration
            const releaseTime = Tone.now() + duration;
            setTimeout(() => {
                this.piano.keyUp({ note, time: releaseTime });
                this.activeNotes.delete(noteId);
            }, duration * 1000);

        } catch (error) {
            console.error('Error playing note:', error);
        }
    }

    /**
     * Play multiple notes as a chord
     * @param {array} notes - Array of notes (e.g., ["C4", "E4", "G4"])
     * @param {number} duration - Duration in seconds
     * @param {number} velocity - Velocity (0-1), defaults to 0.8
     */
    playChord(notes, duration = 1.0, velocity = 0.8) {
        if (!this.isLoaded || !this.piano) {
            console.warn('Piano not loaded yet');
            return;
        }

        if (!Array.isArray(notes) || notes.length === 0) {
            console.warn('Invalid notes array for chord');
            return;
        }

        try {
            const now = Tone.now();

            // Play all notes simultaneously
            notes.forEach(note => {
                this.piano.keyDown({ note, velocity, time: now });

                // Track the note
                const noteId = `${note}-${Date.now()}`;
                this.activeNotes.set(noteId, note);
            });

            // Schedule note off for all notes after duration
            const releaseTime = now + duration;
            setTimeout(() => {
                notes.forEach(note => {
                    this.piano.keyUp({ note, time: releaseTime });
                });
                // Clean up tracked notes
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

    /**
     * Play chord with sustain pedal (holds notes longer)
     * @param {array} notes - Array of notes
     * @param {number} duration - Duration in seconds
     * @param {number} velocity - Velocity (0-1)
     */
    playChordWithSustain(notes, duration = 2.0, velocity = 0.8) {
        if (!this.isLoaded || !this.piano) {
            console.warn('Piano not loaded yet');
            return;
        }

        try {
            const now = Tone.now();

            // Activate sustain pedal
            this.piano.pedalDown({ time: now });
            this.sustainActive = true;

            // Play the chord
            notes.forEach(note => {
                this.piano.keyDown({ note, velocity, time: now });
                this.sustainedNotes.add(note);
            });

            // Release sustain after duration
            setTimeout(() => {
                this.piano.pedalUp({ time: Tone.now() });
                this.sustainActive = false;
                notes.forEach(note => {
                    this.piano.keyUp({ note });
                    this.sustainedNotes.delete(note);
                });
            }, duration * 1000);

        } catch (error) {
            console.error('Error playing chord with sustain:', error);
        }
    }

    /**
     * Play chord with sostenuto (selective sustain)
     * @param {array} notes - Array of notes
     */
    playChordWithSostenuto(notes) {
        // Sostenuto sustains indefinitely until explicitly released
        if (!this.isLoaded || !this.piano) {
            console.warn('Piano not loaded yet');
            return;
        }

        try {
            const now = Tone.now();

            // Activate sustain pedal for sostenuto effect
            this.piano.pedalDown({ time: now });
            this.sustainActive = true;

            notes.forEach(note => {
                this.piano.keyDown({ note, velocity: 0.8, time: now });
                this.sustainedNotes.add(note);
            });

        } catch (error) {
            console.error('Error playing chord with sostenuto:', error);
        }
    }

    /**
     * Stop all currently playing notes
     */
    stopAll() {
        if (!this.piano) return;

        try {
            // Release sustain pedal if active
            if (this.sustainActive) {
                this.piano.pedalUp({ time: Tone.now() });
                this.sustainActive = false;
            }

            // Release all sustained notes
            this.sustainedNotes.forEach(note => {
                this.piano.keyUp({ note });
            });
            this.sustainedNotes.clear();

            // Release all tracked active notes
            const uniqueNotes = new Set(this.activeNotes.values());
            uniqueNotes.forEach(note => {
                this.piano.keyUp({ note });
            });
            this.activeNotes.clear();

        } catch (error) {
            console.error('Error stopping all notes:', error);
        }
    }

    /**
     * Get current player status
     */
    getStatus() {
        return {
            loaded: this.isLoaded,
            activeNotes: this.activeNotes.size,
            sustainActive: this.sustainActive
        };
    }
}

export default ToneJsPianoPlayer;
