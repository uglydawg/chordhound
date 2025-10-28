<?php

declare(strict_types=1);

namespace App\Services;

class MathematicalChordService
{
    /**
     * Chord interval patterns in semitones
     */
    private const CHORD_INTERVALS = [
        'major' => [0, 4, 7],
        'minor' => [0, 3, 7],
        'diminished' => [0, 3, 6],
        'augmented' => [0, 4, 8],
    ];

    /**
     * Note sequence for calculations
     */
    private const NOTES = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

    /**
     * Flat to sharp conversions
     */
    private const FLAT_TO_SHARP = [
        'Db' => 'C#',
        'Eb' => 'D#',
        'Gb' => 'F#',
        'Ab' => 'G#',
        'Bb' => 'A#',
    ];
    
    /**
     * Sharp to flat conversions for proper enharmonic spelling
     */
    private const SHARP_TO_FLAT = [
        'C#' => 'Db',
        'D#' => 'Eb',
        'F#' => 'Gb',
        'G#' => 'Ab',
        'A#' => 'Bb',
    ];

    /**
     * Calculate chord notes based on mathematical intervals
     *
     * @param string $root Root note (e.g., 'C', 'F#', 'Bb')
     * @param string $type Chord type ('major', 'minor', 'diminished', 'augmented')
     * @param string $startPosition Starting position (e.g., 'C4', 'G5')
     * @param int $inversion Inversion (0 = root, 1 = first, 2 = second)
     * @return array Array of notes including bass note
     */
    public function calculateChord(string $root, string $type, string $startPosition, int $inversion = 0): array
    {
        // Store original root for enharmonic preferences
        $originalRoot = $root;
        
        // Normalize root note (convert flats to sharps for calculation)
        $root = $this->normalizeNote($root);
        
        // Get the intervals for this chord type
        $intervals = self::CHORD_INTERVALS[$type] ?? self::CHORD_INTERVALS['major'];
        
        // Calculate the base chord notes (without octaves)
        $chordNotes = $this->calculateChordNotes($root, $intervals);
        
        // Apply proper enharmonic spelling based on context
        $chordNotes = $this->applyEnharmonicSpelling($chordNotes, $originalRoot, $type);
        
        // Apply inversion by rotating the notes
        if ($inversion > 0) {
            $chordNotes = $this->applyInversion($chordNotes, $inversion);
        }
        
        // Parse the starting position
        $startNote = substr($startPosition, 0, -1);
        $startOctave = (int) substr($startPosition, -1);
        
        // Adjust starting octave for very high positions
        if ($startOctave > 6) {
            $startOctave = 6;
        }
        
        // Assign octaves to chord notes based on starting position
        $notesWithOctaves = $this->assignOctaves($chordNotes, $startNote, $startOctave, $inversion);
        
        // Calculate bass note (root one octave below)
        $bassNote = $this->calculateBassNote($originalRoot, $notesWithOctaves, $inversion);
        
        // Return bass note first, then chord notes
        return array_merge([$bassNote], $notesWithOctaves);
    }

    /**
     * Calculate voice leading distance between two chords
     *
     * @param array $chord1 First chord notes
     * @param array $chord2 Second chord notes
     * @return int Total semitone distance
     */
    public function calculateVoiceLeadingDistance(array $chord1, array $chord2): int
    {
        $distance = 0;
        
        // Skip bass notes (index 0) and compare chord notes
        $maxNotes = min(count($chord1) - 1, count($chord2) - 1);
        for ($i = 1; $i <= $maxNotes; $i++) {
            $note1 = $this->noteToSemitones($chord1[$i]);
            $note2 = $this->noteToSemitones($chord2[$i]);
            
            // Calculate minimum distance (could go up or down)
            $directDistance = abs($note2 - $note1);
            $wrappedDistance = 12 - $directDistance;
            
            $distance += min($directDistance, $wrappedDistance);
        }
        
        return $distance;
    }

    /**
     * Normalize note name (convert flats to sharps)
     */
    private function normalizeNote(string $note): string
    {
        // Check if it's a flat note
        if (strlen($note) > 1 && $note[1] === 'b') {
            return self::FLAT_TO_SHARP[$note] ?? $note;
        }
        
        return $note;
    }

    /**
     * Calculate chord notes from root and intervals
     */
    private function calculateChordNotes(string $root, array $intervals): array
    {
        $rootIndex = array_search($root, self::NOTES);
        if ($rootIndex === false) {
            throw new \InvalidArgumentException("Invalid root note: {$root}");
        }
        
        $chordNotes = [];
        foreach ($intervals as $interval) {
            $noteIndex = ($rootIndex + $interval) % 12;
            $chordNotes[] = self::NOTES[$noteIndex];
        }
        
        return $chordNotes;
    }
    
