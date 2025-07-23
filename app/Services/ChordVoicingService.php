<?php

declare(strict_types=1);

namespace App\Services;

class ChordVoicingService
{
    /**
     * Get specific voicing for a chord in a progression
     * Returns array of notes with octaves (e.g., ['C4', 'E4', 'G4'])
     */
    public function getVoicing(string $progression, string $key, int $position, string $chordSymbol): array
    {
        $voicings = $this->getProgressionVoicings($progression);
        
        if (!isset($voicings[$key]) || !isset($voicings[$key][$position - 1])) {
            // Fallback to default voicing
            return $this->getDefaultVoicing($chordSymbol);
        }
        
        return $voicings[$key][$position - 1]['notes'];
    }
    
    /**
     * Get chord symbol for display (e.g., "F/C", "Am/C")
     */
    public function getChordSymbol(string $progression, string $key, int $position): string
    {
        $voicings = $this->getProgressionVoicings($progression);
        
        if (!isset($voicings[$key]) || !isset($voicings[$key][$position - 1])) {
            return '';
        }
        
        return $voicings[$key][$position - 1]['symbol'];
    }
    
    public function getProgressionVoicings(string $progression): array
    {
        return match($progression) {
            'I-IV-V' => $this->getIIVVVoicings(),
            'I-vi-IV-V' => $this->getIviIVVVoicings(),
            'vi-IV-I-V' => $this->getviIVIVVoicings(),
            'I-vi-ii-V' => $this->getIviiiVVoicings(),
            'ii-V-I' => $this->getiiVIVoicings(),
            default => []
        };
    }
    
    private function getIIVVVoicings(): array
    {
        return [
            'C' => [
                ['symbol' => 'C', 'notes' => ['C4', 'E4', 'G4']],
                ['symbol' => 'F/C', 'notes' => ['C4', 'F4', 'A4']],
                ['symbol' => 'G/B', 'notes' => ['B3', 'D4', 'G4']],
            ],
            'C#' => [
                ['symbol' => 'C#', 'notes' => ['C#4', 'F4', 'G#4']],
                ['symbol' => 'F#/C#', 'notes' => ['C#4', 'F#4', 'A#4']],
                ['symbol' => 'G#/C', 'notes' => ['C4', 'D#4', 'G#4']],
            ],
            'D' => [
                ['symbol' => 'D', 'notes' => ['D4', 'F#4', 'A4']],
                ['symbol' => 'G/D', 'notes' => ['D4', 'G4', 'B4']],
                ['symbol' => 'A/C#', 'notes' => ['C#4', 'E4', 'A4']],
            ],
            'D#' => [
                ['symbol' => 'D#', 'notes' => ['D#4', 'G4', 'A#4']],
                ['symbol' => 'G#/D#', 'notes' => ['D#4', 'G#4', 'C5']],
                ['symbol' => 'A#/D', 'notes' => ['D4', 'F4', 'A#4']],
            ],
            'E' => [
                ['symbol' => 'E', 'notes' => ['E4', 'G#4', 'B4']],
                ['symbol' => 'A/E', 'notes' => ['E4', 'A4', 'C#5']],
                ['symbol' => 'B/D#', 'notes' => ['D#4', 'F#4', 'B4']],
            ],
            'F' => [
                ['symbol' => 'F', 'notes' => ['F4', 'A4', 'C5']],
                ['symbol' => 'A#/F', 'notes' => ['F4', 'A#4', 'D5']],
                ['symbol' => 'C/E', 'notes' => ['E4', 'G4', 'C5']],
            ],
            'F#' => [
                ['symbol' => 'F#', 'notes' => ['F#3', 'A#3', 'C#4']],
                ['symbol' => 'B/F#', 'notes' => ['F#3', 'B3', 'D#4']],
                ['symbol' => 'C#/F', 'notes' => ['F3', 'G#3', 'C#4']],
            ],
            'G' => [
                ['symbol' => 'G', 'notes' => ['G3', 'B3', 'D4']],
                ['symbol' => 'C/G', 'notes' => ['G3', 'C4', 'E4']],
                ['symbol' => 'D/F#', 'notes' => ['F#3', 'A3', 'D4']],
            ],
            'G#' => [
                ['symbol' => 'G#', 'notes' => ['G#3', 'C4', 'D#4']],
                ['symbol' => 'C#/G#', 'notes' => ['G#3', 'C#4', 'F4']],
                ['symbol' => 'D#/G', 'notes' => ['G3', 'A#3', 'D#4']],
            ],
            'A' => [
                ['symbol' => 'A', 'notes' => ['A3', 'C#4', 'E4']],
                ['symbol' => 'D/A', 'notes' => ['A3', 'D4', 'F#4']],
                ['symbol' => 'E/G#', 'notes' => ['G#3', 'B3', 'E4']],
            ],
            'A#' => [
                ['symbol' => 'A#', 'notes' => ['A#3', 'D4', 'F4']],
                ['symbol' => 'D#/A#', 'notes' => ['A#3', 'D#4', 'G4']],
                ['symbol' => 'F/A', 'notes' => ['A3', 'C4', 'F4']],
            ],
            'B' => [
                ['symbol' => 'B', 'notes' => ['B3', 'D#4', 'F#4']],
                ['symbol' => 'E/B', 'notes' => ['B3', 'E4', 'G#4']],
                ['symbol' => 'F#/A#', 'notes' => ['A#3', 'C#4', 'F#4']],
            ],
        ];
    }
    
