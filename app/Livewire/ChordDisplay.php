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
                
                // Determine octaves for comfortable hand position
                $octaves = $this->getComfortableOctaves($notesToDisplay, $chord['inversion'] ?? 'root');
                
                foreach ($notesToDisplay as $index => $note) {
                    $octave = $octaves[$index];
                    
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
    
    private function getComfortableOctaves(array $notes, string $inversion): array
    {
        // For comfortable hand position, keep notes within about an octave span
        // Standard voicings for each inversion type
        
        switch ($inversion) {
            case 'root':
                // Root position: Close voicing in middle range
                return [4, 4, 4];
                
            case 'first':
                // First inversion: Bottom note stays low, top two notes close together
                return [3, 4, 4];
                
            case 'second':
                // Second inversion: Spread more evenly
                return [3, 3, 4];
                
            default:
                return [4, 4, 4];
        }
    }

    public function generatePianoKeys()
    {
        $keys = [];
        $whiteKeyWidth = 30;
        $blackKeyWidth = 18;  // Narrower black keys to show white key edges
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
        // Black keys are positioned relative to each octave's starting C
        $blackKeyOffsets = [
            'C#' => 0.75,  // Between C and D
            'D#' => 1.75,  // Between D and E
            'F#' => 3.75,  // Between F and G
            'G#' => 4.75,  // Between G and A
            'A#' => 5.75,  // Between A and B
        ];
        
        // Calculate black key positions for each octave
        for ($octave = $this->startOctave; $octave < $this->startOctave + $this->octaveCount; $octave++) {
            // Find the starting position of C in this octave
            $octaveOffset = ($octave - $this->startOctave) * 7; // 7 white keys per octave
            $baseX = $octaveOffset * $whiteKeyWidth;
            
            foreach ($blackKeyOffsets as $blackNote => $offset) {
                $keyPosition = $this->chordService->getPianoKeyPosition($blackNote, $octave);
                $activeNote = collect($this->activeNotes)->firstWhere('position', $keyPosition);
                
                $keys[] = [
                    'type' => 'black',
                    'note' => $blackNote,
                    'octave' => $octave,
                    'position' => $keyPosition,
                    'x' => $baseX + ($offset * $whiteKeyWidth),
                    'width' => $blackKeyWidth,
                    'isActive' => $activeNote !== null,
                    'isBlueNote' => $activeNote['isBlueNote'] ?? false,
                    'chordPosition' => $activeNote['chordPosition'] ?? null,
                ];
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