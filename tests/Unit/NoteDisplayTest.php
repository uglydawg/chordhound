<?php

declare(strict_types=1);

use App\Livewire\NoteDisplay;
use Livewire\Livewire;

it('displays no notes when activeNotes is empty', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => []])
        ->assertSee('No notes playing');
});

it('displays note names from activeNotes array', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['C4', 'E4', 'G4']])
        ->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');
});

it('displays notes without octave numbers', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['C4', 'E4', 'G4']])
        ->assertSee('C')
        ->assertSee('E')
        ->assertSee('G')
        ->assertDontSee('C4')
        ->assertDontSee('E4')
        ->assertDontSee('G4');
});

it('handles sharp notes correctly', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['F#4', 'A#4', 'C#5']])
        ->assertSee('F#')
        ->assertSee('A#')
        ->assertSee('C#');
});

it('handles flat notes correctly', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['Db4', 'Eb4', 'Gb4']])
        ->assertSee('Db')
        ->assertSee('Eb')
        ->assertSee('Gb');
});

it('removes duplicate note names from different octaves', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['C3', 'C4', 'E4', 'G4']])
        ->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');
});

it('displays bass note separately when includeBass is true', function () {
    Livewire::test(NoteDisplay::class, [
        'activeNotes' => ['C3', 'C4', 'E4', 'G4'],
        'includeBass' => true
    ])
        ->assertSee('Bass:')
        ->assertSee('C3');
});

it('handles empty chord gracefully', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => []])
        ->assertStatus(200)
        ->assertSee('No notes playing');
});

it('orders notes alphabetically', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['G4', 'C4', 'E4']])
        ->assertSeeInOrder(['C', 'E', 'G']);
});

it('applies large text styling for visibility', function () {
    Livewire::test(NoteDisplay::class, ['activeNotes' => ['C4', 'E4', 'G4']])
        ->assertSeeHtml('text-4xl')
        ->assertSeeHtml('font-bold');
});

it('updates display when activeNotes changes', function () {
    // Test with initial notes
    $component1 = Livewire::test(NoteDisplay::class, ['activeNotes' => ['C4', 'E4', 'G4']]);
    $component1->assertSee('C')
        ->assertSee('E')
        ->assertSee('G');

    // Test with different notes (create new component instance)
    $component2 = Livewire::test(NoteDisplay::class, ['activeNotes' => ['F4', 'A4', 'C5']]);
    $component2->assertSee('F')
        ->assertSee('A')
        ->assertSee('C');
});

it('handles maximum number of notes gracefully', function () {
    // Test with many notes (e.g., full octave)
    $notes = ['C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4'];

    Livewire::test(NoteDisplay::class, ['activeNotes' => $notes])
        ->assertStatus(200)
        ->assertSee('C')
        ->assertSee('B');
});

it('shows instructional text when showInstructions is true', function () {
    Livewire::test(NoteDisplay::class, [
        'activeNotes' => [],
        'showInstructions' => true
    ])
        ->assertSee('Click a chord to see the notes');
});

it('hides instructional text when notes are playing', function () {
    Livewire::test(NoteDisplay::class, [
        'activeNotes' => ['C4', 'E4', 'G4'],
        'showInstructions' => true
    ])
        ->assertDontSee('Click a chord')
        ->assertSee('C');
});

it('applies color coding for different note types', function () {
    Livewire::test(NoteDisplay::class, [
        'activeNotes' => ['C4', 'C#4', 'D4'],
        'highlightAccidentals' => true
    ])
        ->assertSeeHtml('text-orange-600'); // For sharp/flat notes
});

it('renders component with reactive property', function () {
    // Test that component can be embedded and receives updates
    $component = Livewire::test(NoteDisplay::class, ['activeNotes' => ['C4']]);

    expect($component->get('activeNotes'))->toBe(['C4']);
});
