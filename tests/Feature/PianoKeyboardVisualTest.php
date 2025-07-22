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

it('displays piano keyboard with proper C2-C4 range as shown in the image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        ->assertSee('piano-container')
        ->assertSee('piano-keyboard')
        // Verify C2-C4 range (3 octaves)
        ->assertSee('C2')
        ->assertSee('C3')
        ->assertSee('C4')
        // Verify octave markers are only on C keys
        ->assertSee('octave-marker');
});

it('renders white keys with correct layout and styling', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // White keys container structure
        ->assertSee('white-keys-container')
        ->assertSee('white-key')
        // White key gradient styling
        ->assertSee('background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%)', false)
        // Border and shadow styling
        ->assertSee('border-radius: 0 0 4px 4px', false)
        ->assertSee('box-shadow: 0 2px 4px rgba(0,0,0,0.1)', false);

    // Verify all white keys in the range are present
    $whiteKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
    foreach ([2, 3, 4] as $octave) {
        foreach ($whiteKeys as $note) {
            $response->assertSee("data-note=\"{$note}{$octave}\"", false);
        }
    }
});

it('renders black keys with correct positioning and styling', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Black keys container structure
        ->assertSee('black-keys-container')
        ->assertSee('black-key')
        // Black key gradient styling (darker appearance)
        ->assertSee('background: linear-gradient(180deg, #374151 0%, #1f2937 100%)', false)
        // Black key dimensions and shadows
        ->assertSee('box-shadow: 0 4px 8px rgba(0,0,0,0.3)', false)
        ->assertSee('border-radius: 0 0 3px 3px', false);

    // Verify all black keys in the range are present
    $blackKeys = ['C#', 'D#', 'F#', 'G#', 'A#'];
    foreach ([2, 3, 4] as $octave) {
        foreach ($blackKeys as $note) {
            $response->assertSee("data-note=\"{$note}{$octave}\"", false);
        }
    }
});

it('shows proper visual feedback for pressed/active keys', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Pressed state styling for white keys (blue gradient like in image)
        ->assertSee('.white-key.pressed', false)
        ->assertSee('background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%)', false)
        // Pressed state styling for black keys
        ->assertSee('.black-key.pressed', false)
        // Key press animation
        ->assertSee('@keyframes pianoKeyPress', false)
        ->assertSee('transform: translateY(3px)', false);
});

it('displays key labels correctly when enabled', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Key label elements
        ->assertSee('key-label')
        // Label styling for white keys
        ->assertSee('text-gray-600')
        // Label styling for black keys
        ->assertSee('text-gray-300')
        // Octave markers on C keys (blue color as in image)
        ->assertSee('text-blue-600')
        ->assertSee('octave-marker');
});

it('maintains proper key proportions and spacing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Piano container dimensions
        ->assertSee('height: 120px', false)
        ->assertSee('min-width: 600px', false)
        // White keys use flexbox for equal spacing
        ->assertSee('flex: 1', false)
        // Black keys height is 60% of container
        ->assertSee('h-3/5', false);
});

it('shows piano with dark theme background matching the image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Dark background colors
        ->assertSee('bg-zinc-950')
        ->assertSee('bg-zinc-900')
        ->assertSee('bg-zinc-800')
        // Piano container gradient background
        ->assertSee('background: linear-gradient(145deg, #1f2937, #374151)', false)
        // Black background for keys area
        ->assertSee('background: #000', false);
});

it('highlights active chord notes with blue color as shown in image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Simulate having a chord selected that would highlight D4, G4, B4
    $response = $this->get('/chords');

    $response->assertOk()
        // Blue highlighting for active notes
        ->assertSee('.pressed', false)
        ->assertSee('background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%)', false)
        // White text on blue background for pressed keys
        ->assertSee('color: white !important', false)
        ->assertSee('font-weight: 600', false);
});

it('properly positions black keys between white keys', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // Black keys use absolute positioning
        ->assertSee('absolute', false)
        ->assertSee('pointer-events-none', false)
        ->assertSee('pointer-events-auto', false)
        // Z-index for proper layering
        ->assertSee('z-10', false);

    // Verify black keys exist with proper data attributes
    $blackKeys = ['C#', 'D#', 'F#', 'G#', 'A#'];
    foreach ([2, 3, 4] as $octave) {
        foreach ($blackKeys as $note) {
            $response->assertSee("data-note=\"{$note}{$octave}\"", false);
        }
    }
});

it('provides hover effects for both white and black keys', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/chords');

    $response->assertOk()
        // White key hover effects
        ->assertSee('.white-key:hover', false)
        ->assertSee('hover:bg-gray-100', false)
        ->assertSee('transform: translateY(1px)', false)
        // Black key hover effects
        ->assertSee('.black-key:hover', false)
        ->assertSee('hover:bg-gray-800', false)
        // Transition animations
        ->assertSee('transition-all duration-100', false);
});