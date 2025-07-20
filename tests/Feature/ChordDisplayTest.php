<?php

declare(strict_types=1);

use App\Livewire\ChordDisplay;
use Livewire\Livewire;

it('shows all four chords separately', function () {
    $chords = [
        1 => ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        2 => ['position' => 2, 'tone' => 'F', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        3 => ['position' => 3, 'tone' => 'G', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        4 => ['position' => 4, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
    ];
    
    Livewire::test(ChordDisplay::class)
        ->call('updateChords', $chords, [])
        ->assertSee('Chords') // Should say "Chords" not "Piano Keyboard"
        ->assertSee('Chord 1')
        ->assertSee('Chord 2')
        ->assertSee('Chord 3')
        ->assertSee('Chord 4')
        ->assertSee('C')
        ->assertSee('F')
        ->assertSee('G')
        ->assertSee('Notes: C - E - G'); // C major notes
});

it('has all chords selected by default', function () {
    Livewire::test(ChordDisplay::class)
        ->assertSet('selectedChords', [1, 2, 3, 4]);
});

it('can toggle chord selection', function () {
    Livewire::test(ChordDisplay::class)
        ->call('toggleChordSelection', 2)
        ->assertSet('selectedChords', [1, 3, 4])
        ->call('toggleChordSelection', 2)
        ->assertSet('selectedChords', [1, 2, 3, 4]);
});

it('can select all chords', function () {
    Livewire::test(ChordDisplay::class)
        ->set('selectedChords', [1, 3])
        ->call('selectAllChords')
        ->assertSet('selectedChords', [1, 2, 3, 4]);
});

it('can deselect all chords', function () {
    Livewire::test(ChordDisplay::class)
        ->call('deselectAllChords')
        ->assertSet('selectedChords', []);
});

it('dispatches selected chords updated event', function () {
    Livewire::test(ChordDisplay::class)
        ->call('toggleChordSelection', 3)
        ->assertDispatched('selected-chords-updated', selectedChords: [1, 2, 4]);
});

it('shows empty state for chords without tones', function () {
    $chords = [
        1 => ['position' => 1, 'tone' => '', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        2 => ['position' => 2, 'tone' => 'F', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        3 => ['position' => 3, 'tone' => '', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
        4 => ['position' => 4, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
    ];
    
    Livewire::test(ChordDisplay::class)
        ->call('updateChords', $chords, [])
        ->assertSee('Empty', false)
        ->assertSee('No chord selected');
});