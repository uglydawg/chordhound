<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class TestPiano2d extends Component
{
    public array $activeNotes = [];

    public array $testChords = [
        'C Major' => ['C4', 'E4', 'G4'],
        'D Minor' => ['D4', 'F4', 'A4'],
        'E Minor' => ['E4', 'G4', 'B4'],
        'F Major' => ['F4', 'A4', 'C5'],
        'G Major' => ['G4', 'B4', 'D5'],
        'A Minor' => ['A4', 'C5', 'E5'],
        'B Diminished' => ['B4', 'D5', 'F5'],
    ];

    public array $twoHandedExamples = [
        'Simple C Chord' => [
            'left' => ['C3', 'G3'],
            'right' => ['C4', 'E4', 'G4']
        ],
        'Walking Bass' => [
            'left' => ['C2', 'E2', 'G2'],
            'right' => ['C4', 'E4', 'G4', 'C5']
        ],
        'Alberti Bass' => [
            'left' => ['C3', 'G3', 'E3', 'G3'],
            'right' => ['C5', 'E5', 'G5']
        ],
    ];

    public function setChord(string $chordName): void
    {
        $this->activeNotes = $this->testChords[$chordName] ?? [];
        $this->dispatch('chord-changed', notes: $this->activeNotes);
        $this->dispatch('play-chord', notes: $this->activeNotes);
    }

    public function clearChord(): void
    {
        $this->activeNotes = [];
        $this->dispatch('chord-changed', notes: $this->activeNotes);
        $this->dispatch('clear-all-hands');
        $this->dispatch('stop-audio');
    }

    public function setTwoHanded(string $exampleName): void
    {
        $example = $this->twoHandedExamples[$exampleName] ?? null;
        if ($example) {
            $this->dispatch('set-both-hands',
                leftNotes: $example['left'],
                rightNotes: $example['right']
            );

            // Also play the audio
            $allNotes = array_merge($example['left'], $example['right']);
            $this->dispatch('play-chord', notes: $allNotes);
        }
    }

    public function handleKeyClick(string $note): void
    {
        // Toggle note on/off
        $key = array_search($note, $this->activeNotes);
        if ($key !== false) {
            unset($this->activeNotes[$key]);
            $this->activeNotes = array_values($this->activeNotes); // Re-index
        } else {
            $this->activeNotes[] = $note;
        }

        $this->dispatch('chord-changed', notes: $this->activeNotes);
        $this->dispatch('play-note', note: $note);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.test-piano2d');
    }
}
