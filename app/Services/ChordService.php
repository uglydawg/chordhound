<?php

declare(strict_types=1);

namespace App\Services;

class ChordService
{
    private array $noteToMidi = [
        'C' => 0, 'C#' => 1, 'D' => 2, 'D#' => 3,
        'E' => 4, 'F' => 5, 'F#' => 6, 'G' => 7,
        'G#' => 8, 'A' => 9, 'A#' => 10, 'B' => 11
    ];

    private array $chordIntervals = [
        'major' => [0, 4, 7],
        'minor' => [0, 3, 7],
        'diminished' => [0, 3, 6],
        'augmented' => [0, 4, 8],
    ];

    public function getChordNotes(string $rootNote, ?string $chordType = 'major', ?string $inversion = 'root'): array
    {
        $chordType = $chordType ?? 'major';
        $inversion = $inversion ?? 'root';

        if (!isset($this->noteToMidi[$rootNote])) {
            throw new \InvalidArgumentException("Invalid root note: $rootNote");
        }

        if (!isset($this->chordIntervals[$chordType])) {
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

    public function calculateBlueNotes(array $chords): array
    {
        // Blue notes are typically the flattened 3rd, 5th, and 7th degrees
        // In the context of this piano chord app, we'll identify notes that create
        // tension or dissonance when played together
        
        $allNotes = [];
        $blueNotes = [];

        // Collect all notes from all chords
        foreach ($chords as $chord) {
            if (!empty($chord['tone'])) {
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
            if (!empty($chord['tone'])) {
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
}