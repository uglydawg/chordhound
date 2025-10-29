/**
 * Piano Audio Manager using Tone.js
 * Handles piano sound playback for individual notes and chords using real piano samples
 */

export class PianoAudio {
    constructor() {
        this.sampler = null;
        this.gainNode = null;
        this.isInitialized = false;
        this.currentNotes = [];
        this.volume = 0.7; // Default 70% volume
    }

    /**
     * Convert standard note format (C#4, D4, etc.) to sample filename format (Cs4, D4, etc.)
     */
    noteToSampleFormat(note) {
        return note.replace('#', 's');
    }

    /**
     * Generate the sample map for Tone.js Sampler
     * Maps Tone.js note format (C#4) to our sample filenames (Cs4.mp3)
     */
    generateSampleMap() {
        const baseUrl = '/audio/piano/';
        // Tone.js expects note names with # for sharps
        const notes = [
            { tone: 'C', file: 'C' },
            { tone: 'C#', file: 'Cs' },
            { tone: 'D', file: 'D' },
            { tone: 'D#', file: 'Ds' },
            { tone: 'E', file: 'E' },
            { tone: 'F', file: 'F' },
            { tone: 'F#', file: 'Fs' },
            { tone: 'G', file: 'G' },
            { tone: 'G#', file: 'Gs' },
            { tone: 'A', file: 'A' },
            { tone: 'A#', file: 'As' },
            { tone: 'B', file: 'B' }
        ];

        const sampleMap = {};

        // Generate map for octaves 1-7
        for (let octave = 1; octave <= 7; octave++) {
            notes.forEach(({ tone, file }) => {
                const toneNote = `${tone}${octave}`; // C#4 (Tone.js format)
                const fileName = `${file}${octave}`; // Cs4 (our file format)
                sampleMap[toneNote] = `${baseUrl}${fileName}.mp3`;
            });
        }

        // Add C8 (highest note)
        sampleMap['C8'] = `${baseUrl}C8.mp3`;

        return sampleMap;
    }

    async initialize() {
        if (this.isInitialized) return;

        try {
            console.log('Initializing PianoAudio with real piano samples...');

            const sampleMap = this.generateSampleMap();
            console.log('Sample map generated with', Object.keys(sampleMap).length, 'notes');
            console.log('First sample URL:', sampleMap['C4']);

            // Create a gain node for volume control
            this.gainNode = new window.Tone.Gain(this.volume).toDestination();

            // Create sampler with all piano samples, connect to gain node
            this.sampler = new window.Tone.Sampler({
                urls: sampleMap,
                release: 1,
                onload: () => {
                    console.log('✅ Piano samples loaded successfully');
                },
                onerror: (error) => {
                    console.error('❌ Failed to load piano samples:', error);
                }
            }).connect(this.gainNode);

            this.isInitialized = true;
            console.log('PianoAudio initialized (samples loading in background)');
        } catch (error) {
            console.error('Failed to initialize PianoAudio:', error);
        }
    }

    async ensureStarted() {
        // Ensure Tone.js audio context is started (required after user interaction)
        if (window.Tone.context.state !== 'running') {
            await window.Tone.start();
            console.log('Tone.js audio context started');
        }
    }

    async playNote(note, duration = '8n') {
        await this.initialize();
        await this.ensureStarted();

        try {
            // Stop any currently playing notes before playing the new one
            this.stopAll();

            // Play the individual note
            this.sampler.triggerAttackRelease(note, duration);
            console.log('🎹 Played note:', note);
        } catch (error) {
            console.error('Failed to play note:', note, error);
        }
    }

    async playChord(notes, duration = '2n') {
        await this.initialize();
        await this.ensureStarted();

        try {
            // IMPORTANT: Stop any currently playing notes before playing new chord
            console.log('🛑 Stopping previous chord...');
            this.stopAll();

            // Small delay to ensure clean cutoff (helps prevent audio artifacts)
            await new Promise(resolve => setTimeout(resolve, 10));

            // Play the new chord (notes already in correct format)
            this.sampler.triggerAttackRelease(notes, duration);
            this.currentNotes = [...notes];
            console.log('🎵 Playing new chord:', notes);
        } catch (error) {
            console.error('Failed to play chord:', notes, error);
        }
    }

    stopAll() {
        if (this.sampler) {
            // Immediately release all currently playing notes
            this.sampler.releaseAll();

            // Also trigger immediate silence on any lingering notes
            // This ensures a clean cutoff without audio artifacts
            if (this.currentNotes.length > 0) {
                this.currentNotes.forEach(note => {
                    try {
                        // Trigger attack and immediate release for clean cutoff
                        this.sampler.triggerRelease(note, window.Tone.now());
                    } catch (e) {
                        // Ignore errors for notes that aren't playing
                    }
                });
            }

            this.currentNotes = [];
            console.log('🛑 All notes stopped');
        }
    }

    async playArpeggio(notes, noteLength = 0.2, gap = 0.1) {
        await this.initialize();
        await this.ensureStarted();

        try {
            const now = window.Tone.now();
            notes.forEach((note, index) => {
                const startTime = now + (index * (noteLength + gap));
                this.sampler.triggerAttackRelease(note, noteLength, startTime);
            });
            console.log('🎼 Played arpeggio:', notes);
        } catch (error) {
            console.error('Failed to play arpeggio:', notes, error);
        }
    }

    /**
     * Set volume with x-squared curve for better perception
     * @param {number} value - Linear volume value from 0 to 1
     */
    setVolume(value) {
        // Clamp value between 0 and 1
        const linearValue = Math.max(0, Math.min(1, value));

        // Apply x-squared curve for better perceived volume
        const curvedValue = linearValue * linearValue;

        if (this.gainNode) {
            this.gainNode.gain.value = curvedValue;
            this.volume = linearValue;
            console.log(`🔊 Volume set to ${Math.round(linearValue * 100)}% (gain: ${curvedValue.toFixed(2)})`);
        }
    }

    getVolume() {
        return this.volume;
    }

    destroy() {
        this.stopAll();
        if (this.sampler) {
            this.sampler.dispose();
        }
        this.isInitialized = false;
    }
}

// Create a singleton instance
export const pianoAudio = new PianoAudio();
