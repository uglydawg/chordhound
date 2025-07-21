<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChordService;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class VoiceLeadingAnimation extends Component
{
    #[Reactive]
    public array $fromChord = [];

    #[Reactive]
    public array $toChord = [];

    public int $position = 0;

    public int $nextPosition = 0;

    public bool $showAnimation = false;

    private ChordService $chordService;

    public function boot()
    {
        $this->chordService = app(ChordService::class);
    }

    public function mount(array $fromChord = [], array $toChord = [], int $position = 0, int $nextPosition = 0)
    {
        $this->fromChord = $fromChord;
        $this->toChord = $toChord;
        $this->position = $position;
        $this->nextPosition = $nextPosition;
        $this->showAnimation = ! empty($fromChord['tone']) && ! empty($toChord['tone']);
    }

    private function getVoiceLeadingData(): array
    {
        if (empty($this->fromChord['tone']) || empty($this->toChord['tone'])) {
            return [];
        }

        // Get notes for both chords
        $fromNotes = $this->chordService->getChordNotes(
            $this->fromChord['tone'],
            $this->fromChord['semitone'] ?? 'major',
            $this->fromChord['inversion'] ?? 'root'
        );

        $toNotes = $this->chordService->getChordNotes(
            $this->toChord['tone'],
            $this->toChord['semitone'] ?? 'major',
            $this->toChord['inversion'] ?? 'root'
        );

        // Get octaves for comfortable hand positions
        $fromOctaves = $this->getComfortableOctaves($fromNotes, $this->fromChord['inversion'] ?? 'root');
        $toOctaves = $this->getComfortableOctaves($toNotes, $this->toChord['inversion'] ?? 'root');

        $movements = [];
        for ($i = 0; $i < min(count($fromNotes), count($toNotes)); $i++) {
            $fromPosition = $this->chordService->getPianoKeyPosition($fromNotes[$i], $fromOctaves[$i]);
            $toPosition = $this->chordService->getPianoKeyPosition($toNotes[$i], $toOctaves[$i]);

            $movements[] = [
                'from' => [
                    'note' => $fromNotes[$i],
                    'octave' => $fromOctaves[$i],
                    'position' => $fromPosition,
                    'x' => $this->getKeyXPosition($fromPosition),
                ],
                'to' => [
                    'note' => $toNotes[$i],
                    'octave' => $toOctaves[$i],
                    'position' => $toPosition,
                    'x' => $this->getKeyXPosition($toPosition),
                ],
                'voice' => $i + 1, // Bass, Middle, Treble
                'distance' => abs($toPosition - $fromPosition),
            ];
        }

        return $movements;
    }

    private function getComfortableOctaves(array $notes, string $inversion): array
    {
        switch ($inversion) {
            case 'root':
                return [4, 4, 4];
            case 'first':
                return [3, 4, 4];
            case 'second':
                return [3, 3, 4];
            default:
                return [4, 4, 4];
        }
    }

    private function getKeyXPosition(int $pianoPosition): float
    {
        // Convert piano position to X coordinate for mini keyboard
        // This is a simplified calculation for the mini keyboard
        $octave = floor(($pianoPosition + 9) / 12);
        $noteInOctave = ($pianoPosition + 9) % 12;

        $whiteKeyPositions = [0, 2, 4, 5, 7, 9, 11]; // C, D, E, F, G, A, B
        $blackKeyPositions = [1, 3, 6, 8, 10]; // C#, D#, F#, G#, A#

        $whiteKeyWidth = 20;
        $baseX = ($octave - 4) * 7 * $whiteKeyWidth; // Adjust for displayed octaves

        if (in_array($noteInOctave, $whiteKeyPositions)) {
            // White key
            $whiteIndex = array_search($noteInOctave, $whiteKeyPositions);

            return $baseX + ($whiteIndex * $whiteKeyWidth) + ($whiteKeyWidth / 2);
        } else {
            // Black key
            $blackOffsets = [0.75, 1.75, 3.75, 4.75, 5.75]; // Between white keys
            $blackIndex = array_search($noteInOctave, $blackKeyPositions);

            return $baseX + ($blackOffsets[$blackIndex] * $whiteKeyWidth);
        }
    }

    private function getAnimationVariation(): array
    {
        // Different animation styles based on transition type
        $variations = [
            'sequential' => [
                'delays' => [0, 0.3, 0.6],
                'duration' => '2s',
                'style' => 'sequential',
            ],
            'simultaneous' => [
                'delays' => [0, 0, 0],
                'duration' => '1.5s',
                'style' => 'simultaneous',
            ],
            'cascade' => [
                'delays' => [0, 0.15, 0.3],
                'duration' => '2.5s',
                'style' => 'cascade',
            ],
            'reverse' => [
                'delays' => [0.6, 0.3, 0],
                'duration' => '2s',
                'style' => 'reverse',
            ],
        ];

        // Choose variation based on transition characteristics
        if ($this->nextPosition == 1 && $this->position == 4) {
            // Loop back to start - use reverse cascade
            return $variations['reverse'];
        } elseif ($this->isStepwiseMotion()) {
            // Smooth stepwise motion - use simultaneous
            return $variations['simultaneous'];
        } elseif ($this->hasLargeLeaps()) {
            // Large intervals - use cascade to show complexity
            return $variations['cascade'];
        } else {
            // Default sequential
            return $variations['sequential'];
        }
    }

    private function isStepwiseMotion(): bool
    {
        $movements = $this->getVoiceLeadingData();
        foreach ($movements as $movement) {
            if ($movement['distance'] > 2) { // More than 2 semitones
                return false;
            }
        }

        return true;
    }

    private function hasLargeLeaps(): bool
    {
        $movements = $this->getVoiceLeadingData();
        foreach ($movements as $movement) {
            if ($movement['distance'] > 7) { // More than perfect fifth
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        $movements = $this->getVoiceLeadingData();
        $variation = $this->getAnimationVariation();

        return view('livewire.voice-leading-animation', [
            'movements' => $movements,
            'variation' => $variation,
        ]);
    }
}
