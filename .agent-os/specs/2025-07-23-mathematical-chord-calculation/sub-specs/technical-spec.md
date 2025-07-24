# Technical Specification

This is the technical specification for the spec detailed in @.agent-os/specs/2025-07-23-mathematical-chord-calculation/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Technical Requirements

- **Mathematical Chord Generation**: Replace hardcoded voicing tables with interval-based calculation
- **Interval Definitions**: Define chord types by their semitone intervals (Major: 0-4-7, Minor: 0-3-7, etc.)
- **Voice Leading Algorithm**: Calculate optimal note positions to minimize movement between chords
- **Bass Note Calculation**: Automatically add root note one octave below the lowest chord note
- **Note-to-MIDI Conversion**: Convert note names (C4, E4, G4) to MIDI numbers for audio playback
- **Dynamic Note Display**: Show note names in large text below piano keys during playback
- **Octave Optimization**: Ensure chord voicings stay within comfortable playing range (C3-C6)
- **Inversion Support**: Mathematical calculation of root position, first, and second inversions

## Approach Options

**Option A: Lookup Table Enhancement**
- Pros: Minimal changes to existing code, predictable results
- Cons: Still requires manual data entry, doesn't solve core flexibility issue

**Option B: Pure Mathematical Calculation (Selected)**
- Pros: Completely dynamic, easily extensible, reduces code complexity significantly
- Cons: Requires careful algorithm design, potential edge cases to handle

**Option C: Hybrid Approach**
- Pros: Combines benefits of both approaches
- Cons: More complex implementation, two systems to maintain

**Rationale:** Pure mathematical calculation provides the most flexibility and maintainability. It aligns with music theory principles and makes future enhancements (7th chords, extended harmonies) trivial to implement.

## Technical Implementation Details

### Chord Calculation Algorithm

```php
class MathematicalChordService {
    // Interval patterns for chord types (in semitones)
    const CHORD_INTERVALS = [
        'major' => [0, 4, 7],
        'minor' => [0, 3, 7],
        'diminished' => [0, 3, 6],
        'augmented' => [0, 4, 8],
    ];
    
    public function calculateChord(string $root, string $type, string $startPosition, int $inversion = 0): array {
        // 1. Convert root and start position to MIDI numbers
        // 2. Calculate base chord intervals
        // 3. Apply inversion if needed
        // 4. Find optimal octave placement from start position
        // 5. Add bass note (root - 12 semitones)
        // 6. Return array of note names with octaves
    }
}
```

### Voice Leading Distance Calculation

The algorithm will use the principle of minimal motion:
1. Calculate total semitone distance for each possible voicing
2. Choose the voicing with the smallest total movement
3. Respect range constraints (typically C3-C6 for comfort)

### Note Display Component

```php
// Livewire component for note display
class NoteDisplay extends Component {
    public array $activeNotes = [];
    
    public function render() {
        return view('livewire.note-display', [
            'notes' => $this->formatNoteNames($this->activeNotes)
        ]);
    }
}
```

## Integration Points

### ChordService Refactoring
- Replace `getMajorChordVoicing()` and `getMinorChordVoicing()` methods
- Update `getChordNotes()` to use new mathematical approach
- Maintain backward compatibility with existing API

### Piano Component Updates
- Add note display below piano keys
- Integrate with Tone.js for audio playback
- Ensure visual feedback matches audio output

### Database Considerations
- No database changes required
- Existing chord_sets structure remains compatible

## Performance Considerations

- Mathematical calculations are negligible compared to audio processing
- Cache frequently used calculations if needed
- Pre-calculate common progressions for instant response

## External Dependencies

- **No new dependencies required** - The mathematical approach uses pure PHP
- Existing Tone.js integration handles audio playback
- Alpine.js for reactive note display updates

## Migration Strategy

1. Implement new mathematical service alongside existing code
2. Add feature flag to switch between implementations
3. Thoroughly test with existing chord progressions
4. Gradually phase out hardcoded tables
5. Remove legacy code once stable