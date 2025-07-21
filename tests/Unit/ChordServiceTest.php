<?php

declare(strict_types=1);

use App\Services\ChordService;

it('transposes chord progressions to different keys', function () {
    $chordService = new ChordService;

    // Test I-IV-V in G major (should be G-C-D)
    $progression = $chordService->transposeProgression('G', 'major', ['I', 'IV', 'V']);
    expect($progression)->toEqual([
        ['tone' => 'G', 'semitone' => 'major'],
        ['tone' => 'C', 'semitone' => 'major'],
        ['tone' => 'D', 'semitone' => 'major'],
    ]);

    // Test I-V-vi-IV in G major (should be G-D-Em-C)
    $progression = $chordService->transposeProgression('G', 'major', ['I', 'V', 'vi', 'IV']);
    expect($progression)->toEqual([
        ['tone' => 'G', 'semitone' => 'major'],
        ['tone' => 'D', 'semitone' => 'major'],
        ['tone' => 'E', 'semitone' => 'minor'],
        ['tone' => 'C', 'semitone' => 'major'],
    ]);

    // Test I-vi-IV-V in G major (should be G-Em-C-D)
    $progression = $chordService->transposeProgression('G', 'major', ['I', 'vi', 'IV', 'V']);
    expect($progression)->toEqual([
        ['tone' => 'G', 'semitone' => 'major'],
        ['tone' => 'E', 'semitone' => 'minor'],
        ['tone' => 'C', 'semitone' => 'major'],
        ['tone' => 'D', 'semitone' => 'major'],
    ]);

    // Test ii-V-I in C major (should be Dm-G-C)
    $progression = $chordService->transposeProgression('C', 'major', ['ii', 'V', 'I']);
    expect($progression)->toEqual([
        ['tone' => 'D', 'semitone' => 'minor'],
        ['tone' => 'G', 'semitone' => 'major'],
        ['tone' => 'C', 'semitone' => 'major'],
    ]);

    // Test i-iv-v in A minor (should be Am-Dm-Em)
    $progression = $chordService->transposeProgression('A', 'minor', ['i', 'iv', 'v']);
    expect($progression)->toEqual([
        ['tone' => 'A', 'semitone' => 'minor'],
        ['tone' => 'D', 'semitone' => 'minor'],
        ['tone' => 'E', 'semitone' => 'minor'],
    ]);
});

it('handles all available keys', function () {
    $chordService = new ChordService;
    $keys = $chordService->getAvailableKeys();

    expect($keys)->toEqual([
        'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B',
    ]);
});

it('analyzes chords and returns correct roman numerals', function () {
    $chordService = new ChordService;

    // Test G major progression: G-Em-C-D -> I-vi-IV-V
    $chords = [
        ['tone' => 'G', 'semitone' => 'major'],
        ['tone' => 'E', 'semitone' => 'minor'],
        ['tone' => 'C', 'semitone' => 'major'],
        ['tone' => 'D', 'semitone' => 'major'],
    ];

    $romanNumerals = $chordService->analyzeProgression($chords, 'G', 'major');
    expect($romanNumerals)->toEqual(['I', 'vi', 'IV', 'V']);

    // Test C major progression: C-Am-F-G -> I-vi-IV-V
    $chords = [
        ['tone' => 'C', 'semitone' => 'major'],
        ['tone' => 'A', 'semitone' => 'minor'],
        ['tone' => 'F', 'semitone' => 'major'],
        ['tone' => 'G', 'semitone' => 'major'],
    ];

    $romanNumerals = $chordService->analyzeProgression($chords, 'C', 'major');
    expect($romanNumerals)->toEqual(['I', 'vi', 'IV', 'V']);

    // Test minor key: Am-Dm-Em -> i-iv-v
    $chords = [
        ['tone' => 'A', 'semitone' => 'minor'],
        ['tone' => 'D', 'semitone' => 'minor'],
        ['tone' => 'E', 'semitone' => 'minor'],
    ];

    $romanNumerals = $chordService->analyzeProgression($chords, 'A', 'minor');
    expect($romanNumerals)->toEqual(['i', 'iv', 'v']);
});
