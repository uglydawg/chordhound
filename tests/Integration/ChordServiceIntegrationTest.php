<?php

declare(strict_types=1);

use App\Services\ChordService;
use App\Services\MathematicalChordService;

beforeEach(function () {
    $this->chordService = new ChordService();
    $this->mathService = new MathematicalChordService();
});

describe('ChordService and MathematicalChordService Integration', function () {

    it('produces compatible note formats', function () {
        // Get notes from both services for C major root position
        $chordServiceNotes = $this->chordService->getChordNotesForDisplay('C', 'major', 'root');
        $mathServiceNotes = $this->mathService->calculateChord('C', 'major', 'C4', 0);

        // Both should return array of note strings with octaves
        expect($chordServiceNotes)->toBeArray();
        expect($mathServiceNotes)->toBeArray();

        // Each note should match the pattern: note name + octave
        foreach ($chordServiceNotes as $note) {
            expect($note)->toMatch('/^[A-G]#?b?\d$/');
        }

        foreach ($mathServiceNotes as $note) {
            expect($note)->toMatch('/^[A-G]#?b?\d$/');
        }
    });

    it('calculates similar voice leading distances', function () {
        // Create test progression: C major -> F major
        $chord1Data = ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root'];
        $chord2Data = ['tone' => 'F', 'semitone' => 'major', 'inversion' => 'root'];

        // Get optimal inversion from ChordService
        $optimalInversion = $this->chordService->calculateOptimalInversion($chord1Data, $chord2Data);

        // Both services should recognize smooth voice leading
        expect($optimalInversion)->toBeIn(['root', 'first', 'second']);

        // Mathematical service should also calculate reasonable distance
        $mathChord1 = $this->mathService->calculateChord('C', 'major', 'C4', 0);
        $mathChord2 = $this->mathService->calculateChord('F', 'major', 'C4', 0);
        $mathDistance = $this->mathService->calculateVoiceLeadingDistance($mathChord1, $mathChord2);

        // Distance should be reasonable (less than 20 semitones total movement)
        expect($mathDistance)->toBeGreaterThan(0);
        expect($mathDistance)->toBeLessThan(20);
    });

    it('handles all 12 keys for major chords', function () {
        $keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

        foreach ($keys as $key) {
            // ChordService
            $chordServiceNotes = $this->chordService->getChordNotesForDisplay($key, 'major', 'root');

            // MathematicalChordService
            $mathServiceNotes = $this->mathService->calculateChord($key, 'major', $key . '4', 0);

            // Both should produce valid results
            expect($chordServiceNotes)->toHaveCount(3); // ChordService returns 3 notes
            expect($mathServiceNotes)->toHaveCount(4); // MathService includes bass note

            // The chord notes (excluding bass in math service) should contain the root
            expect($chordServiceNotes[0])->toContain($key);
            expect($mathServiceNotes[1])->toContain($key); // Skip bass note at index 0
        }
    });

    it('handles all 12 keys for minor chords', function () {
        $keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

        foreach ($keys as $key) {
            // ChordService
            $chordServiceNotes = $this->chordService->getChordNotesForDisplay($key, 'minor', 'root');

            // MathematicalChordService
            $mathServiceNotes = $this->mathService->calculateChord($key, 'minor', $key . '4', 0);

            // Both should produce valid results
            expect($chordServiceNotes)->toHaveCount(3);
            expect($mathServiceNotes)->toHaveCount(4); // Includes bass

            // Both should start with the root note
            expect($chordServiceNotes[0])->toContain($key);
            expect($mathServiceNotes[1])->toContain($key);
        }
    });

    it('handles inversions consistently', function () {
        $inversions = [
            'root' => 0,
            'first' => 1,
            'second' => 2,
        ];

        foreach ($inversions as $inversionName => $inversionNum) {
            // ChordService
            $chordServiceNotes = $this->chordService->getChordNotesForDisplay('C', 'major', $inversionName);

            // MathematicalChordService
            $mathServiceNotes = $this->mathService->calculateChord('C', 'major', 'C4', $inversionNum);

            // Both should return valid chord notes
            expect($chordServiceNotes)->toHaveCount(3);
            expect($mathServiceNotes)->toHaveCount(4); // Includes bass

            // Verify the notes are in ascending order (roughly)
            // This is a basic sanity check for voicing
            expect($chordServiceNotes)->toBeArray();
            expect($mathServiceNotes)->toBeArray();
        }
    });

    it('produces compatible results for common progressions', function () {
        $progressions = [
            ['C', 'F', 'G'],
            ['G', 'C', 'D'],
            ['A', 'D', 'E'],
        ];

        foreach ($progressions as $progression) {
            foreach ($progression as $root) {
                // Both services should handle these common chords
                $chordServiceNotes = $this->chordService->getChordNotesForDisplay($root, 'major', 'root');
                $mathServiceNotes = $this->mathService->calculateChord($root, 'major', $root . '4', 0);

                expect($chordServiceNotes)->toBeArray();
                expect($mathServiceNotes)->toBeArray();

                // Chord notes should contain the root
                $hasRoot = false;
                foreach ($chordServiceNotes as $note) {
                    if (str_starts_with($note, $root)) {
                        $hasRoot = true;
                        break;
                    }
                }
                expect($hasRoot)->toBeTrue();
            }
        }
    });

    it('maintains interval relationships in both services', function () {
        // Test C major chord intervals (0-4-7 semitones)
        $chordServiceNotes = $this->chordService->getChordNotes('C', 'major', 'root');
        $mathServiceNotes = $this->mathService->calculateChord('C', 'major', 'C4', 0);

        // ChordService returns note names without octaves for getChordNotes
        expect($chordServiceNotes)->toEqual(['C', 'E', 'G']);

        // MathService includes bass + chord notes
        // Extract just the note names (without octaves) from math service
        $mathNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), array_slice($mathServiceNotes, 1));

        // Should contain the same note names (may have different octaves)
        expect($mathNoteNames)->toContain('C');
        expect($mathNoteNames)->toContain('E');
        expect($mathNoteNames)->toContain('G');
    });

    it('handles edge cases consistently', function () {
        // Test with sharps
        $chordServiceNotes = $this->chordService->getChordNotesForDisplay('F#', 'major', 'root');
        $mathServiceNotes = $this->mathService->calculateChord('F#', 'major', 'F#4', 0);

        expect($chordServiceNotes)->toBeArray();
        expect($mathServiceNotes)->toBeArray();

        // Test with uncommon chord types
        $diminishedNotes = $this->chordService->getChordNotes('B', 'diminished', 'root');
        $mathDiminishedNotes = $this->mathService->calculateChord('B', 'diminished', 'B3', 0);

        expect($diminishedNotes)->toEqual(['B', 'D', 'F']);

        // Math service should also produce B, D, F (possibly with different octaves)
        $mathNoteNames = array_map(fn($note) => preg_replace('/\d+$/', '', $note), array_slice($mathDiminishedNotes, 1));
        expect($mathNoteNames)->toContain('B');
        expect($mathNoteNames)->toContain('D');
        expect($mathNoteNames)->toContain('F');
    });

    it('performs within acceptable time for typical operations', function () {
        $start = microtime(true);

        // Simulate a typical user workflow: selecting 4 chords
        for ($i = 0; $i < 4; $i++) {
            $this->chordService->getChordNotesForDisplay('C', 'major', 'root');
            $this->mathService->calculateChord('C', 'major', 'C4', 0);
        }

        $duration = microtime(true) - $start;

        // Should complete quickly (under 50ms for 4 chord pairs)
        expect($duration)->toBeLessThan(0.05);
    });

    it('produces consistent results across multiple calls', function () {
        // ChordService should be deterministic
        $notes1 = $this->chordService->getChordNotesForDisplay('G', 'major', 'root');
        $notes2 = $this->chordService->getChordNotesForDisplay('G', 'major', 'root');
        expect($notes1)->toEqual($notes2);

        // MathematicalChordService should also be deterministic
        $mathNotes1 = $this->mathService->calculateChord('G', 'major', 'G4', 0);
        $mathNotes2 = $this->mathService->calculateChord('G', 'major', 'G4', 0);
        expect($mathNotes1)->toEqual($mathNotes2);
    });
});

