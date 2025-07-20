<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('toggles voice leading display', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', true) // Default is true
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true);
});

it('remembers voice leading toggle state in session', function () {
    // Toggle voice leading off
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false);
    
    // New instance should remember the toggle state
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
});

it('shows voice leading animations when enabled', function () {
    // Set up a chord progression
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V')
        ->assertSet('showVoiceLeading', true)
        ->assertSee('Voice Leading'); // Should see the voice leading toggle button
});

it('persists voice leading preference in new sessions', function () {
    // Set voice leading to false
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false);
    
    // Create new component - should restore from session
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
    
    // Toggle back on
    Livewire::test(ChordGrid::class)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true);
    
    // New component should remember it's on
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', true);
});