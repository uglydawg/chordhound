<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class NoteDisplay extends Component
{
    #[Reactive]
    public array $activeNotes = [];

    public bool $includeBass = false;

    public bool $showInstructions = false;

    public bool $highlightAccidentals = false;

    /**
     * Format note names for display (remove octave numbers)
     */
    public function formatNoteNames(): array
    {
        if (empty($this->activeNotes)) {
            return [];
        }

        // Extract note names (without octaves) and remove duplicates
        $noteNames = array_map(function ($note) {
            // Remove trailing digits (octave numbers)
            return preg_replace('/\d+$/', '', $note);
        }, $this->activeNotes);

        // Remove duplicates and re-index
        $uniqueNotes = array_unique($noteNames);

        // Sort alphabetically for consistent display
        sort($uniqueNotes);

        return array_values($uniqueNotes);
    }

    /**
     * Get bass note if includeBass is enabled
     */
    public function getBassNote(): ?string
    {
        if (!$this->includeBass || empty($this->activeNotes)) {
            return null;
        }

        // Bass note is typically the first note (lowest octave)
        $lowestNote = null;
        $lowestOctave = PHP_INT_MAX;

        foreach ($this->activeNotes as $note) {
            $octave = (int) preg_replace('/^[A-G]#?b?/', '', $note);

            if ($octave < $lowestOctave) {
                $lowestOctave = $octave;
                $lowestNote = $note;
            }
        }

        return $lowestNote;
    }

    /**
     * Check if a note is a sharp or flat (accidental)
     */
    public function isAccidental(string $note): bool
    {
        return str_contains($note, '#') || str_contains($note, 'b');
    }

    public function render()
    {
        $noteNames = $this->formatNoteNames();
        $bassNote = $this->getBassNote();

        return view('livewire.note-display', [
            'noteNames' => $noteNames,
            'bassNote' => $bassNote,
        ]);
    }
}
