<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VoiceLeadingInversionsTest extends DuskTestCase
{
    /**
     * Test that voice-leading inversions are displayed correctly
     */
    public function test_voice_leading_inversions_display(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Select C major key
            $browser->select('select[wire\\:change*="setKey"]', 'C')
                ->pause(100);

            // Select I-IV-V progression
            $browser->select('select[wire\\:change*="setProgression"]', 'I-IV-V')
                ->pause(500);

            // Verify the chords are set correctly
            $browser->assertSeeIn('[data-position="1"] .chord-display', 'C')
                ->assertSeeIn('[data-position="2"] .chord-display', 'F')
                ->assertSeeIn('[data-position="3"] .chord-display', 'G');

            // Check inversions when voice leading is enabled
            $browser->assertSeeIn('[data-position="1"] .inversion-display', 'Root')
                ->assertSeeIn('[data-position="2"] .inversion-display', '2nd')
                ->assertSeeIn('[data-position="3"] .inversion-display', '1st');

            echo "✅ Voice-leading inversions confirmed for I-IV-V!\n";
        });
    }

    /**
     * Test toggling voice leading changes inversions
     */
    public function test_toggle_voice_leading_changes_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Set up progression
            $browser->select('select[wire\\:change*="setKey"]', 'G')
                ->pause(100)
                ->select('select[wire\\:change*="setProgression"]', 'I-vi-IV-V')
                ->pause(500);

            // Toggle voice leading off (if it's on by default)
            $voiceLeadingButton = $browser->element('button[wire\\:click="toggleVoiceLeading"]');
            if ($voiceLeadingButton) {
                $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                    ->pause(500);
                
                // All should be root position now
                $browser->assertSeeIn('[data-position="1"] .inversion-display', 'Root')
                    ->assertSeeIn('[data-position="2"] .inversion-display', 'Root')
                    ->assertSeeIn('[data-position="3"] .inversion-display', 'Root')
                    ->assertSeeIn('[data-position="4"] .inversion-display', 'Root');

                // Toggle back on
                $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                    ->pause(500);

                // Should have optimal inversions
                $browser->assertSeeIn('[data-position="1"] .inversion-display', 'Root')
                    ->assertSeeIn('[data-position="2"] .inversion-display', '1st')
                    ->assertSeeIn('[data-position="3"] .inversion-display', '2nd')
                    ->assertSeeIn('[data-position="4"] .inversion-display', '1st');

                echo "✅ Voice-leading toggle confirmed working!\n";
            }
        });
    }

    /**
     * Test playing chords with voice-leading inversions
     */
    public function test_playing_with_voice_leading_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5)
                ->waitFor('.piano-player', 5);

            // Set up jazz ii-V-I progression
            $browser->select('select[wire\\:change*="setKey"]', 'F')
                ->pause(100)
                ->select('select[wire\\:change*="setProgression"]', 'ii-V-I')
                ->pause(500);

            // Verify the inversions
            $browser->assertSeeIn('[data-position="1"] .chord-display', 'Gm')
                ->assertSeeIn('[data-position="1"] .inversion-display', '2nd')
                ->assertSeeIn('[data-position="2"] .chord-display', 'C')
                ->assertSeeIn('[data-position="2"] .inversion-display', '1st')
                ->assertSeeIn('[data-position="3"] .chord-display', 'F')
                ->assertSeeIn('[data-position="3"] .inversion-display', 'Root');

            // Click play button
            $browser->click('.transport-button')
                ->pause(1000);

            // The piano should be playing with proper inversions
            // (actual audio testing would require checking console logs)

            $browser->click('.transport-button'); // Stop

            echo "✅ ii-V-I progression with voice-leading inversions ready for playback!\n";
        });
    }
}