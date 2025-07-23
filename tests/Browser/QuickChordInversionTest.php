<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class QuickChordInversionTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test basic chord playback with timing
     */
    public function test_basic_chord_playback_timing(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(3000); // Wait for page and audio to load

            // Click first chord position
            $browser->click('[data-chord-position="1"]');
            
            // Set C major chord
            $browser->click("button[wire\\:click=\"setChord('C')\"]")
                ->pause(1000);

            // Click the chord to play it
            $browser->click('[data-chord-position="1"]')
                ->pause(100);

            // Verify C major keys are active (C4, E4, G4)
            $browser->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active')
                ->assertPresent('#key-G4.active');

            // Test timing - keys should still be active after 1.4 seconds
            $browser->pause(1400);
            $browser->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active')
                ->assertPresent('#key-G4.active');

            // After 1.6 seconds total, keys should be inactive
            $browser->pause(200);
            $browser->assertMissing('#key-C4.active')
                ->assertMissing('#key-E4.active')  
                ->assertMissing('#key-G4.active');
        });
    }

    /**
     * Test chord inversion changes
     */
    public function test_chord_inversion_changes(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(3000);

            // Set up C major chord
            $browser->click('[data-chord-position="1"]')
                ->click("button[wire\\:click=\"setChord('C')\"]")
                ->pause(1000);

            // Test root position (C4, E4, G4)
            $browser->click('[data-chord-position="1"]')
                ->pause(100);
            $browser->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active')
                ->assertPresent('#key-G4.active');
            $browser->pause(1500);

            // Change to first inversion (click "I" button)
            $browser->click('button:contains("I")')
                ->pause(1000);

            // Test first inversion (E4, G4, C5)
            $browser->click('[data-chord-position="1"]')
                ->pause(100);
            $browser->assertPresent('#key-E4.active')
                ->assertPresent('#key-G4.active')
                ->assertPresent('#key-C5.active');
            $browser->pause(1500);

            // Change to second inversion (click "II" button)
            $browser->click('button:contains("II")')
                ->pause(1000);

            // Test second inversion (G4, C5, E5)
            $browser->click('[data-chord-position="1"]')
                ->pause(100);
            $browser->assertPresent('#key-G4.active')
                ->assertPresent('#key-C5.active')
                ->assertPresent('#key-E5.active');
            $browser->pause(1500);
        });
    }

    /**
     * Test progression applies correct inversions
     */
    public function test_progression_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(3000);

            // Set I-IV-V progression in C major
            $browser->select('select[wire\\:change="setProgression($event.target.value)"]', 'I-IV-V')
                ->pause(1000);

            // Test chord 1 (C major, root position)
            $browser->click('[data-chord-position="1"]')
                ->pause(100);
            $browser->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active')
                ->assertPresent('#key-G4.active');
            $browser->pause(1500);

            // Test chord 2 (F major, second inversion - F/C)
            $browser->click('[data-chord-position="2"]')
                ->pause(100);
            $browser->assertPresent('#key-C5.active')
                ->assertPresent('#key-F4.active')
                ->assertPresent('#key-A4.active');
            $browser->pause(1500);

            // Test chord 3 (G major, first inversion - G/B)
            $browser->click('[data-chord-position="3"]')
                ->pause(100);
            $browser->assertPresent('#key-B4.active')
                ->assertPresent('#key-D5.active')
                ->assertPresent('#key-G4.active');
            $browser->pause(1500);
        });
    }
}