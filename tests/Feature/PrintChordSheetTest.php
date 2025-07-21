<?php

declare(strict_types=1);

use App\Livewire\PrintChordSheet;
use Livewire\Livewire;

it('has all chords selected by default for printing', function () {
    Livewire::test(PrintChordSheet::class)
        ->assertSet('selectedChords', [1, 2, 3, 4]);
});

it('updates selected chords when event is dispatched', function () {
    Livewire::test(PrintChordSheet::class)
        ->dispatch('selected-chords-updated', selectedChords: [1, 3])
        ->assertSet('selectedChords', [1, 3]);
});

it('updates chords when chordsUpdated event is dispatched', function () {
    $chords = [
        1 => ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        2 => ['position' => 2, 'tone' => 'F', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        3 => ['position' => 3, 'tone' => 'G', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        4 => ['position' => 4, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
    ];

    Livewire::test(PrintChordSheet::class)
        ->dispatch('chordsUpdated', ['chords' => $chords])
        ->assertSet('chords', $chords);
});

it('shows print button', function () {
    Livewire::test(PrintChordSheet::class)
        ->assertSee('Print Chord Sheet');
});
