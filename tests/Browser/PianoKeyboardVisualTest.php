<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Facebook\WebDriver\WebDriverDimension;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoKeyboardVisualTest extends DuskTestCase
{
    public function test_piano_keyboard_visual_appearance(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                ->assertVisible('#piano-keyboard')
                // Verify the piano container is displayed correctly
                ->assertVisible('.piano-container')
                ->assertVisible('.piano-keys')
                // Verify white keys are rendered
                ->assertVisible('.white-keys-container')
                ->assertPresent('.white-key')
                // Verify black keys are rendered and positioned
                ->assertVisible('.black-keys-container')
                ->assertPresent('.black-key')
                // Take screenshot for visual comparison
                ->screenshot('piano-keyboard-full');

            // Count the correct number of keys
            $whiteKeyCount = $browser->elements('.white-key');
            $blackKeyCount = $browser->elements('.black-key');
            
            // C2-C4 range should have 21 white keys (7 per octave * 3 octaves)
            $this->assertCount(21, $whiteKeyCount);
            // C2-C4 range should have 15 black keys (5 per octave * 3 octaves)
            $this->assertCount(15, $blackKeyCount);
        });
    }

    public function test_piano_keys_have_correct_labels(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                // Verify C keys have octave markers
                ->assertSeeIn('#key-C2', 'C2')
                ->assertSeeIn('#key-C3', 'C3')
                ->assertSeeIn('#key-C4', 'C4')
                // Verify octave markers have blue styling
                ->assertPresent('.octave-marker.text-blue-600')
                // Click labels toggle to show all labels
                ->click('button[title="Show Labels"]')
                ->pause(500)
                // Verify labels are visible on keys
                ->assertSeeIn('#key-D3', 'D3')
                ->assertSeeIn('#key-G#3', 'G#3');
        });
    }

    public function test_piano_keys_visual_feedback_on_click(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                // Click on a white key
                ->click('#key-C3')
                ->pause(100)
                // Verify visual feedback (pressed state)
                ->assertHasClass('#key-C3', 'active')
                ->screenshot('piano-key-pressed-white')
                // Click on a black key
                ->click('#key-C#3')
                ->pause(100)
                ->assertHasClass('#key-C#3', 'active')
                ->screenshot('piano-key-pressed-black');
        });
    }

    public function test_piano_displays_active_chord_highlighting(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                // Select a chord (G major) - this should highlight G4, B4, D5
                ->click('[wire\\:id*="chord-grid"]')
                ->waitForText('Chord 1')
                ->select('[wire\\:id*="chord-grid"] select[name="chords.1.tone"]', 'G')
                ->pause(500)
                // Verify the chord notes are highlighted
                ->assertHasClass('#key-G4', 'pressed')
                ->assertHasClass('#key-B4', 'pressed')
                // Take screenshot of highlighted chord
                ->screenshot('piano-chord-highlighted');
        });
    }

    public function test_piano_keyboard_responsive_layout(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            // Test on desktop
            $browser->resize(1920, 1080)
                ->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                ->assertVisible('.piano-container')
                ->screenshot('piano-desktop');

            // Test on tablet
            $browser->resize(768, 1024)
                ->assertVisible('.piano-container')
                ->screenshot('piano-tablet');

            // Test on mobile (should still be visible but scrollable)
            $browser->resize(375, 667)
                ->assertVisible('.piano-container')
                ->assertPresent('.overflow-x-auto')
                ->screenshot('piano-mobile');
        });
    }

    public function test_piano_matches_reference_image_style(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chords')
                ->waitForText('Now Playing:')
                // Verify dark theme styling
                ->assertPresent('.bg-zinc-950')
                ->assertPresent('.bg-zinc-900')
                ->assertPresent('.bg-zinc-800')
                // Verify white key gradient styling is applied
                ->assertAttribute('.white-key', 'style', function ($style) {
                    return str_contains($style, 'border-radius: 0 0 4px 4px');
                })
                // Verify black key styling
                ->assertAttribute('.black-key', 'style', function ($style) {
                    return str_contains($style, 'border-radius: 0 0 3px 3px');
                })
                // Take final screenshot for visual comparison
                ->screenshot('piano-reference-comparison');
        });
    }
}