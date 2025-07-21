<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChordSet;
use Illuminate\Http\Request;

class ChordController extends Controller
{
    public function index(Request $request)
    {
        $chordSetId = null;

        // Check if we need to load a specific chord set
        if ($request->has('load') && auth()->check()) {
            $chordSet = ChordSet::where('id', $request->load)
                ->where('user_id', auth()->id())
                ->first();

            if ($chordSet) {
                $chordSetId = $chordSet->id;
            }
        }

        return view('chords.index', compact('chordSetId'));
    }

    public function mySets()
    {
        return view('chords.my-sets');
    }

    public function edit(ChordSet $chordSet)
    {
        // Ensure user owns this chord set
        if ($chordSet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('chords.edit', compact('chordSet'));
    }
}
