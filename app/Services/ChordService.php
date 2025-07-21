<?php

declare(strict_types=1);

namespace App\Services;

class ChordService
{
    private array $noteToMidi = [
        'C' => 0, 'C#' => 1, 'D' => 2, 'D#' => 3,
        'E' => 4, 'F' => 5, 'F#' => 6, 'G' => 7,
        'G#' => 8, 'A' => 9, 'A#' => 10, 'B' => 11,
    ];

    private array $chordIntervals = [
        'major' => [0, 4, 7],
        'minor' => [0, 3, 7],
        'diminished' => [0, 3, 6],
        'augmented' => [0, 4, 8],
    ];

    // Scale intervals for major and minor keys
    private array $scaleIntervals = [
        'major' => [0, 2, 4, 5, 7, 9, 11], // I, ii, iii, IV, V, vi, vii°
        'minor' => [0, 2, 3, 5, 7, 8, 10], // i, ii°, III, iv, v, VI, VII
    ];

    // Roman numeral analysis for chord qualities in major key
    private array $majorKeyQualities = [
        'I' => 'major', 'ii' => 'minor', 'iii' => 'minor',
        'IV' => 'major', 'V' => 'major', 'vi' => 'minor', 'vii' => 'diminished',
    ];

    // Roman numeral analysis for chord qualities in minor key
    private array $minorKeyQualities = [
        'i' => 'minor', 'ii' => 'diminished', 'III' => 'major',
        'iv' => 'minor', 'v' => 'minor', 'VI' => 'major', 'VII' => 'major',
    ];

    public function getChordNotes(string $rootNote, ?string $chordType = 'major', ?string $inversion = 'root'): array
    {
        $chordType = $chordType ?? 'major';
        $inversion = $inversion ?? 'root';

        if (! isset($this->noteToMidi[$rootNote])) {
            throw new \InvalidArgumentException("Invalid root note: $rootNote");
        }

        if (! isset($this->chordIntervals[$chordType])) {
            throw new \InvalidArgumentException("Invalid chord type: $chordType");
        }

        $rootMidi = $this->noteToMidi[$rootNote];
        $intervals = $this->chordIntervals[$chordType];

        // Calculate chord notes
        $notes = [];
        foreach ($intervals as $interval) {
            $midiNote = ($rootMidi + $interval) % 12;
            $notes[] = $this->midiToNote($midiNote);
        }

        // Apply inversion
        $notes = $this->applyInversion($notes, $inversion);

        return $notes;
    }

    private function applyInversion(array $notes, string $inversion): array
    {
        switch ($inversion) {
            case 'first':
                // Move first note to end
                $first = array_shift($notes);
                $notes[] = $first;
                break;
            case 'second':
                // Move first two notes to end
                $first = array_shift($notes);
                $second = array_shift($notes);
                $notes[] = $first;
                $notes[] = $second;
                break;
            case 'third':
                // Reverse order (for 3-note chords, this is equivalent to moving all but last to end)
                $notes = array_reverse($notes);
                break;
        }

        return $notes;
    }

    private function midiToNote(int $midi): string
    {
        $noteNames = array_flip($this->noteToMidi);

        return $noteNames[$midi];
    }

    /**
     * Calculate the optimal inversion for the next chord based on voice leading
     *
     * @param  array  $currentChord  Current chord with tone, semitone, and inversion
     * @param  array  $nextChord  Next chord with tone and semitone
     * @return string The optimal inversion for the next chord
     */
    public function calculateOptimalInversion(array $currentChord, array $nextChord): string
    {
        // Get the actual notes of the current chord with octaves
        $currentNotes = $this->getChordNotesWithOctaves(
            $currentChord['tone'],
            $currentChord['semitone'] ?? 'major',
            $currentChord['inversion'] ?? 'root'
        );

        // Try all inversions of the next chord
        $inversions = ['root', 'first', 'second'];
        $bestInversion = 'root';
        $smallestMovement = PHP_INT_MAX;

        foreach ($inversions as $inversion) {
            $nextNotes = $this->getChordNotesWithOctaves(
                $nextChord['tone'],
                $nextChord['semitone'] ?? 'major',
                $inversion
            );

            // Calculate total movement distance
            $totalMovement = $this->calculateVoiceLeadingDistance($currentNotes, $nextNotes);

            if ($totalMovement < $smallestMovement) {
                $smallestMovement = $totalMovement;
                $bestInversion = $inversion;
            }
        }

        return $bestInversion;
    }

