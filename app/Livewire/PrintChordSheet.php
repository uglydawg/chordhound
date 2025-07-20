<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class PrintChordSheet extends Component
{
    public array $chords = [];
    
    #[On('chordsUpdated')]
    public function updateChords($event)
    {
        if (isset($event['chords'])) {
            $this->chords = $event['chords'];
        }
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