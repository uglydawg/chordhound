<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

it('dispatches play-chord event when a chord is clicked', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('chord-grid')
        ->call('setChord', 'C', 'major')
        ->call('selectChord', 1)
        ->assertDispatched('play-chord', function ($event, $data) {
            return $data['chord']['tone'] === 'C' 
                && $data['chord']['semitone'] === 'major'
                && $data['chord']['position'] === 1;
        });
});

it('plays chord when manually called through playChord method', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('chord-grid')
        ->call('setChord', 'G', 'minor')
        ->call('playChord', 1)
        ->assertDispatched('play-chord', function ($event, $data) {
            return $data['chord']['tone'] === 'G' 
                && $data['chord']['semitone'] === 'minor';
        });
});

it('does not play empty chords', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test('chord-grid');
    
    // Clear the progression to ensure we have empty chords
    $component->call('setProgression', '');
    
    // Clear all chords to start fresh
    $component->call('clearChord', 1);
    $component->call('clearChord', 2);
    $component->call('clearChord', 3);
    $component->call('clearChord', 4);
    
    // Check that the chord at position 1 is indeed empty
    expect($component->get('chords')[1]['tone'])->toBe('');
    
    $component->call('selectChord', 1)
        ->assertNotDispatched('play-chord');
});

it('piano player can update current chord from setCurrentChord method', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $chord = [
        'position' => 1,
        'tone' => 'F',
        'semitone' => 'major',
        'inversion' => 'root',
        'is_blue_note' => false,
    ];

    Livewire::test('piano-player')
        ->call('setCurrentChord', $chord)
        ->assertSet('currentChord', $chord);
});