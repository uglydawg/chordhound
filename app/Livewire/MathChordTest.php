<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MathematicalChordService;
use Livewire\Component;

class MathChordTest extends Component
{
    public string $root = 'C';
    public string $type = 'major';
    public string $startPosition = 'C4';
    public int $inversion = 0;
    
    public array $calculatedNotes = [];
    public string $error = '';
    
    // Available options
    public array $roots = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    public array $types = ['major', 'minor', 'diminished', 'augmented'];
    public array $inversions = [
        0 => 'Root Position',
        1 => 'First Inversion',
        2 => 'Second Inversion'
    ];
    
    protected MathematicalChordService $chordService;
    
    public function boot()
    {
        $this->chordService = new MathematicalChordService();
    }
    
    public function mount()
    {
        $this->calculateChord();
    }
    
    public function calculateChord()
    {
        $this->error = '';
        
        try {
            $this->calculatedNotes = $this->chordService->calculateChord(
                $this->root,
                $this->type,
                $this->startPosition,
                $this->inversion
            );
            
            // Dispatch event to play the chord
            $this->dispatch('play-math-chord', notes: $this->calculatedNotes);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->calculatedNotes = [];
        }
    }
    
    public function playChord()
    {
        if (!empty($this->calculatedNotes)) {
            $this->dispatch('play-math-chord', notes: $this->calculatedNotes);
        }
    }
    
    public function compareWithHardcoded()
    {
        // Calculate voice leading distance if comparing two chords
        if ($this->root === 'C' && $this->type === 'major') {
            $chord1 = $this->calculatedNotes;
            $chord2 = $this->chordService->calculateChord('F', 'major', $this->startPosition, $this->inversion);
            $distance = $this->chordService->calculateVoiceLeadingDistance($chord1, $chord2);
            
            session()->flash('voiceLeading', "Voice leading distance from C to F: $distance semitones");
        }
    }
    
    public function updated($property)
    {
        if (in_array($property, ['root', 'type', 'startPosition', 'inversion'])) {
            $this->calculateChord();
        }
    }
    
    public function render()
    {
        return view('livewire.math-chord-test');
    }
}