    /**
     * Get chord notes with specific octaves based on comfortable voicing
     */
    private function getChordNotesWithOctaves(string $rootNote, string $chordType, string $inversion): array
    {
        $notes = $this->getChordNotes($rootNote, $chordType, $inversion);

        // Apply comfortable octaves based on inversion
        $octaves = match ($inversion) {
            'root' => [4, 4, 4],
            'first' => [3, 4, 4],
            'second' => [3, 3, 4],
            default => [4, 4, 4]
        };

        $notesWithOctaves = [];
        foreach ($notes as $index => $note) {
            $notesWithOctaves[] = [
                'note' => $note,
                'octave' => $octaves[$index] ?? 4,
                'midi' => $this->noteToMidi[$note] + (12 * ($octaves[$index] ?? 4)),
            ];
        }

        return $notesWithOctaves;
    }

    /**
     * Calculate the total voice leading distance between two chords
     */
    private function calculateVoiceLeadingDistance(array $chord1, array $chord2): int
    {
        $totalDistance = 0;

        // For each note in the first chord, find the closest note in the second chord
        foreach ($chord1 as $note1) {
            $minDistance = PHP_INT_MAX;

            foreach ($chord2 as $note2) {
                $distance = abs($note1['midi'] - $note2['midi']);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                }
            }

            $totalDistance += $minDistance;
        }

