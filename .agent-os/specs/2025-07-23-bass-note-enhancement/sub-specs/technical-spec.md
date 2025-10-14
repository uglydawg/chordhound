# Technical Specification

This is the technical specification for the spec detailed in @.agent-os/specs/2025-07-23-bass-note-enhancement/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Technical Requirements

### Bass Note Calculation

- Extract the root note from each chord regardless of inversion
- Calculate the bass note frequency as root note minus 24 semitones (2 octaves)
- Handle edge cases where bass notes might fall below piano range (A0 = 27.5 Hz)
- Maintain consistent bass note even when chord is inverted

### Audio Synthesis Integration

- Modify existing Tone.js implementation to include bass voice
- Create separate oscillator or sampler instance for bass notes
- Ensure bass and chord voices share the same audio context
- Implement proper voice allocation to prevent audio dropouts
- Use appropriate envelope settings for natural bass sound

### Volume and Mix Balance

- Set bass note velocity/volume to approximately 70-80% of chord volume
- Implement frequency-based volume compensation (Fletcher-Munson curves)
- Ensure bass doesn't mask chord clarity in mid frequencies
- Test balance across different playback systems (headphones, speakers)

### Timing and Synchronization

- Trigger bass note at exact same time as chord notes
- Ensure no perceptible delay between bass and chord attack
- Handle note-off events simultaneously for bass and chord
- Maintain timing accuracy during rapid chord changes

## Approach Options

**Option A: Modify Existing Chord Playback Method**
- Pros: Minimal code changes, maintains current architecture
- Cons: Tightly couples bass logic with chord playback

**Option B: Create Separate Bass Voice Handler** (Selected)
- Pros: Clean separation of concerns, easier to test and modify
- Cons: Requires coordination between two audio components

**Rationale:** Option B provides better modularity and allows for future enhancements like bass patterns or custom bass notes without affecting core chord playback logic.

## Implementation Details

### Code Architecture

```javascript
// New BassPlayer class to handle bass note generation
class BassPlayer {
  constructor(synth) {
    this.synth = synth;
    this.volume = -12; // dB relative to main volume
  }

  playBassNote(rootNote, duration) {
    const bassNote = this.calculateBassNote(rootNote);
    if (this.isValidBassNote(bassNote)) {
      this.synth.triggerAttackRelease(bassNote, duration);
    }
  }

  calculateBassNote(rootNote) {
    // Convert note to MIDI number, subtract 24, convert back
    const midiNumber = Tone.Frequency(rootNote).toMidi();
    return Tone.Frequency(midiNumber - 24, "midi").toNote();
  }

  isValidBassNote(note) {
    const frequency = Tone.Frequency(note).toFrequency();
    return frequency >= 27.5; // A0 lowest piano note
  }
}
```

### Integration with ChordPlayer

```javascript
// Modify existing playChord method
async playChord(chordNotes, rootNote, duration = "2n") {
  // Existing chord playback
  this.polySynth.triggerAttackRelease(chordNotes, duration);
  
  // New bass note playback
  if (this.bassPlayer && rootNote) {
    this.bassPlayer.playBassNote(rootNote, duration);
  }
}
```

### Tone.js Configuration

```javascript
// Bass synth configuration
const bassSynth = new Tone.Synth({
  oscillator: {
    type: "sine" // Pure tone for bass
  },
  envelope: {
    attack: 0.02,
    decay: 0.1,
    sustain: 0.9,
    release: 0.5
  }
}).toDestination();

bassSynth.volume.value = -12; // Reduce volume relative to main
```

## Performance Considerations

- Initialize bass synth once on page load, not per playback
- Use efficient note calculation without string manipulation
- Implement note pooling if rapid playback causes issues
- Monitor CPU usage during chord progression playback
- Consider Web Audio API scheduling for precise timing

## Edge Cases

### Low Frequency Handling
- Notes below A0 (27.5 Hz) should not be played
- Implement octave wrapping for extremely low roots
- Test with all possible root notes in the system

### Audio Context State
- Handle suspended audio context gracefully
- Ensure bass synth resumes when main synth resumes
- Coordinate user interaction requirements for audio

### Rapid Chord Changes
- Prevent bass note overlap during fast progressions
- Implement proper note-off handling
- Test with fastest possible chord switching

## Browser Compatibility

- Test Web Audio API support across browsers
- Verify low frequency playback on different devices
- Ensure mobile devices can reproduce bass frequencies
- Test with Bluetooth speakers and headphones

## Configuration Options

While not in initial scope, consider architecture that allows future:
- Bass octave selection (1 or 2 octaves down)
- Bass note on/off toggle
- Bass volume control
- Alternative bass note selection (5th, etc.)

## External Dependencies

No new dependencies required - existing Tone.js library supports all needed functionality.