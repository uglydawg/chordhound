# ChordHound Piano Key Activation Tests

This directory contains comprehensive Dusk tests that verify the correct piano key activation and timing for all chord types, inversions, and progressions in ChordHound.

## Test Files

### 1. `QuickChordInversionTest.php`
**Purpose**: Basic functionality verification
- Tests basic chord playback with 1.5-second timing
- Verifies chord inversion changes (Root → First → Second)
- Tests I-IV-V progression inversion application

**Runtime**: ~2-3 minutes

### 2. `ComprehensiveChordInversionTest.php`
**Purpose**: Complete chord coverage testing
- Tests all 12 tones (C, C#, D, D#, E, F, F#, G, G#, A, A#, B)
- Tests all 4 chord types (major, minor, diminished, augmented)
- Tests all 3 inversions (root, first, second) 
- Verifies exact piano key activation for each combination
- Tests 1.5-second sustain timing for all chords
- Tests rapid chord changes clear previous keys

**Coverage**: 12 × 4 × 3 = 144 chord combinations
**Runtime**: ~15-20 minutes

### 3. `ProgressionInversionKeyTest.php`
**Purpose**: Progression and key change testing
- Tests all 5 progressions (I-IV-V, I-vi-IV-V, vi-IV-I-V, I-vi-ii-V, ii-V-I)
- Tests progressions in 7 different keys (C, D, E, F, G, A, B)
- Verifies correct inversion application from the progression table
- Tests key changes maintain proper inversions
- Tests major/minor key type changes
- Verifies timing consistency across all combinations

**Coverage**: 5 progressions × 7 keys = 35 combinations
**Runtime**: ~10-15 minutes

## What These Tests Verify

### Piano Key Activation
✅ Correct piano keys light up for each chord
✅ No incorrect keys are activated
✅ Keys activate immediately when chord is clicked
✅ Keys deactivate after exactly 1.5 seconds

### Chord Types
✅ **Major chords**: Root, third, fifth (e.g., C-E-G)
✅ **Minor chords**: Root, flat third, fifth (e.g., C-Eb-G)
✅ **Diminished chords**: Root, flat third, flat fifth (e.g., C-Eb-Gb)
✅ **Augmented chords**: Root, third, sharp fifth (e.g., C-E-G#)

### Inversions
✅ **Root position**: Root in bass (C-E-G)
✅ **First inversion**: Third in bass (E-G-C)
✅ **Second inversion**: Fifth in bass (G-C-E)

### Progressions
✅ **I-IV-V**: Root, second, first inversions
✅ **I-vi-IV-V**: Root, first, second, first inversions
✅ **vi-IV-I-V**: Root, first, second, root inversions
✅ **I-vi-ii-V**: Root, first, root, first inversions
✅ **ii-V-I**: Second, first, root inversions

### Key Changes
✅ All progressions work correctly in all keys
✅ Inversions are maintained when changing keys
✅ Major/minor key types are handled correctly

## Running the Tests

### Individual Test Files
```bash
# Quick basic tests
php artisan dusk tests/Browser/QuickChordInversionTest.php

# Comprehensive chord coverage
php artisan dusk tests/Browser/ComprehensiveChordInversionTest.php

# Progression and key testing
php artisan dusk tests/Browser/ProgressionInversionKeyTest.php
```

### All Tests with Runner Script
```bash
# Run all chord tests with progress reporting
./run-chord-tests.sh
```

## Expected Output

When tests pass, you'll see output like:
```
✓ Tested C major (root inversion): C4, E4, G4
✓ Tested C major (first inversion): E4, G4, C5
✓ Tested C major (second inversion): G4, C5, E5
✓ Tested C minor (root inversion): C4, Eb4, G4
...
✓ Tested I-IV-V in C major
✓ Tested I-IV-V in D major
...
```

## Technical Details

### Piano Key Identification
Piano keys are identified by their `data-note` attribute and CSS classes:
- **Key ID**: `#key-{note}` (e.g., `#key-C4`)
- **Active state**: `.active` class
- **Pressed state**: `.pressed` class

### Timing Verification
Tests verify:
1. Keys activate immediately (within 200ms)
2. Keys remain active for 1.4 seconds
3. Keys deactivate by 1.6 seconds (allowing 100ms buffer)

### Chord Note Calculation
The tests include helper methods that mirror the JavaScript chord calculation logic:
- `getExpectedNotes()`: Calculates expected notes for any chord/inversion
- `lowerNote()`/`raiseNote()`: Handles semitone modifications
- `raiseOctave()`: Handles octave changes for inversions

## Browser Requirements

These tests require:
- Chrome or Chromium browser
- ChromeDriver (automatically managed by Laravel Dusk)
- Audio support (tests verify visual feedback, not audio output)

## Troubleshooting

### Common Issues
1. **Timeout errors**: Tests may take longer on slower systems
2. **Chrome driver**: Run `php artisan dusk:chrome-driver --detect` to update
3. **Audio initialization**: Some tests wait for audio context setup

### Debug Mode
Add `->screenshot('debug-{test-name}')` to any test for visual debugging.

## Integration with CI/CD

These tests can be integrated into continuous integration pipelines to ensure chord functionality remains correct across code changes. Consider running the QuickChordInversionTest in CI and the comprehensive tests nightly due to their extended runtime.