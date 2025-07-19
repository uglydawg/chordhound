<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChordService;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ChordPiano extends Component
{
    #[Reactive]
    public array $chord = [];
    
    public int $position = 0;
    
    private ChordService $chordService;
    
    public function boot()
    {
        $this->chordService = app(ChordService::class);
    }
    
    public function render()
    {
        $pianoKeys = [];
        $activeNotes = [];
        
        if (!empty($this->chord['tone'])) {
            $notes = $this->chordService->getChordNotes(
                $this->chord['tone'],
                $this->chord['semitone'] ?? 'major',
                $this->chord['inversion'] ?? 'root'
            );
            
            // Add octave to each note for display
            foreach ($notes as $note) {
                // Add notes in both octaves to show on our mini piano
                $activeNotes[] = $note . '4';
                $activeNotes[] = $note . '5';
            }
        }
        
        // Create a mini piano (2 octaves centered around middle C)
        $whiteKeyWidth = 20;
        $blackKeyWidth = 14;
        $whiteKeyOffsets = ['C' => 0, 'D' => 1, 'E' => 2, 'F' => 3, 'G' => 4, 'A' => 5, 'B' => 6];
        $blackKeyPositions = ['C#' => 0.7, 'D#' => 1.7, 'F#' => 3.7, 'G#' => 4.7, 'A#' => 5.7];
        
        $x = 0;
        for ($octave = 4; $octave <= 5; $octave++) {
            foreach (['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'] as $note) {
                $fullNote = $note . $octave;
                $isActive = in_array($fullNote, $activeNotes);
                
                if (strpos($note, '#') === false) {
                    // White key
                    $pianoKeys[] = [
                        'type' => 'white',
                        'note' => $note,
                        'octave' => $octave,
                        'x' => $x + ($whiteKeyOffsets[$note] * $whiteKeyWidth),
                        'width' => $whiteKeyWidth,
                        'isActive' => $isActive,
                        'isBlueNote' => $this->chord['is_blue_note'] ?? false,
                    ];
                }
            }
            
            // Black keys
            foreach ($blackKeyPositions as $note => $offset) {
                $fullNote = $note . $octave;
                $isActive = in_array($fullNote, $activeNotes);
                
                $pianoKeys[] = [
                    'type' => 'black',
                    'note' => $note,
                    'octave' => $octave,
                    'x' => $x + ($offset * $whiteKeyWidth) - ($blackKeyWidth / 2),
                    'width' => $blackKeyWidth,
                    'isActive' => $isActive,
                    'isBlueNote' => $this->chord['is_blue_note'] ?? false,
                ];
            }
            
            $x += 7 * $whiteKeyWidth;
        }
        
        $totalWidth = 14 * $whiteKeyWidth; // 2 octaves * 7 white keys
        
        return view('livewire.chord-piano', [
            'pianoKeys' => $pianoKeys,
            'totalWidth' => $totalWidth,
        ]);
    }
}