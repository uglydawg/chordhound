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
    
    // Key and progression
    public string $selectedKey = 'C';
    public string $selectedProgression = 'I-IV-V-I';
    public bool $isPlaying = false;
    public int $currentChordIndex = 0;
    public float $playbackSpeed = 1.0; // seconds between chords
    
    // Available options
    public array $roots = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    public array $types = ['major', 'minor', 'diminished', 'augmented'];
    public array $inversions = [
        0 => 'Root Position',
        1 => 'First Inversion',
        2 => 'Second Inversion'
    ];
    
    // Common progressions
    public array $progressions = [
        'I-IV-V-I' => ['I', 'IV', 'V', 'I'],
        'I-V-vi-IV' => ['I', 'V', 'vi', 'IV'],
        'I-vi-IV-V' => ['I', 'vi', 'IV', 'V'],
        'ii-V-I' => ['ii', 'V', 'I'],
        'I-IV-I-V' => ['I', 'IV', 'I', 'V'],
        'vi-IV-I-V' => ['vi', 'IV', 'I', 'V'],
        'I-iv-I-II' => ['I', 'iv', 'I', 'II'], // Your requested progression
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
            
            // Dispatch events
            $this->dispatch('play-math-chord', notes: $this->calculatedNotes);
            $this->dispatch('chord-notes-updated', notes: $this->calculatedNotes);
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
    
    // Convert Roman numeral to root note based on key
    private function romanToRoot(string $roman, string $key): array
    {
        $keyIndex = array_search($key, $this->roots);
        if ($keyIndex === false) return ['C', 'major'];
        
        // Define intervals for each roman numeral
        $romanIntervals = [
            'I' => [0, 'major'],
            'ii' => [2, 'minor'],
            'iii' => [4, 'minor'],
            'IV' => [5, 'major'],
            'V' => [7, 'major'],
            'vi' => [9, 'minor'],
            'vii°' => [11, 'diminished'],
            // Borrowed chords
            'iv' => [5, 'minor'], // Borrowed from parallel minor
            'II' => [2, 'major'], // Secondary dominant
        ];
        
        if (!isset($romanIntervals[$roman])) return ['C', 'major'];
        
        [$interval, $type] = $romanIntervals[$roman];
        $rootIndex = ($keyIndex + $interval) % 12;
        
        return [$this->roots[$rootIndex], $type];
    }
    
    public function playProgression()
    {
        $this->isPlaying = true;
        $this->currentChordIndex = 0;
        $this->playNextChord();
    }
    
    public function stopProgression()
    {
        $this->isPlaying = false;
        $this->currentChordIndex = 0;
    }
    
    public function playNextChord()
    {
        if (!$this->isPlaying) return;
        
        $progression = $this->progressions[$this->selectedProgression];
        if ($this->currentChordIndex >= count($progression)) {
            $this->stopProgression();
            return;
        }
        
        // Get the current chord in the progression
        $roman = $progression[$this->currentChordIndex];
        [$chordRoot, $chordType] = $this->romanToRoot($roman, $this->selectedKey);
        
        // Calculate and play the chord
        $notes = $this->chordService->calculateChord($chordRoot, $chordType, $this->startPosition, 0);
        $this->calculatedNotes = $notes;
        $this->root = $chordRoot;
        $this->type = $chordType;
        
        // Dispatch events
        $this->dispatch('play-math-chord', notes: $notes);
        $this->dispatch('chord-notes-updated', notes: $notes); // Update piano visualization
        $this->dispatch('progression-chord-changed', 
            index: $this->currentChordIndex, 
            roman: $roman,
            root: $chordRoot,
            type: $chordType
        );
        
        // Schedule next chord
        $this->currentChordIndex++;
        if ($this->currentChordIndex < count($progression)) {
            $this->dispatch('schedule-next-chord', delay: $this->playbackSpeed * 1000);
        } else {
            // Loop back to beginning after a delay
            $this->currentChordIndex = 0;
            if ($this->isPlaying) {
                $this->dispatch('schedule-next-chord', delay: $this->playbackSpeed * 1000);
            }
        }
    }
    
    public function setPlaybackSpeed($speed)
    {
        $this->playbackSpeed = (float) $speed;
    }
    
    public function render()
    {
        return view('livewire.math-chord-test');
    }
}