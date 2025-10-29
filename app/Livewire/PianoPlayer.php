<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChordService;
use Livewire\Attributes\On;
use Livewire\Component;

class PianoPlayer extends Component
{
    public int $tempo = 120;

    public bool $isPlaying = false;

    public float $currentTime = 0;

    public float $duration = 16; // 16 beats for 4 chords (4 beats each)

    public array $chords = [];

    public string $selectedSound = 'piano'; // Default piano sound

    public bool $showLabels = true;

    public array $currentChord = []; // Currently displayed chord

    public int $currentChordIndex = 0;

    public array $activeNotes = []; // Currently active/highlighted notes

    // Rhythm and timing options
    public string $selectedRhythm = 'block';
    public string $timeSignature = '4/4';
    
    // Piano display configuration
    public array $octaves = [2, 3, 4, 5]; // Default to C2, C3, C4 and C5
    public int $startOctave = 2;
    public int $endOctave = 5;

    // Available piano sounds
    public array $availableSounds = [
        'piano' => 'Piano',
    ];

    // Bass line patterns
    public string $selectedBassLine = 'root-fifth';

    public array $bassLinePatterns = [
        'root-only' => 'Root Only',
        'root-octave' => 'Root + Octave',
        'root-fifth' => 'Root-Fifth (Most Common)',
        'root-fifth-alt' => 'Root-Fifth Alternating',
        'walking' => 'Walking Bass',
        'none' => 'No Bass'
    ];

    // Piano rhythm patterns
    public array $rhythmPatterns = [
        'block' => 'Block Chords',
        'alberti' => 'Alberti Bass',
        'waltz' => 'Waltz Pattern',
        'broken' => 'Broken Chords',
        'arpeggio' => 'Arpeggiated',
        'march' => 'March',
        'ballad' => 'Ballad Style',
        'ragtime' => 'Ragtime'
    ];

    // Time signatures
    public array $timeSignatures = [
        '4/4' => '4/4 (Common)',
        '3/4' => '3/4 (Waltz)',
        '6/8' => '6/8 (Compound)',
        '2/4' => '2/4 (March)',
        '5/4' => '5/4 (Irregular)',
        '7/8' => '7/8 (Complex)'
    ];

    // BPM presets
    public array $bpmPresets = [
        60 => '60 (Slow)',
        80 => '80 (Relaxed)',
        100 => '100 (Moderate)',
        120 => '120 (Standard)',
        140 => '140 (Upbeat)',
        160 => '160 (Fast)',
        180 => '180 (Very Fast)'
    ];

    private ChordService $chordService;

    public function mount()
    {
        // Initialize with empty chords if not set
        if (empty($this->chords)) {
            $this->chords = [];
            $this->currentChord = [];
        }
        // Ensure no keys are pressed by default
        $this->activeNotes = [];
    }

    public function boot()
    {
        $this->chordService = app(ChordService::class);
    }

    #[On('chordsUpdated')]
    public function updateChords($data = [])
    {
        // Handle both array parameter and event object
        $chords = is_array($data) && isset($data['chords']) ? $data['chords'] : $data;

        \Log::info('PianoPlayer received chords update:', ['chords' => $chords]);
        
        $this->chords = $chords;
        // Don't automatically display any chord - wait for user interaction
        $this->currentChord = [];
        $this->activeNotes = [];
    }

    #[On('play-chord')]
    public function playChord($chord)
    {
        \Log::info('PianoPlayer received play-chord event:', ['chord' => $chord]);
        
        // Set the current chord to display on piano
        $this->currentChord = $chord;
        
        // The activeNotes will be calculated in the render method
        // This will cause the piano to highlight the correct keys
    }
    
    public function setCurrentChord(array $chord)
    {
        $this->currentChord = $chord;
    }

    public function togglePlayback()
    {
        $this->isPlaying = ! $this->isPlaying;
        $this->dispatch('toggle-playback', isPlaying: $this->isPlaying);
        
        // Reset time when stopping
        if (!$this->isPlaying) {
            $this->currentTime = 0;
        }
    }


    public function updateTempo($tempo = 120)
    {
        $this->tempo = (int) $tempo;
        $this->dispatch('tempo-changed', tempo: $this->tempo);
    }

    public function incrementTempo()
    {
        $this->tempo = min(200, $this->tempo + 5); // Max 200 BPM
        $this->dispatch('tempo-changed', tempo: $this->tempo);
    }

    public function decrementTempo()
    {
        $this->tempo = max(60, $this->tempo - 5); // Min 60 BPM
        $this->dispatch('tempo-changed', tempo: $this->tempo);
    }

    public function updateSound($sound = 'piano')
    {
        if (isset($this->availableSounds[$sound])) {
            $this->selectedSound = $sound;
            $this->dispatch('sound-changed', sound: $this->selectedSound);
        }
    }

    public function toggleLabels()
    {
        $this->showLabels = ! $this->showLabels;
    }

    public function updatedSelectedRhythm()
    {
        // Auto-adjust time signature for certain rhythms
        if ($this->selectedRhythm === 'waltz' && $this->timeSignature !== '3/4') {
            $this->timeSignature = '3/4';
            $this->dispatch('time-signature-changed', timeSignature: $this->timeSignature);
        }

        $this->dispatch('rhythm-changed', rhythm: $this->selectedRhythm);
    }

    public function updatedTimeSignature()
    {
        // Auto-adjust rhythm for certain time signatures
        if ($this->timeSignature === '3/4' && $this->selectedRhythm !== 'waltz') {
            $this->selectedRhythm = 'waltz';
            $this->dispatch('rhythm-changed', rhythm: $this->selectedRhythm);
        }

        $this->dispatch('time-signature-changed', timeSignature: $this->timeSignature);
    }

    public function updatedTempo()
    {
        $this->dispatch('tempo-changed', tempo: $this->tempo);
    }

    // Method to update current chord during playback
    public function updateCurrentChord($chordIndex = 0)
    {
        $this->currentChordIndex = $chordIndex;
        $chordValues = array_values($this->chords);
        if (isset($chordValues[$chordIndex])) {
            $this->currentChord = $chordValues[$chordIndex];
        }
    }

    public function render()
    {
        // Calculate active notes from current chord
        $this->activeNotes = [];

        if (! empty($this->currentChord['tone'])) {
            // Use the service method that returns notes with correct octaves
            $this->activeNotes = $this->chordService->getChordNotesForDisplay(
                $this->currentChord['tone'],
                $this->currentChord['semitone'] ?? 'major',
                $this->currentChord['inversion'] ?? 'root'
            );
        }

        // Dispatch event to update Canvas piano
        $this->dispatch('update-active-notes', notes: $this->activeNotes);

        return view('livewire.piano-player', [
            'activeNotes' => $this->activeNotes,
        ]);
    }
}