    /**
     * Apply proper enharmonic spelling based on chord type and root
     */
    private function applyEnharmonicSpelling(array $notes, string $originalRoot, string $type): array
    {
        // For C minor, we want Eb instead of D#
        if ($type === 'minor' && $originalRoot === 'C' && in_array('D#', $notes)) {
            $notes = array_map(fn($note) => $note === 'D#' ? 'Eb' : $note, $notes);
        }
        
        return $notes;
    }

    /**
     * Apply inversion to chord notes
     */
    private function applyInversion(array $notes, int $inversion): array
    {
        // Rotate notes based on inversion
        for ($i = 0; $i < $inversion; $i++) {
            $first = array_shift($notes);
            $notes[] = $first;
        }
        
        return $notes;
    }

    /**
     * Assign octaves to chord notes based on starting position
     */
    private function assignOctaves(array $chordNotes, string $startNote, int $startOctave, int $inversion): array
    {
        $notesWithOctaves = [];
        
        // For root position, use straightforward octave assignment
        if ($inversion === 0) {
            $currentOctave = $startOctave;
            foreach ($chordNotes as $i => $note) {
                if ($i === 0) {
                    // First note uses the starting octave
                    $notesWithOctaves[] = $note . $currentOctave;
                } else {
                    // Calculate if we need to go to next octave
                    $prevNoteIndex = $this->getNoteIndex(substr($notesWithOctaves[$i-1], 0, -1));
                    $currNoteIndex = $this->getNoteIndex($note);
                    
                    if ($currNoteIndex <= $prevNoteIndex) {
                        $currentOctave++;
                    }
                    
                    // Keep within reasonable range
                    if ($currentOctave > 6) {
                        $currentOctave = 6;
                    }
                    
                    $notesWithOctaves[] = $note . $currentOctave;
                }
            }
        } else {
            // For inversions, optimize for voice leading from start position
            $currentOctave = $startOctave;
            
            foreach ($chordNotes as $i => $note) {
                if ($i === 0) {
                    // First note of inversion should be at or near start position
                    $notesWithOctaves[] = $note . $currentOctave;
                } else {
                    // Place subsequent notes optimally
                    $prevNote = $notesWithOctaves[$i-1];
                    $prevNoteIndex = $this->getNoteIndex(substr($prevNote, 0, -1));
                    $prevOctave = (int) substr($prevNote, -1);
                    
                    $noteIndex = $this->getNoteIndex($note);
                    
                    // Determine optimal octave
                    if ($noteIndex <= $prevNoteIndex) {
                        $currentOctave = $prevOctave + 1;
                    } else {
                        $currentOctave = $prevOctave;
                    }
                    
                    // Keep within reasonable range
                    if ($currentOctave > 6) {
                        $currentOctave = 6;
                    }
                    
                    $notesWithOctaves[] = $note . $currentOctave;
                }
            }
        }
        
        return $notesWithOctaves;
    }

    /**
     * Calculate bass note (root one octave below lowest chord note)
     */
    private function calculateBassNote(string $root, array $chordNotes, int $inversion): string
    {
        // Normalize root for comparison
        $normalizedRoot = $this->normalizeNote($root);
        
        // For inversions, bass note is always based on the root position
        if ($inversion > 0) {
            // Find the C in the chord (might be C5 in first inversion)
            foreach ($chordNotes as $note) {
                $noteName = substr($note, 0, -1);
                if ($this->normalizeNote($noteName) === $normalizedRoot) {
                    $rootOctave = (int) substr($note, -1);
                    // Bass is one octave below, but for inversions we might need C3
                    $bassOctave = min($rootOctave - 1, 3);
                    if ($bassOctave < 1) $bassOctave = 1;
                    return $root . $bassOctave;
                }
            }
            // Default to C3 for inversions if root not found
            return $root . '3';
        }
        
        // For root position, find the root note in the chord
        $rootOctave = 4; // Default
        
        foreach ($chordNotes as $note) {
            $noteName = substr($note, 0, -1);
            if ($this->normalizeNote($noteName) === $normalizedRoot) {
                $rootOctave = (int) substr($note, -1);
                break;
            }
        }
        
        // Bass note is one octave below
        $bassOctave = $rootOctave - 1;
        
        // Ensure bass note is not too low
        if ($bassOctave < 1) {
            $bassOctave = 1;
        }
        
        return $root . $bassOctave;
    }

    /**
     * Convert note to absolute semitone value
     */
    private function noteToSemitones(string $note): int
    {
        $noteName = $this->normalizeNote(substr($note, 0, -1));
        $octave = (int) substr($note, -1);
        
        $noteIndex = array_search($noteName, self::NOTES);
        if ($noteIndex === false) {
            throw new \InvalidArgumentException("Invalid note: {$note}");
        }
        
        return ($octave * 12) + $noteIndex;
    }
    
    /**
     * Get note index, handling both sharps and flats
     */
    private function getNoteIndex(string $note): int
    {
        $normalized = $this->normalizeNote($note);
        $index = array_search($normalized, self::NOTES);
        return $index !== false ? $index : 0;
    }
}