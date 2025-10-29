import './bootstrap';
import * as Tone from 'tone';
import { PianoCanvas } from './piano-canvas.js';
import { PianoAudio, pianoAudio } from './piano-audio.js';

// Make Tone available globally
window.Tone = Tone;

// Make PianoCanvas available globally
window.PianoCanvas = PianoCanvas;

// Make PianoAudio available globally
window.PianoAudio = PianoAudio;
window.pianoAudio = pianoAudio;