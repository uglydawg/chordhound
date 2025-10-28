<?php

declare(strict_types=1);

use App\Livewire\ChordPiano;
use App\Livewire\PianoPlayer;
use Livewire\Livewire;

it('displays NoteDisplay component in ChordPiano', function () {
    Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root']
    ])
        ->assertSeeLivewire('note-display');
});

it('passes active notes to NoteDisplay in ChordPiano', function () {
    $component = Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root']
    ]);

    // Should show the note names
    $component->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');
});

it('displays NoteDisplay component in PianoPlayer', function () {
    Livewire::test(PianoPlayer::class)
        ->assertSeeLivewire('note-display');
});

it('shows notes in PianoPlayer when chord is set', function () {
    $component = Livewire::test(PianoPlayer::class);

    // Set a chord
    $component->call('setCurrentChord', [
        'tone' => 'G',
        'semitone' => 'major',
        'inversion' => 'root'
    ]);

    // Should display the chord notes
    $component->assertSee('G')
        ->assertSee('B')
        ->assertSee('D');
});

it('updates notes when chord changes in ChordPiano', function () {
    // Test with C major
    $componentC = Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root']
    ]);

    $componentC->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');

    // Test with F major (new component instance)
    $componentF = Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'F', 'semitone' => 'major', 'inversion' => 'root']
    ]);

    $componentF->assertSee('F')
        ->assertSee('A')
        ->assertSee('C'); // F major has C note
});

it('shows no notes when chord is empty in ChordPiano', function () {
    Livewire::test(ChordPiano::class, ['chord' => []])
        ->assertSee('Select a chord to display');
});

it('highlights accidentals in note display', function () {
    Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'F#', 'semitone' => 'major', 'inversion' => 'root']
    ])
        ->assertSeeHtml('text-orange-600'); // Sharp notes should be highlighted
});

it('displays correct notes for minor chords', function () {
    Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'A', 'semitone' => 'minor', 'inversion' => 'root']
    ])
        ->assertSee('A')
        ->assertSee('C')
        ->assertSee('E');
});

it('displays correct notes for chord inversions', function () {
    // First inversion should still show the same note names, just different octaves
    Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'first']
    ])
        ->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');
});

it('animates note appearance with Alpine.js', function () {
    Livewire::test(ChordPiano::class, [
        'chord' => ['tone' => 'C', 'semitone' => 'major', 'inversion' => 'root']
    ])
        ->assertSeeHtml('x-transition')
        ->assertSeeHtml('x-show');
});
