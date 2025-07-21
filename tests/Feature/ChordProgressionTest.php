<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('applies chord progressions when selecting key and progression', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('selectedKey', 'G')
        ->assertSet('selectedProgression', 'I-V-vi-IV')
        ->assertSet('chords.1.tone', 'G')
        ->assertSet('chords.2.tone', 'D')
        ->assertSet('chords.3.tone', 'E')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'C');

    // Test changing key with existing progression
    Livewire::test(ChordGrid::class)
        ->call('setProgression', 'I-V-vi-IV')
        ->call('setKey', 'C')
        ->assertSet('chords.1.tone', 'C')
        ->assertSet('chords.2.tone', 'G')
        ->assertSet('chords.3.tone', 'A')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'F');

    // Test I-vi-IV-V progression
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'G')
        ->call('setProgression', 'I-vi-IV-V')
        ->assertSet('chords.1.tone', 'G')
        ->assertSet('chords.2.tone', 'E')
        ->assertSet('chords.2.semitone', 'minor')
        ->assertSet('chords.3.tone', 'C')
        ->assertSet('chords.4.tone', 'D');
});
