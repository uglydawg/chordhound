<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChordSet;
use Illuminate\Http\Request;

class ChordController extends Controller
{
    public function index()
    {
        return view('chords.index');
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