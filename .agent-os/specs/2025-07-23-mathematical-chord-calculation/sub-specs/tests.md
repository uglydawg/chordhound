# Tests Specification

This is the tests coverage details for the spec detailed in @.agent-os/specs/2025-07-23-mathematical-chord-calculation/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Test Coverage

### Unit Tests

**MathematicalChordService**
- Calculate major chord from various starting positions
- Calculate minor chord with correct intervals
- Calculate diminished and augmented chords
- Apply inversions correctly (root, first, second)
- Add bass note one octave below root
- Handle edge cases (very high/low starting positions)
- Convert between note names and MIDI numbers accurately
- Validate voice leading distance calculations

**ChordInterval**
- Verify interval patterns for all chord types
- Test semitone calculations
- Validate octave wrapping behavior

**NoteFormatter**
- Format note names with correct octave numbers
- Handle enharmonic equivalents (C# vs Db)
- Parse note strings to MIDI numbers

### Integration Tests

**Chord Calculation Integration**
- Calculate complete 4-chord progressions
- Verify smooth voice leading between chords
- Ensure bass notes follow chord roots
- Test with all supported chord types
- Validate audio output matches visual display

**Piano Display Integration**
- Note names appear when keys are pressed
- Note names disappear when keys are released
- Multiple notes display simultaneously
- Display updates match chord changes
- Bass notes display correctly

### Feature Tests

**End-to-End Chord Generation**
- User selects chord and hears correct notes
- Visual piano matches audio output
- Note names display during playback
- Chord progressions transition smoothly
- Print view includes calculated voicings

**Backwards Compatibility**
- Existing saved chord sets load correctly
- API responses maintain same structure
- No regression in current features

### Mocking Requirements

- **Tone.js Audio Engine:** Mock audio playback for faster tests
- **Browser Audio API:** Stub Web Audio context in feature tests

## Test Examples

### Mathematical Chord Calculation Tests

```php
it('calculates C major chord from G4 position', function () {
    $service = new MathematicalChordService();
    $notes = $service->calculateChord('C', 'major', 'G4');
    
    expect($notes)->toBe(['C3', 'C4', 'E4', 'G4']);
});

it('calculates first inversion correctly', function () {
    $service = new MathematicalChordService();
    $notes = $service->calculateChord('C', 'major', 'E4', 1);
    
    expect($notes)->toContain('E4', 'G4', 'C5')
        ->and($notes[0])->toBe('C3'); // Bass note
});

it('minimizes voice leading distance between chords', function () {
    $service = new MathematicalChordService();
    $chord1 = $service->calculateChord('C', 'major', 'C4');
    $chord2 = $service->calculateChord('G', 'major', 'C4');
    
    $totalDistance = $service->calculateVoiceLeadingDistance($chord1, $chord2);
    expect($totalDistance)->toBeLessThan(12); // Less than an octave total movement
});
```

### Note Display Tests

```php
it('displays note names when chord is played', function () {
    Livewire::test(ChordDisplay::class)
        ->call('playChord', 'C', 'major')
        ->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');
});

it('hides note names after chord stops', function () {
    Livewire::test(ChordDisplay::class)
        ->call('playChord', 'C', 'major')
        ->call('stopChord')
        ->assertDontSee('C')
        ->assertDontSee('E')
        ->assertDontSee('G');
});
```

### Performance Tests

```php
it('calculates chords within acceptable time', function () {
    $service = new MathematicalChordService();
    $start = microtime(true);
    
    // Calculate 100 different chords
    for ($i = 0; $i < 100; $i++) {
        $service->calculateChord('C', 'major', 'C4');
    }
    
    $duration = microtime(true) - $start;
    expect($duration)->toBeLessThan(0.1); // Less than 100ms for 100 calculations
});
```

## Test Data

### Chord Progression Test Cases
- I-IV-V-I in C major
- ii-V-I in G major
- I-vi-IV-V in F major
- All inversions of C major chord
- Edge cases: Very high (C7) and low (C2) starting positions

### Expected Voice Leading Results
- C major to F major: Minimal movement (C stays, E→F, G→A)
- C major to G major: Smooth transition preserving common tones
- Progression through circle of fifths maintaining smooth voice leading