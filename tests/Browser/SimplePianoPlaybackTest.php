<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimplePianoPlaybackTest extends DuskTestCase
{
    /**
     * Test that piano player plays chords when play button is pressed.
     */
    public function testPianoPlayerPlaysChords(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->assertSee('Piano Player')
                    ->pause(2000); // Give time for components to fully load

            // Verify initial state
            $browser->assertSee('Now Playing:')
                    ->assertSee('No chord selected');

            // Verify play button is visible
            $browser->assertVisible('button[title="Play"]');

            // Click the play button
            $browser->click('button[title="Play"]')
                    ->pause(1000);

            // Verify playback started
            $browser->assertVisible('button[title="Pause"]')
                    ->assertMissing('button[title="Play"]');

            // Take a screenshot during playback
            $browser->screenshot('piano-playing');

            // Wait a bit and verify piano keys are being highlighted
            $browser->pause(2000)
                    ->assertPresent('.piano-key.active');

            // Verify the "Now Playing:" shows a chord (not "No chord selected")
            $browser->assertDontSee('No chord selected');

            // Check that the progress bar is visible and has some width
            $progressWidth = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');
            $this->assertNotEquals('0px', $progressWidth, 'Progress bar should show progress');

            // Click pause
            $browser->click('button[title="Pause"]')
                    ->pause(500);

            // Verify paused state
            $browser->assertVisible('button[title="Play"]')
                    ->assertMissing('button[title="Pause"]');

            // Click stop
            $browser->click('button[title="Stop"]')
                    ->pause(500);

            // Verify stopped state - progress should be reset
            $resetProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');
            $this->assertEquals('0px', $resetProgress, 'Progress bar should be reset');
        });
    }

    /**
     * Test clicking individual chords in the grid.
     */
    public function testClickingChordsPlaysThemIndividually(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(2000);

            // Click on the first chord position in the timeline grid
            $browser->click('[data-chord-position="1"]')
                    ->pause(1000);

            // Verify piano shows active keys
            $browser->assertPresent('.piano-key.active');

            // The "Now Playing:" should show the chord
            $browser->assertDontSee('No chord selected');

            // Take a screenshot
            $browser->screenshot('chord-clicked');
        });
    }

    /**
     * Test tempo control.
     */
    public function testTempoControl(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(2000);

            // Find the BPM input and verify default is 120
            $browser->assertInputValue('input[type="number"][min="60"][max="200"]', '120');

            // Change tempo to 180
            $browser->clear('input[type="number"][min="60"][max="200"]')
                    ->type('input[type="number"][min="60"][max="200"]', '180')
                    ->keys('input[type="number"][min="60"][max="200"]', '{tab}')
                    ->pause(500);

            // Verify the value changed
            $browser->assertInputValue('input[type="number"][min="60"][max="200"]', '180');
        });
    }
}