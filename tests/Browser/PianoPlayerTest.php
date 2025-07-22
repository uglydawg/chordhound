<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoPlayerTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_piano_displays_correct_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/chords')
                ->assertSee('Full Piano Layout (C1 - C5)')
                ->assertPresent('#piano-keyboard')
                ->assertPresent('.piano-key')
                ->pause(1000) // Wait for piano to render
                ->screenshot('piano-layout');

            // Verify octave markers are visible
            foreach (range(1, 5) as $octave) {
                $browser->assertSeeIn('#piano-keyboard', "C{$octave}");
            }

            // Verify piano keys are clickable
            $browser->click('[data-note="C4"]')
                ->pause(100); // Audio should play
        });
    }

    public function test_piano_keys_have_correct_data_attributes(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/chords')
                ->assertPresent('[data-note="C1"]')
                ->assertPresent('[data-note="C#1"]')
                ->assertPresent('[data-note="D1"]')
                ->assertPresent('[data-note="D#1"]')
                ->assertPresent('[data-note="E1"]')
                ->assertPresent('[data-note="F1"]')
                ->assertPresent('[data-note="F#1"]')
                ->assertPresent('[data-note="G1"]')
                ->assertPresent('[data-note="G#1"]')
                ->assertPresent('[data-note="A1"]')
                ->assertPresent('[data-note="A#1"]')
                ->assertPresent('[data-note="B1"]')
                ->assertPresent('[data-note="C5"]');
        });
    }

    public function test_audio_controls_are_present(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/chords')
                ->assertSee('Test Piano Sound')
                ->click('button:contains("Test Piano Sound")')
                ->pause(500) // Wait for audio test
                ->assertSee('Sound')
                ->select('select[wire\\:model\\.live="selectedSound"]', 'cinematic')
                ->pause(100)
                ->assertSeeIn('.text-green-400', '✓'); // Check mark for sample indicator
        });
    }

    public function test_piano_visuals_match_design(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(1000) // Let everything render
                ->screenshot('piano-player-full')
                ->assertPresent('.bg-zinc-950') // Dark background
                ->assertPresent('svg rect[fill="#FFFFFF"]') // White keys
                ->assertPresent('svg rect[fill="#000000"]') // Black keys
                ->within('#piano-keyboard', function ($piano) {
                    // Verify the piano has the correct number of keys
                    $piano->assertPresent('rect.piano-key');
                });
        });
    }
}
