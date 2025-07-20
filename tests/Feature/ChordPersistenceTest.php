<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('remembers selected key across page loads', function () {
    // Set a key and progression
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'D')
        ->call('setProgression', 'I-IV-V')
        ->assertSet('selectedKey', 'D')
        ->assertSet('selectedProgression', 'I-IV-V');
    
    // Create a new component instance - should restore from session
    Livewire::test(ChordGrid::class)
        ->assertSet('selectedKey', 'D')
        ->assertSet('selectedProgression', 'I-IV-V')
        ->assertSet('chords.1.tone', 'D')  // Should have D major progression
        ->assertSet('chords.2.tone', 'G')
        ->assertSet('chords.3.tone', 'A');
});

it('remembers key type selection', function () {
    // Set minor key
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'A')
        ->call('setKeyType', 'minor')
        ->call('setProgression', 'I-IV-V')
        ->assertSet('selectedKeyType', 'minor');
    
    // New instance should remember minor key type
    Livewire::test(ChordGrid::class)
        ->assertSet('selectedKey', 'A')
        ->assertSet('selectedKeyType', 'minor')
        ->assertSet('selectedProgression', 'I-IV-V');
});

it('uses defaults when no session data exists', function () {
    // Clear session
    session()->forget('chord_grid');
    
    // Should use defaults: G major with I-V-vi-IV
    Livewire::test(ChordGrid::class)
        ->assertSet('selectedKey', 'G')
        ->assertSet('selectedKeyType', 'major')
        ->assertSet('selectedProgression', 'I-V-vi-IV')
        ->assertSet('chords.1.tone', 'G')
        ->assertSet('chords.2.tone', 'D')
        ->assertSet('chords.3.tone', 'E')
        ->assertSet('chords.3.semitone', 'minor')
        ->assertSet('chords.4.tone', 'C');
});

it('does not override when editing a chord set', function () {
    // Set some preferences
    Livewire::test(ChordGrid::class)
        ->call('setKey', 'F')
        ->call('setProgression', 'I-vi-IV-V');
    
    // Create a chord set manually without factory
    $user = \App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $chordSet = \App\Models\ChordSet::create([
        'user_id' => $user->id,
        'name' => 'Test Chord Set',
    ]);
    
    $chordSet->chords()->create([
        'position' => 1,
        'tone' => 'C',
        'semitone' => 'major',
        'inversion' => 'root',
        'is_blue_note' => false,
    ]);
    
    Livewire::test(ChordGrid::class, ['chordSetId' => $chordSet->id])
        ->assertSet('chords.1.tone', 'C'); // Should load from chord set, not session
});

it('remembers roman numeral toggle state', function () {
    // Enable roman numerals
    Livewire::test(ChordGrid::class)
        ->call('toggleRomanNumerals')
        ->assertSet('showRomanNumerals', true);
    
    // New instance should remember the toggle state
    Livewire::test(ChordGrid::class)
        ->assertSet('showRomanNumerals', true);
    
    // Disable and test again
    Livewire::test(ChordGrid::class)
        ->call('toggleRomanNumerals')
        ->assertSet('showRomanNumerals', false);
    
    // Should remember disabled state
    Livewire::test(ChordGrid::class)
        ->assertSet('showRomanNumerals', false);
});