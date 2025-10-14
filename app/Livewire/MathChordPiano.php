<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Attributes\On;
use Livewire\Component;

class MathChordPiano extends Component
{
    #[Reactive]
    public array $calculatedNotes = [];

    public string $chordName = '';

    public bool $showLabels = false;

    public bool $larger = false;
    
    public ?string $highlightedNote = null;
    
    public array $pressedKeys = [];

    #[On('highlight-note')]
    public function highlightNote($note): void
    {
        $this->highlightedNote = $note;
    }
    
    #[On('press-key')]
    public function pressKey($note): void
    {
        if (!in_array($note, $this->pressedKeys)) {
            $this->pressedKeys[] = $note;
        }
    }
    
    #[On('release-key')]
    public function releaseKey($note): void
    {
        $this->pressedKeys = array_filter($this->pressedKeys, fn($key) => $key !== $note);
    }

    public function render()
    {
        $pianoKeys = [];
        $activeNotes = $this->calculatedNotes;

        // Create a mini piano (2 octaves centered around middle C)
        $whiteKeyWidth = $this->larger ? 30 : 20;
        $blackKeyWidth = $this->larger ? 18 : 12;  // Narrower black keys
        $whiteKeyOffsets = ['C' => 0, 'D' => 1, 'E' => 2, 'F' => 3, 'G' => 4, 'A' => 5, 'B' => 6];
        // Black keys positioned between white keys (no black key between E-F and B-C)
        $blackKeyPositions = [
            'C#' => 0.75,  // Between C and D
            'D#' => 1.75,  // Between D and E
            'F#' => 3.75,  // Between F and G
            'G#' => 4.75,  // Between G and A
            'A#' => 5.75,  // Between A and B
        ];

        $x = 0;
        for ($octave = 3; $octave <= 5; $octave++) {
            foreach (['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'] as $note) {
                $fullNote = $note.$octave;
                $isActive = in_array($fullNote, $activeNotes);
                $isHighlighted = $this->highlightedNote === $fullNote;
                $isPressed = in_array($fullNote, $this->pressedKeys);

                if (strpos($note, '#') === false) {
                    // White key
                    $pianoKeys[] = [
                        'type' => 'white',
                        'note' => $note,
                        'octave' => $octave,
                        'x' => $x + ($whiteKeyOffsets[$note] * $whiteKeyWidth),
                        'width' => $whiteKeyWidth,
                        'isActive' => $isActive,
                        'isHighlighted' => $isHighlighted,
                        'isPressed' => $isPressed,
                    ];
                }
            }

            // Black keys
            foreach ($blackKeyPositions as $note => $offset) {
                $fullNote = $note.$octave;
                $isActive = in_array($fullNote, $activeNotes);
                $isHighlighted = $this->highlightedNote === $fullNote;
                $isPressed = in_array($fullNote, $this->pressedKeys);

                $pianoKeys[] = [
                    'type' => 'black',
                    'note' => $note,
                    'octave' => $octave,
                    'x' => $x + ($offset * $whiteKeyWidth),
                    'width' => $blackKeyWidth,
                    'isActive' => $isActive,
                    'isHighlighted' => $isHighlighted,
                    'isPressed' => $isPressed,
                ];
            }

            $x += 7 * $whiteKeyWidth;
        }

        $totalWidth = 21 * $whiteKeyWidth; // 3 octaves * 7 white keys

        return view('livewire.math-chord-piano', [
            'pianoKeys' => $pianoKeys,
            'totalWidth' => $totalWidth,
        ]);
    }
}