        return $totalDistance;
    }

    public function calculateBlueNotes(array $chords): array
    {
        // Blue notes are typically the flattened 3rd, 5th, and 7th degrees
        // In the context of this piano chord app, we'll identify notes that create
        // tension or dissonance when played together

        $allNotes = [];
        $blueNotes = [];

        // Collect all notes from all chords
        foreach ($chords as $chord) {
            if (! empty($chord['tone'])) {
                $notes = $this->getChordNotes(
                    $chord['tone'],
                    $chord['semitone'] ?? 'major',
                    $chord['inversion'] ?? 'root'
                );
                $allNotes = array_merge($allNotes, $notes);
            }
        }

        // Count note occurrences
        $noteCount = array_count_values($allNotes);

        // Identify potential blue notes based on harmonic analysis
        foreach ($chords as $index => $chord) {
            if (! empty($chord['tone'])) {
                $chordNotes = $this->getChordNotes(
                    $chord['tone'],
                    $chord['semitone'] ?? 'major',
                    $chord['inversion'] ?? 'root'
                );

                // Check for minor thirds in major context or tritones
                foreach ($chordNotes as $note) {
                    if ($this->isBlueNote($note, $allNotes, $chord)) {
                        $blueNotes[$index] = true;
                        break;
                    }
                }
            }
        }

        return $blueNotes;
    }

    private function isBlueNote(string $note, array $allNotes, array $currentChord): bool
    {
        // Simplified blue note detection
        // In a real implementation, this would involve more complex harmonic analysis

        $noteIndex = $this->noteToMidi[$note];

        // Check for tritone intervals (6 semitones)
        foreach ($allNotes as $otherNote) {
            if ($otherNote !== $note) {
                $otherIndex = $this->noteToMidi[$otherNote];
                $interval = abs($noteIndex - $otherIndex);

                // Tritone or minor second intervals often create blue note effects
                if ($interval === 6 || $interval === 1 || $interval === 11) {
                    return true;
                }
            }
        }

        // Check if it's a flat 3rd, 5th, or 7th in context
        if ($currentChord['semitone'] === 'major') {
            $root = $this->noteToMidi[$currentChord['tone']];
            $intervalFromRoot = ($noteIndex - $root + 12) % 12;

            // Flat 3rd (3 semitones), flat 5th (6 semitones), flat 7th (10 semitones)
            if (in_array($intervalFromRoot, [3, 6, 10])) {
                return true;
            }
        }

        return false;
    }

    public function getPianoKeyPosition(string $note, int $octave = 4): int
    {
        // Returns the position of a key on an 88-key piano (0-87)
        // A0 is position 0, C8 is position 87

        $noteIndex = $this->noteToMidi[$note];
        $midiNumber = ($octave + 1) * 12 + $noteIndex;

        // A0 is MIDI 21, so subtract 21 to get piano key position
        return $midiNumber - 21;
    }

    public function isBlackKey(string $note): bool
    {
        return strpos($note, '#') !== false;
    }

    /**
     * Transpose a chord progression from roman numerals to a specific key
     *
     * @param  string  $key  The target key (e.g., 'G', 'C', 'A')
     * @param  string  $keyType  The key type ('major' or 'minor')
     * @param  array  $progression  Array of roman numerals (e.g., ['I', 'IV', 'V'])
     * @return array Array of chords with tone and semitone
     */
    public function transposeProgression(string $key, string $keyType, array $progression): array
    {
        if (! isset($this->noteToMidi[$key])) {
            throw new \InvalidArgumentException("Invalid key: $key");
        }

        $qualities = $keyType === 'major' ? $this->majorKeyQualities : $this->minorKeyQualities;
        $scaleIntervals = $this->scaleIntervals[$keyType];
        $rootMidi = $this->noteToMidi[$key];
        $result = [];

        foreach ($progression as $roman) {
            $romanUpper = strtoupper($roman);
            $scalePosition = $this->romanToScalePosition($romanUpper);

            if ($scalePosition === null) {
                continue;
            }

            // Calculate the root note
            $interval = $scaleIntervals[$scalePosition - 1];
            $noteMidi = ($rootMidi + $interval) % 12;
            $note = $this->midiToNote($noteMidi);

            // Determine the chord quality
            $quality = $qualities[$roman] ?? 'major';

            $result[] = [
                'tone' => $note,
                'semitone' => $quality,
            ];
        }

        return $result;
    }

    /**
     * Convert roman numeral to scale position (1-7)
     */
    private function romanToScalePosition(string $roman): ?int
    {
        $romanMap = [
            'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4,
            'V' => 5, 'VI' => 6, 'VII' => 7,
        ];

        // Remove any modifiers and get base roman numeral
        $baseRoman = preg_replace('/[^IVX]/', '', strtoupper($roman));

        return $romanMap[$baseRoman] ?? null;
    }

    /**
     * Get available keys for selection
     */
    public function getAvailableKeys(): array
    {
        return array_keys($this->noteToMidi);
    }

    /**
     * Analyze a chord and return its roman numeral in the given key
     *
     * @param  string  $chordTone  The chord root note (e.g., 'G', 'C#')
     * @param  string  $chordQuality  The chord quality ('major', 'minor', 'diminished', 'augmented')
     * @param  string  $key  The key to analyze in (e.g., 'G', 'C')
     * @param  string  $keyType  The key type ('major' or 'minor')
     * @return string|null The roman numeral or null if not in key
     */
    public function analyzeChordInKey(string $chordTone, string $chordQuality, string $key, string $keyType): ?string
    {
        if (! isset($this->noteToMidi[$chordTone]) || ! isset($this->noteToMidi[$key])) {
            return null;
        }

        $keyRoot = $this->noteToMidi[$key];
        $chordRoot = $this->noteToMidi[$chordTone];

        // Calculate the interval from the key root
        $interval = ($chordRoot - $keyRoot + 12) % 12;

        // Find which scale degree this interval corresponds to
        $scaleIntervals = $this->scaleIntervals[$keyType];
        $scaleDegree = array_search($interval, $scaleIntervals);

        if ($scaleDegree === false) {
            // Chord is not diatonic to the key
            return null;
        }

        // Convert scale degree to roman numeral
        $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII'];
        $roman = $romanNumerals[$scaleDegree];

        // Determine expected quality for this scale degree
        $qualities = $keyType === 'major' ? $this->majorKeyQualities : $this->minorKeyQualities;
        $expectedQualities = array_values($qualities);
        $expectedQuality = $expectedQualities[$scaleDegree] ?? 'major';

        // Make lowercase for minor chords in major keys, or adjust for minor keys
        if ($keyType === 'major' && $chordQuality === 'minor') {
            $roman = strtolower($roman);
        } elseif ($keyType === 'minor') {
            // In minor keys, use the conventional notation
            $minorNotation = ['i', 'ii°', 'III', 'iv', 'v', 'VI', 'VII'];
            $roman = $minorNotation[$scaleDegree];

            // Adjust if the actual quality differs from expected
            if ($chordQuality === 'major' && in_array($scaleDegree, [0, 3, 4])) {
                $roman = strtoupper($roman);
            }
        }

        // Add diminished symbol if needed
        if ($chordQuality === 'diminished' && strpos($roman, '°') === false) {
            $roman .= '°';
        }

        // Add augmented symbol if needed
        if ($chordQuality === 'augmented') {
            $roman .= '+';
        }

        return $roman;
    }

    /**
     * Analyze a progression of chords and return roman numerals
     *
     * @param  array  $chords  Array of chords with 'tone' and 'semitone' keys
     * @param  string  $key  The key to analyze in
     * @param  string  $keyType  The key type ('major' or 'minor')
     * @return array Array of roman numerals
     */
    public function analyzeProgression(array $chords, string $key, string $keyType): array
    {
        $romanNumerals = [];

        foreach ($chords as $chord) {
            if (empty($chord['tone'])) {
                $romanNumerals[] = '';

                continue;
            }

            $roman = $this->analyzeChordInKey(
                $chord['tone'],
                $chord['semitone'] ?? 'major',
                $key,
                $keyType
            );

            $romanNumerals[] = $roman ?? '?';
        }

        return $romanNumerals;
    }
}
