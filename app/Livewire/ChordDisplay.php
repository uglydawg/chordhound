<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChordService;
use Livewire\Attributes\On;
use Livewire\Component;

class ChordDisplay extends Component
{
    public array $chords = [];
    public array $blueNotes = [];
    public array $activeNotes = [];
    public int $startOctave = 3;
    public int $octaveCount = 3;
    
    private ChordService $chordService;

    public function mount()
    {
        // Initialize with empty chords
        $this->chords = [];
        $this->activeNotes = [];
    }

    public function boot()
    {
        $this->chordService = app(ChordService::class);
    }

    #[On('chordsUpdated')]
    public function updateChords($chords, $blueNotes)
    {
        $this->chords = $chords;
        $this->blueNotes = $blueNotes;
        $this->calculateActiveNotes();
    }

    private function calculateActiveNotes()
    {
        $this->activeNotes = [];
        
        foreach ($this->chords as $position => $chord) {
            if (!empty($chord['tone'])) {
                $notes = $this->chordService->getChordNotes(
                    $chord['tone'],
                    $chord['semitone'] ?? 'major',
                    $chord['inversion'] ?? 'root'
                );
                
                // Only take the first 3 notes for triads
                $notesToDisplay = array_slice($notes, 0, 3);
                
                foreach ($notesToDisplay as $index => $note) {
                    // Display chord in middle octave (4) for clarity
                    $octave = 4;
                    if ($index === 2 && $chord['inversion'] === 'first') {
                        // For first inversion, raise the last note an octave
                        $octave = 5;
                    } elseif ($index >= 1 && $chord['inversion'] === 'second') {
                        // For second inversion, raise the last two notes an octave
                        $octave = 5;
                    }
                    
                    $keyPosition = $this->chordService->getPianoKeyPosition($note, $octave);
                    if ($keyPosition >= 0 && $keyPosition <= 87) {
                        $this->activeNotes[] = [
                            'note' => $note,
                            'octave' => $octave,
                            'position' => $keyPosition,
                            'isBlueNote' => $chord['is_blue_note'] ?? false,
                            'chordPosition' => $position,
                        ];
                    }
                }
            }
        }
    }

    public function generatePianoKeys()
    {
        $keys = [];
        $whiteKeyWidth = 30;
        $blackKeyWidth = 20;
        $whiteKeyCount = 0;
        
        for ($octave = $this->startOctave; $octave < $this->startOctave + $this->octaveCount; $octave++) {
            foreach (['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'] as $note) {
                $keyPosition = $this->chordService->getPianoKeyPosition($note, $octave);
                $isBlack = $this->chordService->isBlackKey($note);
                
                $activeNote = collect($this->activeNotes)->firstWhere('position', $keyPosition);
                
                if (!$isBlack) {
                    $keys[] = [
                        'type' => 'white',
                        'note' => $note,
                        'octave' => $octave,
                        'position' => $keyPosition,
                        'x' => $whiteKeyCount * $whiteKeyWidth,
                        'width' => $whiteKeyWidth,
                        'isActive' => $activeNote !== null,
                        'isBlueNote' => $activeNote['isBlueNote'] ?? false,
                        'chordPosition' => $activeNote['chordPosition'] ?? null,
                    ];
                    $whiteKeyCount++;
                }
            }
        }
        
        // Add black keys on top
        $blackKeyOffsets = [
            'C#' => 0.65,
            'D#' => 1.35,
            'F#' => 3.65,
            'G#' => 4.35,
            'A#' => 5.35,
        ];
        
        $whiteKeyCount = 0;
        for ($octave = $this->startOctave; $octave < $this->startOctave + $this->octaveCount; $octave++) {
            foreach (['C', 'D', 'E', 'F', 'G', 'A', 'B'] as $whiteNote) {
                if ($whiteNote === 'C') {
                    $baseX = $whiteKeyCount * $whiteKeyWidth;
                    
                    foreach ($blackKeyOffsets as $blackNote => $offset) {
                        $fullNote = substr($blackNote, 0, -1); // Remove #
                        $keyPosition = $this->chordService->getPianoKeyPosition($blackNote, $octave);
                        $activeNote = collect($this->activeNotes)->firstWhere('position', $keyPosition);
                        
                        $keys[] = [
                            'type' => 'black',
                            'note' => $blackNote,
                            'octave' => $octave,
                            'position' => $keyPosition,
                            'x' => $baseX + ($offset * $whiteKeyWidth) - ($blackKeyWidth / 2),
                            'width' => $blackKeyWidth,
                            'isActive' => $activeNote !== null,
                            'isBlueNote' => $activeNote['isBlueNote'] ?? false,
                            'chordPosition' => $activeNote['chordPosition'] ?? null,
                        ];
                    }
                }
                
                if (in_array($whiteNote, ['C', 'D', 'E', 'F', 'G', 'A', 'B'])) {
                    $whiteKeyCount++;
                }
            }
        }
        
        return $keys;
    }

    public function render()
    {
        $pianoKeys = $this->generatePianoKeys();
        $totalWidth = $this->octaveCount * 7 * 30; // 7 white keys per octave * 30px width
        
        return view('livewire.chord-display', [
            'pianoKeys' => $pianoKeys,
            'totalWidth' => $totalWidth,
        ]);
    }
}