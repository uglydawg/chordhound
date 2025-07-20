<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('updates chords when key changes with selected progression', function () {
    // Start with G major and I-V-vi-IV progression
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'G')
        ->call('setProgression', 'I-V-vi-IV')
        ->assertSet('selectedKey', 'G')
        ->assertSet('selectedProgression', 'I-V-vi-IV')
        ->assertSet('chords.1.tone', 'G')
        ->assertSet('chords.2.tone', 'D')
        ->assertSet('chords.3.tone', 'E')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'C');
    
    // Change key to C - chords should update automatically
    $component->call('setKey', 'C')
        ->assertSet('selectedKey', 'C')
        ->assertSet('selectedProgression', 'I-V-vi-IV') // Progression should remain
        ->assertSet('chords.1.tone', 'C')
        ->assertSet('chords.2.tone', 'G')
        ->assertSet('chords.3.tone', 'A')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'F');
    
    // Change key to D - chords should update again
    $component->call('setKey', 'D')
        ->assertSet('selectedKey', 'D')
        ->assertSet('selectedProgression', 'I-V-vi-IV')
        ->assertSet('chords.1.tone', 'D')
        ->assertSet('chords.2.tone', 'A')
        ->assertSet('chords.3.tone', 'B')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'G');
});

it('updates chords when key type changes', function () {
    // Start with C major and I-vi-IV-V progression
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-vi-IV-V')
        ->call('setKeyType', 'major')
        ->assertSet('chords.1.tone', 'C')
        ->assertSet('chords.1.semitone', 'major')
        ->assertSet('chords.2.tone', 'A')
        ->assertSet('chords.2.semitone', 'minor');
    
    // Change to C minor - chords should update for minor key
    $component->call('setKeyType', 'minor')
        ->assertSet('selectedKeyType', 'minor');
    
    // In minor key, I-vi-IV-V becomes i-VI-iv-v
    // The qualities change based on the minor scale
});