<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class MidiPlayer extends Component
{
    public int $tempo = 120;
    public bool $isPlaying = false;
    public float $currentTime = 0;
    public float $duration = 8; // 8 beats for 4 chords (2 beats each)
    public array $chords = [];
    
    #[On('chordsUpdated')]
    public function updateChords($chords)
    {
        $this->chords = $chords;
    }
    
    public function togglePlayback()
    {
        $this->isPlaying = !$this->isPlaying;
        $this->dispatch('toggle-playback', isPlaying: $this->isPlaying);
    }
    
    public function stop()
    {
        $this->isPlaying = false;
        $this->currentTime = 0;
        $this->dispatch('stop-playback');
    }
    
    public function updateTempo($tempo)
    {
        $this->tempo = (int) $tempo;
        $this->dispatch('tempo-changed', tempo: $this->tempo);
    }
    
    public function render()
    {
        return view('livewire.midi-player');
    }
}