    private function getIviIVVVoicings(): array
    {
        return [
            'C' => [
                ['symbol' => 'C', 'notes' => ['C4', 'E4', 'G4']],
                ['symbol' => 'Am/C', 'notes' => ['C4', 'E4', 'A4']],
                ['symbol' => 'F/C', 'notes' => ['C4', 'F4', 'A4']],
                ['symbol' => 'G/B', 'notes' => ['B3', 'D4', 'G4']],
            ],
            'C#' => [
                ['symbol' => 'C#', 'notes' => ['C#4', 'F4', 'G#4']],
                ['symbol' => 'A#m/C#', 'notes' => ['C#4', 'F4', 'A#4']],
                ['symbol' => 'F#/C#', 'notes' => ['C#4', 'F#4', 'A#4']],
                ['symbol' => 'G#/C', 'notes' => ['C4', 'D#4', 'G#4']],
            ],
            'D' => [
                ['symbol' => 'D', 'notes' => ['D4', 'F#4', 'A4']],
                ['symbol' => 'Bm/D', 'notes' => ['D4', 'F#4', 'B4']],
                ['symbol' => 'G/D', 'notes' => ['D4', 'G4', 'B4']],
                ['symbol' => 'A/C#', 'notes' => ['C#4', 'E4', 'A4']],
            ],
            'D#' => [
                ['symbol' => 'D#', 'notes' => ['D#4', 'G4', 'A#4']],
                ['symbol' => 'Cm/D#', 'notes' => ['D#4', 'G4', 'C5']],
                ['symbol' => 'G#/D#', 'notes' => ['D#4', 'G#4', 'C5']],
                ['symbol' => 'A#/D', 'notes' => ['D4', 'F4', 'A#4']],
            ],
            'E' => [
                ['symbol' => 'E', 'notes' => ['E4', 'G#4', 'B4']],
                ['symbol' => 'C#m/E', 'notes' => ['E4', 'G#4', 'C#5']],
                ['symbol' => 'A/E', 'notes' => ['E4', 'A4', 'C#5']],
                ['symbol' => 'B/D#', 'notes' => ['D#4', 'F#4', 'B4']],
            ],
            'F' => [
                ['symbol' => 'F', 'notes' => ['F4', 'A4', 'C5']],
                ['symbol' => 'Dm/F', 'notes' => ['F4', 'A4', 'D5']],
                ['symbol' => 'A#/F', 'notes' => ['F4', 'A#4', 'D5']],
                ['symbol' => 'C/E', 'notes' => ['E4', 'G4', 'C5']],
            ],
            'F#' => [
                ['symbol' => 'F#', 'notes' => ['F#3', 'A#3', 'C#4']],
                ['symbol' => 'D#m/F#', 'notes' => ['F#3', 'A#3', 'D#4']],
                ['symbol' => 'B/F#', 'notes' => ['F#3', 'B3', 'D#4']],
                ['symbol' => 'C#/F', 'notes' => ['F3', 'G#3', 'C#4']],
            ],
            'G' => [
                ['symbol' => 'G', 'notes' => ['G3', 'B3', 'D4']],
                ['symbol' => 'Em/G', 'notes' => ['G3', 'B3', 'E4']],
                ['symbol' => 'C/G', 'notes' => ['G3', 'C4', 'E4']],
                ['symbol' => 'D/F#', 'notes' => ['F#3', 'A3', 'D4']],
            ],
            'G#' => [
                ['symbol' => 'G#', 'notes' => ['G#3', 'C4', 'D#4']],
                ['symbol' => 'Fm/G#', 'notes' => ['G#3', 'C4', 'F4']],
                ['symbol' => 'C#/G#', 'notes' => ['G#3', 'C#4', 'F4']],
                ['symbol' => 'D#/G', 'notes' => ['G3', 'A#3', 'D#4']],
            ],
            'A' => [
                ['symbol' => 'A', 'notes' => ['A3', 'C#4', 'E4']],
                ['symbol' => 'F#m/A', 'notes' => ['A3', 'C#4', 'F#4']],
                ['symbol' => 'D/A', 'notes' => ['A3', 'D4', 'F#4']],
                ['symbol' => 'E/G#', 'notes' => ['G#3', 'B3', 'E4']],
            ],
            'A#' => [
                ['symbol' => 'A#', 'notes' => ['A#3', 'D4', 'F4']],
                ['symbol' => 'Gm/A#', 'notes' => ['A#3', 'D4', 'G4']],
                ['symbol' => 'D#/A#', 'notes' => ['A#3', 'D#4', 'G4']],
                ['symbol' => 'F/A', 'notes' => ['A3', 'C4', 'F4']],
            ],
            'B' => [
                ['symbol' => 'B', 'notes' => ['B3', 'D#4', 'F#4']],
                ['symbol' => 'G#m/B', 'notes' => ['B3', 'D#4', 'G#4']],
                ['symbol' => 'E/B', 'notes' => ['B3', 'E4', 'G#4']],
                ['symbol' => 'F#/A#', 'notes' => ['A#3', 'C#4', 'F#4']],
            ],
        ];
    }
    
