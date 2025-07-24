<?php

declare(strict_types=1);

use App\Services\MathematicalChordService;

beforeEach(function () {
    $this->service = new MathematicalChordService();
});

it('calculates C major chord from C4 position', function () {
    $notes = $this->service->calculateChord('C', 'major', 'C4');
    
    expect($notes)->toBe(['C3', 'C4', 'E4', 'G4']);
});

it('calculates G major chord from G4 position', function () {
    $notes = $this->service->calculateChord('G', 'major', 'G4');
    
    expect($notes)->toBe(['G3', 'G4', 'B4', 'D5']);
});

it('calculates C minor chord from C4 position', function () {
    $notes = $this->service->calculateChord('C', 'minor', 'C4');
    
    expect($notes)->toBe(['C3', 'C4', 'Eb4', 'G4']);
});

it('calculates D minor chord from D4 position', function () {
    $notes = $this->service->calculateChord('D', 'minor', 'D4');
    
    expect($notes)->toBe(['D3', 'D4', 'F4', 'A4']);
});

it('calculates diminished chord correctly', function () {
    $notes = $this->service->calculateChord('B', 'diminished', 'B3');
    
    expect($notes)->toBe(['B2', 'B3', 'D4', 'F4']);
});

it('calculates augmented chord correctly', function () {
    $notes = $this->service->calculateChord('C', 'augmented', 'C4');
    
    expect($notes)->toBe(['C3', 'C4', 'E4', 'G#4']);
});

it('applies first inversion correctly', function () {
    $notes = $this->service->calculateChord('C', 'major', 'E4', 1);
    
    // First inversion: E-G-C with C bass
    expect($notes)->toBe(['C3', 'E4', 'G4', 'C5']);
});

it('applies second inversion correctly', function () {
    $notes = $this->service->calculateChord('C', 'major', 'G4', 2);
    
    // Second inversion: G-C-E with C bass
    expect($notes)->toBe(['C3', 'G4', 'C5', 'E5']);
});

it('handles sharps in root note', function () {
    $notes = $this->service->calculateChord('F#', 'major', 'F#4');
    
    expect($notes)->toBe(['F#3', 'F#4', 'A#4', 'C#5']);
});

it('handles flats by converting to sharps', function () {
    $notes = $this->service->calculateChord('Bb', 'major', 'Bb3');
    
    // A#/Bb are enharmonic equivalents
    expect($notes[0])->toBeIn(['A#2', 'Bb2']);
    expect($notes[1])->toBe('A#3');
    expect($notes[2])->toBe('D4');
    expect($notes[3])->toBe('F4');
});

it('calculates optimal voicing from different starting positions', function () {
    // Starting from E4, should find closest C major voicing
    $notes = $this->service->calculateChord('C', 'major', 'E4');
    
    // Should place notes close to E4
    expect($notes[0])->toBe('C3'); // Bass note always one octave below root
    expect($notes)->toContain('E4'); // Should include the starting position
});

it('keeps chord within playable range', function () {
    // Even with very high starting position
    $notes = $this->service->calculateChord('C', 'major', 'C7');
    
    // Should not go above reasonable range
    foreach ($notes as $note) {
        if ($note !== $notes[0]) { // Skip bass note
            $octave = (int) substr($note, -1);
            expect($octave)->toBeLessThanOrEqual(6);
        }
    }
});

it('handles very low starting positions', function () {
    $notes = $this->service->calculateChord('C', 'major', 'C2');
    
    // Bass note
    expect($notes[0])->toBe('C1');
    // Chord should be in reasonable range
    expect($notes[1])->toBe('C2');
});

it('calculates voice leading distance between chords', function () {
    $chord1 = $this->service->calculateChord('C', 'major', 'C4');
    $chord2 = $this->service->calculateChord('F', 'major', 'C4');
    
    $distance = $this->service->calculateVoiceLeadingDistance($chord1, $chord2);
    
    // Distance should be minimal for good voice leading
    expect($distance)->toBeGreaterThan(0);
    // C→F, E→A, G→C is about 5+5+5=15 semitones total
    expect($distance)->toBeLessThanOrEqual(15);
});

it('provides consistent results for same input', function () {
    $notes1 = $this->service->calculateChord('G', 'major', 'D4');
    $notes2 = $this->service->calculateChord('G', 'major', 'D4');
    
    expect($notes1)->toBe($notes2);
});

it('handles all 12 root notes', function () {
    $roots = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    
    foreach ($roots as $root) {
        $notes = $this->service->calculateChord($root, 'major', $root . '4');
        
        expect($notes)->toBeArray();
        expect($notes)->toHaveCount(4); // Bass + 3 chord notes
        expect($notes[0])->toContain($root); // Bass note contains root
    }
});

it('returns notes compatible with MultiInstrumentPlayer format', function () {
    $notes = $this->service->calculateChord('C', 'major', 'C4');
    
    foreach ($notes as $note) {
        // Each note should match pattern: note name + octave number
        expect($note)->toMatch('/^[A-G]#?\d$/');
    }
});

it('optimizes voicing to minimize movement from starting position', function () {
    // Starting from G4, C major chord should use closest voicing
    $notes = $this->service->calculateChord('C', 'major', 'G4');
    
    // Should use a voicing that includes G4 or nearby
    $hasNearbyG = false;
    foreach ($notes as $note) {
        if (str_starts_with($note, 'G') && $note !== $notes[0]) {
            $hasNearbyG = true;
            break;
        }
    }
    
    expect($hasNearbyG)->toBeTrue();
});

it('maintains proper interval relationships', function () {
    $notes = $this->service->calculateChord('C', 'major', 'C4');
    
    // Remove bass note for interval checking
    $chordNotes = array_slice($notes, 1);
    
    // The chord should contain C, E, and G in some octave
    $noteNames = array_map(fn($note) => substr($note, 0, -1), $chordNotes);
    
    expect($noteNames)->toContain('C');
    expect($noteNames)->toContain('E');
    expect($noteNames)->toContain('G');
});

it('performs within acceptable time limits', function () {
    $start = microtime(true);
    
    // Calculate 100 different chords
    for ($i = 0; $i < 100; $i++) {
        $this->service->calculateChord('C', 'major', 'C4');
    }
    
    $duration = microtime(true) - $start;
    
    // Should complete 100 calculations in under 100ms
    expect($duration)->toBeLessThan(0.1);
});