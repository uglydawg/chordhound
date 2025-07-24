<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

beforeEach(function () {
    // Clear session before each test
    session()->flush();
});

it('defaults showVoiceLeading to false', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
});

it('toggles voice leading visibility', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false);
});

it('persists voice leading preference in session', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false)
        ->call('toggleVoiceLeading')
        ->assertSessionHas('chord_grid.show_voice_leading', true)
        ->call('toggleVoiceLeading')
        ->assertSessionHas('chord_grid.show_voice_leading', false);
});

it('loads voice leading preference from session on mount', function () {
    // Set session value
    session(['chord_grid.show_voice_leading' => true]);
    
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', true);
    
    // Test with false value
    session(['chord_grid.show_voice_leading' => false]);
    
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
});

it('maintains voice leading preference when no session exists', function () {
    // Ensure no session value exists
    session()->forget('chord_grid.show_voice_leading');
    
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false);
});