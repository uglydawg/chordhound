<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class PrintChordSheet extends Component
{
    public array $selectedChords = [1, 2, 3, 4];
    public array $chords = [];
    
    #[On('selected-chords-updated')]
    public function updateSelectedChords($selectedChords)
    {
        $this->selectedChords = $selectedChords;
    }
    
    #[On('chordsUpdated')]
    public function updateChords($chords)
    {
        $this->chords = $chords;
    }
    
    public function printSheet()
    {
        $this->dispatch('print-chord-sheet');
    }
    
    public function render()
    {
        return view('livewire.print-chord-sheet');
    }
}