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

    public float $duration = 8; // 8 beats for 4 chords (2 beats each)

    public array $chords = [];

    public string $selectedSound = 'piano'; // Default piano sound

    public bool $showLabels = true;

    public array $currentChord = []; // Currently displayed chord

    public int $currentChordIndex = 0;

    public array $activeNotes = []; // Currently active/highlighted notes

    // Available piano sounds
    public array $availableSounds = [
        'piano' => 'Piano',
    ];

    private ChordService $chordService;

    public function mount()
    {
        // Initialize with empty chords if not set
        if (empty($this->chords)) {
            $this->chords = [];
            $this->currentChord = [];
        }
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

        $this->chords = $chords;
        // Display first chord by default
        if (! empty($chords)) {
            $this->currentChord = array_values($chords)[0] ?? [];
        } else {
            $this->currentChord = [];
        }
    }
    
    public function setCurrentChord(array $chord)
    {
        $this->currentChord = $chord;
    }

    public function togglePlayback()
    {
        $this->isPlaying = ! $this->isPlaying;
        $this->dispatch('toggle-playback', isPlaying: $this->isPlaying);
    }

    public function stop()
    {
        $this->isPlaying = false;
        $this->currentTime = 0;
        $this->currentChordIndex = 0;
        // Reset to first chord
        if (! empty($this->chords)) {
            $this->currentChord = array_values($this->chords)[0] ?? [];
        }
        $this->dispatch('stop-playback');
    }

    public function updateTempo($tempo = 120)
    {
        $this->tempo = (int) $tempo;
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

    // Method to update current chord during playback
    public function updateCurrentChord($chordIndex = 0)
    {
        $this->currentChordIndex = $chordIndex;
        $chordValues = array_values($this->chords);
        if (isset($chordValues[$chordIndex])) {
            $this->currentChord = $chordValues[$chordIndex];
        }
    }

    private function getComfortableOctaves(array $notes, string $inversion): array
    {
        switch ($inversion) {
            case 'root':
                return [3, 3, 3];
            case 'first':
                return [2, 3, 3];
            case 'second':
                return [2, 2, 3];
            default:
                return [3, 3, 3];
        }
    }

    public function render()
    {
        // Calculate active notes from current chord
        $this->activeNotes = [];

        if (! empty($this->currentChord['tone'])) {
            $notes = $this->chordService->getChordNotes(
                $this->currentChord['tone'],
                $this->currentChord['semitone'] ?? 'major',
                $this->currentChord['inversion'] ?? 'root'
            );

            $notesToDisplay = array_slice($notes, 0, 3);
            $octaves = $this->getComfortableOctaves($notesToDisplay, $this->currentChord['inversion'] ?? 'root');

            foreach ($notesToDisplay as $index => $note) {
                $this->activeNotes[] = $note.$octaves[$index];
            }
        }

        return view('livewire.piano-player', [
            'activeNotes' => $this->activeNotes,
        ]);
    }
}