    private function getviIVIVVoicings(): array
    {
        return [
            'C' => [
                ['symbol' => 'Am', 'notes' => ['A3', 'C4', 'E4']],
                ['symbol' => 'F/A', 'notes' => ['A3', 'C4', 'F4']],
                ['symbol' => 'C/G', 'notes' => ['G3', 'C4', 'E4']],
                ['symbol' => 'G', 'notes' => ['G3', 'B3', 'D4']],
            ],
            'C#' => [
                ['symbol' => 'A#m', 'notes' => ['A#3', 'C#4', 'F4']],
                ['symbol' => 'F#/A#', 'notes' => ['A#3', 'C#4', 'F#4']],
                ['symbol' => 'C#/G#', 'notes' => ['G#3', 'C#4', 'F4']],
                ['symbol' => 'G#', 'notes' => ['G#3', 'C4', 'D#4']],
            ],
            'D' => [
                ['symbol' => 'Bm', 'notes' => ['B3', 'D4', 'F#4']],
                ['symbol' => 'G/B', 'notes' => ['B3', 'D4', 'G4']],
                ['symbol' => 'D/A', 'notes' => ['A3', 'D4', 'F#4']],
                ['symbol' => 'A', 'notes' => ['A3', 'C#4', 'E4']],
            ],
            'D#' => [
                ['symbol' => 'Cm', 'notes' => ['C4', 'D#4', 'G4']],
                ['symbol' => 'G#/C', 'notes' => ['C4', 'D#4', 'G#4']],
                ['symbol' => 'D#/A#', 'notes' => ['A#3', 'D#4', 'G4']],
                ['symbol' => 'A#', 'notes' => ['A#3', 'D4', 'F4']],
            ],
            'E' => [
                ['symbol' => 'C#m', 'notes' => ['C#4', 'E4', 'G#4']],
                ['symbol' => 'A/C#', 'notes' => ['C#4', 'E4', 'A4']],
                ['symbol' => 'E/B', 'notes' => ['B3', 'E4', 'G#4']],
                ['symbol' => 'B', 'notes' => ['B3', 'D#4', 'F#4']],
            ],
            'F' => [
                ['symbol' => 'Dm', 'notes' => ['D4', 'F4', 'A4']],
                ['symbol' => 'A#/D', 'notes' => ['D4', 'F4', 'A#4']],
                ['symbol' => 'F/C', 'notes' => ['C4', 'F4', 'A4']],
                ['symbol' => 'C', 'notes' => ['C4', 'E4', 'G4']],
            ],
            'F#' => [
                ['symbol' => 'D#m', 'notes' => ['D#4', 'F#4', 'A#4']],
                ['symbol' => 'B/D#', 'notes' => ['D#4', 'F#4', 'B4']],
                ['symbol' => 'F#/C#', 'notes' => ['C#4', 'F#4', 'A#4']],
                ['symbol' => 'C#', 'notes' => ['C#4', 'F4', 'G#4']],
            ],
            'G' => [
                ['symbol' => 'Em', 'notes' => ['E4', 'G4', 'B4']],
                ['symbol' => 'C/E', 'notes' => ['E4', 'G4', 'C5']],
                ['symbol' => 'G/D', 'notes' => ['D4', 'G4', 'B4']],
                ['symbol' => 'D', 'notes' => ['D4', 'F#4', 'A4']],
            ],
            'G#' => [
                ['symbol' => 'Fm', 'notes' => ['F4', 'G#4', 'C5']],
                ['symbol' => 'C#/F', 'notes' => ['F4', 'G#4', 'C#5']],
                ['symbol' => 'G#/D#', 'notes' => ['D#4', 'G#4', 'C5']],
                ['symbol' => 'D#', 'notes' => ['D#4', 'G4', 'A#4']],
            ],
            'A' => [
                ['symbol' => 'F#m', 'notes' => ['F#3', 'A3', 'C#4']],
                ['symbol' => 'D/F#', 'notes' => ['F#3', 'A3', 'D4']],
                ['symbol' => 'A/E', 'notes' => ['E3', 'A3', 'C#4']],
                ['symbol' => 'E', 'notes' => ['E3', 'G#3', 'B3']],
            ],
            'A#' => [
                ['symbol' => 'Gm', 'notes' => ['G3', 'A#3', 'D4']],
                ['symbol' => 'D#/G', 'notes' => ['G3', 'A#3', 'D#4']],
                ['symbol' => 'A#/F', 'notes' => ['F3', 'A#3', 'D4']],
                ['symbol' => 'F', 'notes' => ['F3', 'A3', 'C4']],
            ],
            'B' => [
                ['symbol' => 'G#m', 'notes' => ['G#3', 'B3', 'D#4']],
                ['symbol' => 'E/G#', 'notes' => ['G#3', 'B3', 'E4']],
                ['symbol' => 'B/F#', 'notes' => ['F#3', 'B3', 'D#4']],
                ['symbol' => 'F#', 'notes' => ['F#3', 'A#3', 'C#4']],
            ],
        ];
    }
    
