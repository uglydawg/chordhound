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
    
    private ChordService $chordService;
    
    // Common chord progressions for suggestions (based on web search results)
    private array $chordSuggestions = [
        'I-V-vi-IV (Pop)' => [
            ['tone' => 'C', 'semitone' => 'major'],
            ['tone' => 'G', 'semitone' => 'major'],
            ['tone' => 'A', 'semitone' => 'minor'],
            ['tone' => 'F', 'semitone' => 'major']
        ],
        'I-vi-IV-V (50s)' => [
            ['tone' => 'C', 'semitone' => 'major'],
            ['tone' => 'A', 'semitone' => 'minor'],
            ['tone' => 'F', 'semitone' => 'major'],
            ['tone' => 'G', 'semitone' => 'major']
        ],
        'ii-V-I (Jazz)' => [
            ['tone' => 'D', 'semitone' => 'minor'],
            ['tone' => 'G', 'semitone' => 'major'],
            ['tone' => 'C', 'semitone' => 'major']
        ],
        'I-IV-V (Blues)' => [
            ['tone' => 'C', 'semitone' => 'major'],
            ['tone' => 'F', 'semitone' => 'major'],
            ['tone' => 'G', 'semitone' => 'major']
        ],
        'vi-IV-I-V' => [
            ['tone' => 'A', 'semitone' => 'minor'],
            ['tone' => 'F', 'semitone' => 'major'],
            ['tone' => 'C', 'semitone' => 'major'],
            ['tone' => 'G', 'semitone' => 'major']
        ],
        'I-vi-ii-V' => [
            ['tone' => 'C', 'semitone' => 'major'],
            ['tone' => 'A', 'semitone' => 'minor'],
            ['tone' => 'D', 'semitone' => 'minor'],
            ['tone' => 'G', 'semitone' => 'major']
        ]
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
            // Default the first four chords to G, Em, C, D
            $this->chords[1] = [
                'position' => 1,
                'tone' => 'G',
                'semitone' => 'major',
                'inversion' => 'first',
                'is_blue_note' => false,
            ];
            $this->chords[2] = [
                'position' => 2,
                'tone' => 'E',
                'semitone' => 'minor',
                'inversion' => 'root',
                'is_blue_note' => false,
            ];
            $this->chords[3] = [
                'position' => 3,
                'tone' => 'C',
                'semitone' => 'major',
                'inversion' => 'root',
                'is_blue_note' => false,
            ];
            $this->chords[4] = [
                'position' => 4,
                'tone' => 'D',
                'semitone' => 'major',
                'inversion' => 'root',
                'is_blue_note' => false,
            ];
            
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
        $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
    }

    public function toggleSuggestions()
    {
        $this->showSuggestions = !$this->showSuggestions;
    }

    public function applySuggestion($progressionKey, $autoInversion = false)
    {
        if (isset($this->chordSuggestions[$progressionKey])) {
            $progression = $this->chordSuggestions[$progressionKey];
            
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
        $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
    }

    private function calculateBlueNotes()
    {
        $this->blueNotes = $this->chordService->calculateBlueNotes($this->chords);
        
        foreach ($this->chords as $position => &$chord) {
            $chord['is_blue_note'] = isset($this->blueNotes[$position]);
        }
    }

    public function render()
    {
        $tones = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        
        return view('livewire.chord-grid', [
            'tones' => $tones,
            'chordSuggestions' => $this->chordSuggestions,
        ]);
    }
}