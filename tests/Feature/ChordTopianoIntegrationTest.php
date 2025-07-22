<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

it('displays chord progression page with both chord grid and piano player', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Chord grid elements
        ->assertSee('Chord Progression')
        ->assertSee('Chord Palette')
        ->assertSee('Beat 1-2') // First chord beat indicator
        // Piano player elements  
        ->assertSee('Piano Player')
        ->assertSee('Now Playing:')
        ->assertSee('Test Piano Sound')
        // Integrated functionality
        ->assertSee('C1') // Piano keys
        ->assertSee('Major') // Chord type selector
        ->assertSee('BPM'); // Piano controls
});

it('chord grid and piano player have proper livewire integration', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');
    
    // Check that both components are present with proper wire:id attributes
    $response->assertOk()
        ->assertSee('wire:id', false) // Livewire components have wire:id attributes
        ->assertSee('Livewire.dispatch'); // JavaScript Livewire functionality is present
});