    private function getIviiiVVoicings(): array
    {
        return [
            'C' => [
                ['symbol' => 'C', 'notes' => ['C4', 'E4', 'G4']],
                ['symbol' => 'Am/C', 'notes' => ['C4', 'E4', 'A4']],
                ['symbol' => 'Dm', 'notes' => ['D4', 'F4', 'A4']],
                ['symbol' => 'G/B', 'notes' => ['B3', 'D4', 'G4']],
            ],
            'C#' => [
                ['symbol' => 'C#', 'notes' => ['C#4', 'F4', 'G#4']],
                ['symbol' => 'A#m/C#', 'notes' => ['C#4', 'F4', 'A#4']],
                ['symbol' => 'D#m', 'notes' => ['D#4', 'F#4', 'A#4']],
                ['symbol' => 'G#/C', 'notes' => ['C4', 'D#4', 'G#4']],
            ],
            'D' => [
                ['symbol' => 'D', 'notes' => ['D4', 'F#4', 'A4']],
                ['symbol' => 'Bm/D', 'notes' => ['D4', 'F#4', 'B4']],
                ['symbol' => 'Em', 'notes' => ['E4', 'G4', 'B4']],
                ['symbol' => 'A/C#', 'notes' => ['C#4', 'E4', 'A4']],
            ],
            'D#' => [
                ['symbol' => 'D#', 'notes' => ['D#4', 'G4', 'A#4']],
                ['symbol' => 'Cm/D#', 'notes' => ['D#4', 'G4', 'C5']],
                ['symbol' => 'Fm', 'notes' => ['F4', 'G#4', 'C5']],
                ['symbol' => 'A#/D', 'notes' => ['D4', 'F4', 'A#4']],
            ],
            'E' => [
                ['symbol' => 'E', 'notes' => ['E4', 'G#4', 'B4']],
                ['symbol' => 'C#m/E', 'notes' => ['E4', 'G#4', 'C#5']],
                ['symbol' => 'F#m', 'notes' => ['F#4', 'A4', 'C#5']],
                ['symbol' => 'B/D#', 'notes' => ['D#4', 'F#4', 'B4']],
            ],
            'F' => [
                ['symbol' => 'F', 'notes' => ['F4', 'A4', 'C5']],
                ['symbol' => 'Dm/F', 'notes' => ['F4', 'A4', 'D5']],
                ['symbol' => 'Gm', 'notes' => ['G4', 'A#4', 'D5']],
                ['symbol' => 'C/E', 'notes' => ['E4', 'G4', 'C5']],
            ],
            'F#' => [
                ['symbol' => 'F#', 'notes' => ['F#3', 'A#3', 'C#4']],
                ['symbol' => 'D#m/F#', 'notes' => ['F#3', 'A#3', 'D#4']],
                ['symbol' => 'G#m', 'notes' => ['G#3', 'B3', 'D#4']],
                ['symbol' => 'C#/F', 'notes' => ['F3', 'G#3', 'C#4']],
            ],
            'G' => [
                ['symbol' => 'G', 'notes' => ['G3', 'B3', 'D4']],
                ['symbol' => 'Em/G', 'notes' => ['G3', 'B3', 'E4']],
                ['symbol' => 'Am', 'notes' => ['A3', 'C4', 'E4']],
                ['symbol' => 'D/F#', 'notes' => ['F#3', 'A3', 'D4']],
            ],
            'G#' => [
                ['symbol' => 'G#', 'notes' => ['G#3', 'C4', 'D#4']],
                ['symbol' => 'Fm/G#', 'notes' => ['G#3', 'C4', 'F4']],
                ['symbol' => 'A#m', 'notes' => ['A#3', 'C#4', 'F4']],
                ['symbol' => 'D#/G', 'notes' => ['G3', 'A#3', 'D#4']],
            ],
            'A' => [
                ['symbol' => 'A', 'notes' => ['A3', 'C#4', 'E4']],
                ['symbol' => 'F#m/A', 'notes' => ['A3', 'C#4', 'F#4']],
                ['symbol' => 'Bm', 'notes' => ['B3', 'D4', 'F#4']],
                ['symbol' => 'E/G#', 'notes' => ['G#3', 'B3', 'E4']],
            ],
            'A#' => [
                ['symbol' => 'A#', 'notes' => ['A#3', 'D4', 'F4']],
                ['symbol' => 'Gm/A#', 'notes' => ['A#3', 'D4', 'G4']],
                ['symbol' => 'Cm', 'notes' => ['C4', 'D#4', 'G4']],
                ['symbol' => 'F/A', 'notes' => ['A3', 'C4', 'F4']],
            ],
            'B' => [
                ['symbol' => 'B', 'notes' => ['B3', 'D#4', 'F#4']],
                ['symbol' => 'G#m/B', 'notes' => ['B3', 'D#4', 'G#4']],
                ['symbol' => 'C#m', 'notes' => ['C#4', 'E4', 'G#4']],
                ['symbol' => 'F#/A#', 'notes' => ['A#3', 'C#4', 'F#4']],
            ],
        ];
    }
    