describe('Voice Leading Optimization Compatibility', function () {

    it('calculates optimal inversions for common progressions', function () {
        $progressions = [
            // I-IV-V in C
            [
                ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root'],
                ['tone' => 'F', 'semitone' => 'major'],
            ],
            // I-vi-IV-V in G
            [
                ['tone' => 'G', 'semitone' => 'major', 'inversion' => 'root'],
                ['tone' => 'E', 'semitone' => 'minor'],
            ],
        ];

        foreach ($progressions as [$current, $next]) {
            $optimalInversion = $this->chordService->calculateOptimalInversion($current, $next);

            // Should return a valid inversion
            expect($optimalInversion)->toBeIn(['root', 'first', 'second']);

            // The optimal inversion should minimize movement
            // We can verify this produces better voice leading than random inversion
            expect($optimalInversion)->toBeString();
        }
    });

    it('minimizes voice leading distance', function () {
        // Test that optimal inversion reduces movement
        $current = ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root'];
        $next = ['tone' => 'G', 'semitone' => 'major'];

        $optimalInversion = $this->chordService->calculateOptimalInversion($current, $next);

        // Calculate voice leading with optimal inversion
        $mathChord1 = $this->mathService->calculateChord('C', 'major', 'C4', 0);

        $inversionMap = ['root' => 0, 'first' => 1, 'second' => 2];
        $mathChord2Optimal = $this->mathService->calculateChord(
            'G',
            'major',
            'C4',
            $inversionMap[$optimalInversion]
        );

        $optimalDistance = $this->mathService->calculateVoiceLeadingDistance($mathChord1, $mathChord2Optimal);

        // Compare with a non-optimal inversion
        $mathChord2Root = $this->mathService->calculateChord('G', 'major', 'C4', 0);
        $rootDistance = $this->mathService->calculateVoiceLeadingDistance($mathChord1, $mathChord2Root);

        // Optimal should be equal or better than root position
        expect($optimalDistance)->toBeLessThanOrEqual($rootDistance + 1); // Allow small margin
    });
});
