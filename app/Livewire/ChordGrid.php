<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChordService;
use Livewire\Attributes\On;
use Livewire\Component;

class ChordGrid extends Component
{
    public array $chords = [];
    public array $blueNotes = [];
    public ?int $chordSetId = null;
    public int $activePosition = 1;
    public bool $showSuggestions = false;
    public string $selectedKey = 'G';
    public string $selectedKeyType = 'major';
    public bool $showRomanNumerals = false;
    public array $romanNumerals = [];
    public string $selectedProgression = '';
    
    private ChordService $chordService;
    
    // Common chord progressions as roman numerals (based on image and popular progressions)
    private array $chordProgressions = [
        'I-IV-V' => ['I', 'IV', 'V'],
        'I-V-vi-IV' => ['I', 'V', 'vi', 'IV'],
        'I-vi-IV-V' => ['I', 'vi', 'IV', 'V'],
        'vi-IV-I-V' => ['vi', 'IV', 'I', 'V'],
        'I-vi-ii-V' => ['I', 'vi', 'ii', 'V'],
        'ii-V-I' => ['ii', 'V', 'I'],
    ];
    
    // Progression descriptions
    private array $progressionDescriptions = [
        'I-IV-V' => 'Classic Rock/Blues',
        'I-V-vi-IV' => 'Pop Progression',
        'I-vi-IV-V' => '50s Doo-Wop',
        'vi-IV-I-V' => 'Alternative Pop',
        'I-vi-ii-V' => 'Jazz Standard',
        'ii-V-I' => 'Jazz Cadence',
    ];

    public function mount(?int $chordSetId = null)
    {
        $this->chordSetId = $chordSetId;
        
        // Initialize with 4 chord slots
        for ($i = 1; $i <= 4; $i++) {
            $this->chords[$i] = [
                'position' => $i,
                'tone' => '',
                'semitone' => 'major',
                'inversion' => 'root',
                'is_blue_note' => false,
            ];
        }

        // Load existing chords if editing
        if ($this->chordSetId) {
            $this->loadChords();
        } else {
            // Set default progression to I-V-vi-IV
            $this->selectedProgression = 'I-V-vi-IV';
            $this->applySelectedProgression();
            
            $this->calculateBlueNotes();
            $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
        }
    }

    public function boot()
    {
        $this->chordService = app(ChordService::class);
    }

    private function loadChords()
    {
        $chordSet = \App\Models\ChordSet::find($this->chordSetId);
        if ($chordSet) {
            foreach ($chordSet->chords as $chord) {
                if ($chord->position <= 4) {
                    $this->chords[$chord->position] = [
                        'position' => $chord->position,
                        'tone' => $chord->tone,
                        'semitone' => $chord->semitone ?? 'major',
                        'inversion' => $chord->inversion ?? 'root',
                        'is_blue_note' => $chord->is_blue_note,
                    ];
                }
            }
        }
    }

    public function selectChord($position)
    {
        $this->activePosition = $position;
    }

    public function setChord($tone, $semitone = 'major')
    {
        $this->chords[$this->activePosition] = [
            'position' => $this->activePosition,
            'tone' => $tone,
            'semitone' => $semitone,
            'inversion' => $this->chords[$this->activePosition]['inversion'] ?? 'root',
            'is_blue_note' => false,
        ];
        
        $this->calculateBlueNotes();
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
        $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
    }
    
    public function setInversion($inversion)
    {
        if (isset($this->chords[$this->activePosition]) && $this->chords[$this->activePosition]['tone']) {
            $this->chords[$this->activePosition]['inversion'] = $inversion;
            $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
        }
    }

    public function clearChord($position)
    {
        $this->chords[$position] = [
            'position' => $position,
            'tone' => '',
            'semitone' => 'major',
            'inversion' => 'root',
            'is_blue_note' => false,
        ];
        
        $this->calculateBlueNotes();
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
        $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
    }

    public function toggleSuggestions()
    {
        $this->showSuggestions = !$this->showSuggestions;
    }

    public function setKey($key)
    {
        $this->selectedKey = $key;
        if ($this->selectedProgression) {
            $this->applySelectedProgression();
        }
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
    }
    
    public function setKeyType($keyType)
    {
        $this->selectedKeyType = $keyType;
        if ($this->selectedProgression) {
            $this->applySelectedProgression();
        }
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
    }
    
    public function setProgression($progressionKey)
    {
        $this->selectedProgression = $progressionKey;
        if ($progressionKey) {
            $this->applySelectedProgression();
        }
    }
    
    private function applySelectedProgression()
    {
        if (isset($this->chordProgressions[$this->selectedProgression])) {
            $this->applyProgression($this->selectedProgression, false);
        }
    }
    
    public function applyProgression($progressionKey, $autoInversion = false)
    {
        if (isset($this->chordProgressions[$progressionKey])) {
            $romanNumerals = $this->chordProgressions[$progressionKey];
            $progression = $this->chordService->transposeProgression($this->selectedKey, $this->selectedKeyType, $romanNumerals);
            
            foreach ($progression as $index => $chord) {
                if ($index < 4) {
                    $position = $index + 1;
                    
                    // Determine inversion
                    $inversion = 'root';
                    if ($autoInversion && $index > 0) {
                        // Calculate optimal inversion based on previous chord
                        $prevChord = $this->chords[$position - 1];
                        if (!empty($prevChord['tone'])) {
                            $inversion = $this->chordService->calculateOptimalInversion($prevChord, $chord);
                        }
                    }
                    
                    $this->chords[$position] = [
                        'position' => $position,
                        'tone' => $chord['tone'],
                        'semitone' => $chord['semitone'],
                        'inversion' => $inversion,
                        'is_blue_note' => false,
                    ];
                }
            }
            
            $this->calculateBlueNotes();
            $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
            $this->showSuggestions = false;
        }
    }
    
    public function optimizeVoiceLeading()
    {
        // Optimize inversions for smooth voice leading
        for ($i = 2; $i <= 4; $i++) {
            if (!empty($this->chords[$i]['tone']) && !empty($this->chords[$i - 1]['tone'])) {
                $optimalInversion = $this->chordService->calculateOptimalInversion(
                    $this->chords[$i - 1],
                    $this->chords[$i]
                );
                $this->chords[$i]['inversion'] = $optimalInversion;
            }
        }
        
        $this->calculateBlueNotes();
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
        $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
    }

    private function calculateBlueNotes()
    {
        $this->blueNotes = $this->chordService->calculateBlueNotes($this->chords);
        
        foreach ($this->chords as $position => &$chord) {
            $chord['is_blue_note'] = isset($this->blueNotes[$position]);
        }
    }
    
    public function toggleRomanNumerals()
    {
        $this->showRomanNumerals = !$this->showRomanNumerals;
        if ($this->showRomanNumerals) {
            $this->updateRomanNumerals();
        }
    }
    
    private function updateRomanNumerals()
    {
        $this->romanNumerals = $this->chordService->analyzeProgression(
            $this->chords,
            $this->selectedKey,
            $this->selectedKeyType
        );
    }

    public function render()
    {
        $tones = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $availableKeys = $this->chordService->getAvailableKeys();
        
        return view('livewire.chord-grid', [
            'tones' => $tones,
            'availableKeys' => $availableKeys,
            'progressions' => $this->chordProgressions,
            'progressionDescriptions' => $this->progressionDescriptions,
        ]);
    }
}