<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BasicPianoIntegrationTest extends DuskTestCase
{
    /**
     * Test that the piano player and chord grid are integrated properly.
     */
    public function testPianoPlayerIntegrationWithChordGrid(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->assertSee('Piano Player')
                    ->pause(3000); // Give more time for JavaScript to load

            // Verify components are present
            $browser->assertPresent('.timeline-grid')
                    ->assertPresent('.piano-player')
                    ->assertPresent('#piano-keyboard');

            // Verify default chord progression is loaded (G, D, Em, C)
            $browser->assertSee('G')
                    ->assertSee('D')
                    ->assertSee('Em')
                    ->assertSee('C');

            // Verify transport controls are present
            $browser->assertVisible('button[title="Play"]')
                    ->assertVisible('button[title="Stop"]');

            // Verify BPM control
            $browser->assertVisible('input[type="number"][min="60"][max="200"]')
                    ->assertInputValue('input[type="number"][min="60"][max="200"]', '120');

            // Click play button
            $browser->click('button[title="Play"]')
                    ->pause(1000);

            // Verify UI changes to indicate playback
            $browser->assertVisible('button[title="Pause"]')
                    ->assertMissing('button[title="Play"]');

            // Take screenshot after giving time for first chord to play
            $browser->pause(2000)
                    ->screenshot('integration-test-playing');

            // Stop playback
            $browser->click('button[title="Stop"]')
                    ->pause(500);

            // Verify stopped state
            $browser->assertVisible('button[title="Play"]')
                    ->assertMissing('button[title="Pause"]');

            // Test clicking on a chord directly
            $browser->click('[data-chord-position="1"]')
                    ->pause(1000)
                    ->screenshot('chord-1-clicked');

            // The test passes if we get here without errors
            $this->assertTrue(true, 'Piano player and chord grid integration is working');
        });
    }
}