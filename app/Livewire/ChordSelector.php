<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\ChordSetChord;
use App\Services\ChordService;
use Livewire\Attributes\On;
use Livewire\Component;

class ChordSelector extends Component
{
    public array $chords = [];
    public array $blueNotes = [];
    public ?int $chordSetId = null;
    
    private ChordService $chordService;

    public function mount(?int $chordSetId = null)
    {
        $this->chordSetId = $chordSetId;
        
        // Initialize with 8 empty chord slots
        for ($i = 1; $i <= 8; $i++) {
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
                'inversion' => 'first', // G starts in first inversion
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
            
            // Calculate blue notes for default chords
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

    public function updateChord($position, $field, $value)
    {
        if (isset($this->chords[$position])) {
            $this->chords[$position][$field] = $value;
            $this->calculateBlueNotes();
            $this->dispatch('chordsUpdated', chords: $this->chords, blueNotes: $this->blueNotes);
        }
    }

    public function clearChord($position)
    {
        if (isset($this->chords[$position])) {
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
    }

    private function calculateBlueNotes()
    {
        $this->blueNotes = $this->chordService->calculateBlueNotes($this->chords);
        
        // Update blue note flags
        foreach ($this->chords as $position => &$chord) {
            $chord['is_blue_note'] = isset($this->blueNotes[$position]);
        }
    }

    #[On('save-chord-set')]
    public function saveChordSet($name, $description = null)
    {
        if (!auth()->check()) {
            $this->dispatch('notify', type: 'error', message: 'You must be logged in to save chord sets.');
            return;
        }

        $chordSet = $this->chordSetId 
            ? \App\Models\ChordSet::find($this->chordSetId)
            : new \App\Models\ChordSet();

        $chordSet->fill([
            'user_id' => auth()->id(),
            'name' => $name,
            'description' => $description,
        ]);

        $chordSet->save();

        // Delete existing chords if updating
        if ($this->chordSetId) {
            $chordSet->chords()->delete();
        }

        // Save new chords
        foreach ($this->chords as $chord) {
            if (!empty($chord['tone'])) {
                $chordSet->chords()->create([
                    'position' => $chord['position'],
                    'tone' => $chord['tone'],
                    'semitone' => $chord['semitone'],
                    'inversion' => $chord['inversion'],
                    'is_blue_note' => $chord['is_blue_note'],
                ]);
            }
        }

        $this->chordSetId = $chordSet->id;
        $this->dispatch('notify', type: 'success', message: 'Chord set saved successfully!');
        $this->dispatch('chord-set-saved', id: $chordSet->id);
    }

    public function render()
    {
        return view('livewire.chord-selector', [
            'tones' => ChordSetChord::TONES,
            'semitones' => ChordSetChord::SEMITONES,
            'inversions' => ChordSetChord::INVERSIONS,
        ]);
    }
}