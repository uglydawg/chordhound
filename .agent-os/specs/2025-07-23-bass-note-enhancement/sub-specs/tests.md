# Tests Specification

This is the tests coverage details for the spec detailed in @.agent-os/specs/2025-07-23-bass-note-enhancement/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Test Coverage

### Unit Tests

**BassPlayer Class**
- Test calculateBassNote returns note 24 semitones lower
- Test isValidBassNote returns false for notes below A0
- Test isValidBassNote returns true for valid bass range
- Test volume is set correctly relative to main volume
- Test bass note calculation for all 12 root notes

**Bass Note Calculation**
- Test C4 root produces C2 bass note
- Test A2 root produces A0 bass note (edge case)
- Test G1 root handles below-range gracefully
- Test enharmonic equivalents (C# vs Db) produce same bass

**Integration with ChordPlayer**
- Test playChord triggers both chord and bass notes
- Test bass note uses chord's root regardless of inversion
- Test bass note duration matches chord duration
- Test bass player initialization and teardown

### Integration Tests

**Audio Playback Flow**
- Test simultaneous trigger of chord and bass notes
- Test no perceptible delay between attacks
- Test proper note-off for both voices
- Test audio context handling for both synths

**Chord Progression Playback**
- Test bass notes play for each chord in progression
- Test smooth transitions between bass notes
- Test no audio artifacts during rapid changes
- Test progression loop includes bass throughout

**Volume Balance**
- Test bass volume relative to chord volume
- Test frequency compensation maintains clarity
- Test balance across different chord voicings
- Test no clipping at maximum volumes

### Feature Tests

**User Interaction Flow**
- Click chord tile triggers chord with bass
- Play button includes bass for all chords
- Stop button silences both chord and bass
- Page reload maintains bass functionality

**Cross-Browser Audio**
- Test bass playback in Chrome
- Test bass playback in Firefox
- Test bass playback in Safari
- Test mobile browser bass reproduction

**Performance Tests**
- Measure latency between trigger and sound
- Test CPU usage during progression playback
- Verify no memory leaks during extended use
- Test performance with rapid chord switching

### Audio Quality Tests

**Frequency Response**
- Verify bass note frequencies are accurate
- Test low frequency reproduction limits
- Ensure no aliasing or distortion
- Verify proper envelope application

**Mixing and Balance**
- Test bass doesn't overpower chord
- Test chord remains clear with bass
- Test stereo image remains centered
- Test no phase cancellation issues

### Edge Case Tests

**Extreme Range Handling**
- Test lowest possible chord roots
- Test automatic octave wrapping if needed
- Test system behavior at frequency limits
- Test graceful degradation on limited speakers

**Rapid Interaction**
- Test multiple rapid chord clicks
- Test quick progression start/stop
- Test chord changes during playback
- Test no hanging notes or audio glitches

## Mocking Requirements

**Tone.js Mocking**
- Mock Tone.Synth for unit tests
- Mock frequency conversion methods
- Mock triggerAttackRelease calls
- Verify correct parameters passed

**Audio Context Mocking**
- Mock Web Audio API for unit tests
- Mock audio context state changes
- Test suspended context handling
- Verify resume/suspend coordination

**User Interaction Mocking**
- Mock click events for audio context
- Mock timing for simultaneous triggers
- Test event ordering and propagation

## Test Implementation Examples

### Jest/Vitest Test for Bass Calculation

```javascript
describe('BassPlayer', () => {
  let bassPlayer;
  let mockSynth;

  beforeEach(() => {
    mockSynth = {
      triggerAttackRelease: jest.fn()
    };
    bassPlayer = new BassPlayer(mockSynth);
  });

  it('calculates bass note two octaves below root', () => {
    expect(bassPlayer.calculateBassNote('C4')).toBe('C2');
    expect(bassPlayer.calculateBassNote('G3')).toBe('G1');
    expect(bassPlayer.calculateBassNote('A4')).toBe('A2');
  });

  it('validates bass notes within piano range', () => {
    expect(bassPlayer.isValidBassNote('C2')).toBe(true);
    expect(bassPlayer.isValidBassNote('A0')).toBe(true);
    expect(bassPlayer.isValidBassNote('G0')).toBe(false);
  });

  it('triggers bass note with correct parameters', () => {
    bassPlayer.playBassNote('C4', '4n');
    expect(mockSynth.triggerAttackRelease).toHaveBeenCalledWith('C2', '4n');
  });
});
```

### Integration Test for Chord + Bass Playback

```javascript
it('plays chord and bass notes simultaneously', async () => {
  const chordPlayer = new ChordPlayer();
  const bassSpy = jest.spyOn(chordPlayer.bassPlayer, 'playBassNote');
  const chordSpy = jest.spyOn(chordPlayer.polySynth, 'triggerAttackRelease');

  await chordPlayer.playChord(['C4', 'E4', 'G4'], 'C4', '2n');

  expect(chordSpy).toHaveBeenCalledWith(['C4', 'E4', 'G4'], '2n');
  expect(bassSpy).toHaveBeenCalledWith('C4', '2n');
  expect(bassSpy).toHaveBeenCalledTimes(1);
});
```

### Livewire Component Test

```php
it('triggers audio playback with bass when chord is selected', function () {
    Livewire::test(ChordGrid::class)
        ->call('selectChord', 0)
        ->assertEmitted('playChordWithBass', [
            'notes' => ['C4', 'E4', 'G4'],
            'root' => 'C4',
            'duration' => '2n'
        ]);
});
```

### Browser Test for Audio Quality

```javascript
// Playwright/Puppeteer test
test('bass note plays without distortion', async ({ page }) => {
  await page.goto('/chords');
  
  // Start audio analysis
  const audioData = await page.evaluate(() => {
    return new Promise((resolve) => {
      const audioContext = new AudioContext();
      const analyser = audioContext.createAnalyser();
      // ... setup audio analysis
      
      // Trigger chord playback
      document.querySelector('.chord-tile').click();
      
      // Collect frequency data
      setTimeout(() => {
        const frequencies = new Float32Array(analyser.frequencyBinCount);
        analyser.getFloatFrequencyData(frequencies);
        resolve(frequencies);
      }, 500);
    });
  });
  
  // Verify bass frequencies are present
  const bassFreqIndex = Math.floor(65.41 * audioData.length / 22050); // C2
  expect(audioData[bassFreqIndex]).toBeGreaterThan(-40); // dB threshold
});
```

## Testing Priorities

1. **Critical Path**: Bass note calculation and playback accuracy
2. **High Priority**: Audio timing and synchronization
3. **High Priority**: Volume balance and mix quality
4. **Medium Priority**: Edge case handling for extreme ranges
5. **Lower Priority**: Performance optimization tests

All audio functionality must be tested across major browsers and devices, with particular attention to mobile audio limitations and Bluetooth device compatibility.