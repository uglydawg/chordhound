<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\ChordSet;
use Livewire\Component;
use Livewire\WithPagination;

class ChordSetList extends Component
{
    use WithPagination;

    public function deleteChordSet($id)
    {
        $chordSet = ChordSet::where('user_id', auth()->id())->find($id);

        if ($chordSet) {
            $chordSet->delete();
            $this->dispatch('notify', type: 'success', message: 'Chord set deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.chord-set-list', [
            'chordSets' => ChordSet::where('user_id', auth()->id())
                ->latest()
                ->paginate(10),
        ]);
    }
}