    private function getiiVIVoicings(): array
    {
        return [
            'C' => [
                ['symbol' => 'Dm/A', 'notes' => ['A3', 'D4', 'F4']],
                ['symbol' => 'G/B', 'notes' => ['B3', 'D4', 'G4']],
                ['symbol' => 'C', 'notes' => ['C4', 'E4', 'G4']],
            ],
            'C#' => [
                ['symbol' => 'D#m/A#', 'notes' => ['A#3', 'D#4', 'F#4']],
                ['symbol' => 'G#/C', 'notes' => ['C4', 'D#4', 'G#4']],
                ['symbol' => 'C#', 'notes' => ['C#4', 'F4', 'G#4']],
            ],
            'D' => [
                ['symbol' => 'Em/B', 'notes' => ['B3', 'E4', 'G4']],
                ['symbol' => 'A/C#', 'notes' => ['C#4', 'E4', 'A4']],
                ['symbol' => 'D', 'notes' => ['D4', 'F#4', 'A4']],
            ],
            'D#' => [
                ['symbol' => 'Fm/C', 'notes' => ['C4', 'F4', 'G#4']],
                ['symbol' => 'A#/D', 'notes' => ['D4', 'F4', 'A#4']],
                ['symbol' => 'D#', 'notes' => ['D#4', 'G4', 'A#4']],
            ],
            'E' => [
                ['symbol' => 'F#m/C#', 'notes' => ['C#4', 'F#4', 'A4']],
                ['symbol' => 'B/D#', 'notes' => ['D#4', 'F#4', 'B4']],
                ['symbol' => 'E', 'notes' => ['E4', 'G#4', 'B4']],
            ],
            'F' => [
                ['symbol' => 'Gm/D', 'notes' => ['D4', 'G4', 'A#4']],
                ['symbol' => 'C/E', 'notes' => ['E4', 'G4', 'C5']],
                ['symbol' => 'F', 'notes' => ['F4', 'A4', 'C5']],
            ],
            'F#' => [
                ['symbol' => 'G#m/D#', 'notes' => ['D#4', 'G#4', 'B4']],
                ['symbol' => 'C#/F', 'notes' => ['F4', 'G#4', 'C#5']],
                ['symbol' => 'F#', 'notes' => ['F#4', 'A#4', 'C#5']],
            ],
            'G' => [
                ['symbol' => 'Am/E', 'notes' => ['E4', 'A4', 'C5']],
                ['symbol' => 'D/F#', 'notes' => ['F#4', 'A4', 'D5']],
                ['symbol' => 'G', 'notes' => ['G4', 'B4', 'D5']],
            ],
            'G#' => [
                ['symbol' => 'A#m/F', 'notes' => ['F4', 'A#4', 'C#5']],
                ['symbol' => 'D#/G', 'notes' => ['G4', 'A#4', 'D#5']],
                ['symbol' => 'G#', 'notes' => ['G#4', 'C5', 'D#5']],
            ],
            'A' => [
                ['symbol' => 'Bm/F#', 'notes' => ['F#3', 'B3', 'D4']],
                ['symbol' => 'E/G#', 'notes' => ['G#3', 'B3', 'E4']],
                ['symbol' => 'A', 'notes' => ['A3', 'C#4', 'E4']],
            ],
            'A#' => [
                ['symbol' => 'Cm/G', 'notes' => ['G3', 'C4', 'D#4']],
                ['symbol' => 'F/A', 'notes' => ['A3', 'C4', 'F4']],
                ['symbol' => 'A#', 'notes' => ['A#3', 'D4', 'F4']],
            ],
            'B' => [
                ['symbol' => 'C#m/G#', 'notes' => ['G#3', 'C#4', 'E4']],
                ['symbol' => 'F#/A#', 'notes' => ['A#3', 'C#4', 'F#4']],
                ['symbol' => 'B', 'notes' => ['B3', 'D#4', 'F#4']],
            ],
        ];
    }
    
    private function getDefaultVoicing(string $chordSymbol): array
    {
        // Parse chord symbol and return default voicing
        // This is a fallback for chords not in the tables
        $root = $chordSymbol[0];
        if (strlen($chordSymbol) > 1 && $chordSymbol[1] === '#') {
            $root .= '#';
        }
        
        // Return simple root position triad in C4
        return [$root . '4', $this->getThird($root, str_contains($chordSymbol, 'm')) . '4', $this->getFifth($root) . '4'];
    }
    
    private function getThird(string $root, bool $isMinor): string
    {
        $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $index = array_search($root, $notes);
        $interval = $isMinor ? 3 : 4;
        return $notes[($index + $interval) % 12];
    }
    
    private function getFifth(string $root): string
    {
        $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $index = array_search($root, $notes);
        return $notes[($index + 7) % 12];
    }
}