<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('toggles voice leading display', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false) // Default is false
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false);
});

it('remembers voice leading toggle state in session', function () {
    // Toggle voice leading on
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true);

    // New instance should remember the toggle state
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', true);
});

it('shows voice leading animations when enabled', function () {
    // Set up a chord progression
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V')
        ->assertSet('showVoiceLeading', false)
        ->assertSee('Voice Leading'); // Should see the voice leading toggle button
});

it('persists voice leading preference in new sessions', function () {
    // Default is false, toggle to true
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true);

    // Create new component - should restore from session
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', true);

    // Toggle back off
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false);

    // New component should remember it's off
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
});
