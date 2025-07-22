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

it('displays piano with correct layout from C1 to C5', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('Now Playing:')
        ->assertSee('C1')
        ->assertSee('C2')
        ->assertSee('C3')
        ->assertSee('C4')
        ->assertSee('C5');
});

it('shows all white keys with proper labels', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk();

    // Check for white keys in each octave
    $whiteKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
    foreach (range(1, 5) as $octave) {
        foreach ($whiteKeys as $key) {
            if (! ($octave == 5 && $key != 'C')) { // C5 is the last key
                $response->assertSee($key.$octave);
            }
        }
    }
});

it('displays piano keyboard with HTML button elements', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('full-piano-container')
        ->assertSee('piano-keyboard')
        ->assertSee('piano-key')
        ->assertSee('full-piano-white')
        ->assertSee('full-piano-black')
        ->assertSee('data-note=');
});

it('shows test audio button and audio sample indicator', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('Test Piano Sound');
});

it('includes piano sound selector', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('Piano')
        ->assertSee('Sound');
});

it('shows transport controls for playback', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('BPM')
        ->assertSee('Labels')
        ->assertSee('Sound');
});

it('displays piano keys with correct styling for black and white keys', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // White keys with gradient styling
        ->assertSee('background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%)', false)
        // Black keys with gradient styling
        ->assertSee('background: linear-gradient(180deg, #1a1a1a 0%, #000000 100%)', false)
        // Key press animation
        ->assertSee('@keyframes pianoKeyPress', false);
});
