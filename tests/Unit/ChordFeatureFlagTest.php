<?php

declare(strict_types=1);

use App\Services\ChordService;

it('uses legacy engine by default', function () {
    config(['chord.calculation_engine' => 'legacy']);

    $chordService = new ChordService();
    $notes = $chordService->getChordNotesForDisplay('C', 'major', 'root');

    // Legacy engine should return 3 notes (without bass)
    expect($notes)->toHaveCount(3);
    expect($notes)->toEqual(['C4', 'E4', 'G4']);
});

it('uses mathematical engine when configured', function () {
    config(['chord.calculation_engine' => 'mathematical']);

    $chordService = new ChordService();
    $notes = $chordService->getChordNotesForDisplay('C', 'major', 'root');

    // Mathematical engine also returns 3 notes (bass is excluded in getChordNotesForDisplay)
    expect($notes)->toHaveCount(3);

    // Should contain C, E, G in some octave
    $noteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $notes);
    expect($noteNames)->toContain('C');
    expect($noteNames)->toContain('E');
    expect($noteNames)->toContain('G');
});

it('produces compatible results in both modes', function () {
    // Test with legacy mode
    config(['chord.calculation_engine' => 'legacy']);
    $legacyService = new ChordService();
    $legacyNotes = $legacyService->getChordNotesForDisplay('G', 'major', 'root');

    // Test with mathematical mode
    config(['chord.calculation_engine' => 'mathematical']);
    $mathService = new ChordService();
    $mathNotes = $mathService->getChordNotesForDisplay('G', 'major', 'root');

    // Both should return arrays of the same length
    expect($legacyNotes)->toHaveCount(3);
    expect($mathNotes)->toHaveCount(3);

    // Both should contain the same note names (possibly different octaves)
    $legacyNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $legacyNotes);
    $mathNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $mathNotes);

    expect($legacyNoteNames)->toContain('G');
    expect($legacyNoteNames)->toContain('B');
    expect($legacyNoteNames)->toContain('D');

    expect($mathNoteNames)->toContain('G');
    expect($mathNoteNames)->toContain('B');
    expect($mathNoteNames)->toContain('D');
});

it('handles inversions in both modes', function () {
    $inversions = ['root', 'first', 'second'];

    foreach ($inversions as $inversion) {
        // Legacy mode
        config(['chord.calculation_engine' => 'legacy']);
        $legacyService = new ChordService();
        $legacyNotes = $legacyService->getChordNotesForDisplay('C', 'major', $inversion);

        // Mathematical mode
        config(['chord.calculation_engine' => 'mathematical']);
        $mathService = new ChordService();
        $mathNotes = $mathService->getChordNotesForDisplay('C', 'major', $inversion);

        // Both should return valid results
        expect($legacyNotes)->toHaveCount(3);
        expect($mathNotes)->toHaveCount(3);

        // Both should contain C, E, G
        $legacyNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $legacyNotes);
        $mathNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $mathNotes);

        expect($legacyNoteNames)->toContain('C');
        expect($legacyNoteNames)->toContain('E');
        expect($legacyNoteNames)->toContain('G');

        expect($mathNoteNames)->toContain('C');
        expect($mathNoteNames)->toContain('E');
        expect($mathNoteNames)->toContain('G');
    }
});

it('handles minor chords in both modes', function () {
    // Legacy mode
    config(['chord.calculation_engine' => 'legacy']);
    $legacyService = new ChordService();
    $legacyNotes = $legacyService->getChordNotesForDisplay('A', 'minor', 'root');

    // Mathematical mode
    config(['chord.calculation_engine' => 'mathematical']);
    $mathService = new ChordService();
    $mathNotes = $mathService->getChordNotesForDisplay('A', 'minor', 'root');

    // Extract note names
    $legacyNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $legacyNotes);
    $mathNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), $mathNotes);

    // Both should contain A, C, E
    expect($legacyNoteNames)->toContain('A');
    expect($legacyNoteNames)->toContain('C');
    expect($legacyNoteNames)->toContain('E');

    expect($mathNoteNames)->toContain('A');
    expect($mathNoteNames)->toContain('C');
    expect($mathNoteNames)->toContain('E');
});

it('switches modes dynamically based on config', function () {
    $chordService = new ChordService();

    // Start with legacy
    config(['chord.calculation_engine' => 'legacy']);
    $notes1 = $chordService->getChordNotesForDisplay('D', 'major', 'root');
    expect($notes1)->toBeArray();

    // Switch to mathematical
    config(['chord.calculation_engine' => 'mathematical']);
    $notes2 = $chordService->getChordNotesForDisplay('D', 'major', 'root');
    expect($notes2)->toBeArray();

    // Both should work and return valid chord notes
    expect($notes1)->toHaveCount(3);
    expect($notes2)->toHaveCount(3);
});

it('defaults to legacy mode when config is invalid', function () {
    config(['chord.calculation_engine' => 'invalid']);

    $chordService = new ChordService();
    $notes = $chordService->getChordNotesForDisplay('F', 'major', 'root');

    // Should fall back to legacy behavior
    expect($notes)->toHaveCount(3);
    expect($notes)->toBeArray();
});
