<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordPlaybackIntegrationTest extends DuskTestCase
{
    /**
     * Test that piano player plays chords when play button is pressed.
     */
    public function testPianoPlayerPlaysChordProgressionOnPlayButton(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->assertSee('Chord Progression')
                    ->assertSee('Piano Player');

            // Wait for components to load
            $browser->waitFor('[wire\\:id]', 10)
                    ->pause(1000); // Give time for Livewire to initialize

            // Verify initial state - play button should be visible
            $browser->assertVisible('button[title="Play"]')
                    ->assertMissing('button[title="Pause"]');

            // Check that default chord progression is loaded
            $browser->within('.timeline-grid', function ($browser) {
                // Default progression should have chords
                $browser->assertVisible('[data-chord-position]');
            });

            // Verify piano player is in stopped state
            $browser->within('.piano-player', function ($browser) {
                $browser->assertSeeIn('.text-zinc-400', 'Now Playing:')
                        ->assertVisible('#piano-keyboard');
            });

            // Click the play button
            $browser->click('button[title="Play"]')
                    ->pause(500); // Wait for playback to start

            // Verify play button changed to pause button
            $browser->assertMissing('button[title="Play"]')
                    ->assertVisible('button[title="Pause"]');

            // Take a screenshot to debug
            $browser->screenshot('piano-playback-test');

            // Verify chord highlighting during playback - check for the orange border class
            $browser->waitFor('.border-orange-500', 10)
                    ->assertPresent('.border-orange-500.animate-pulse');

            // Verify piano keys are being highlighted
            $browser->within('.piano-player', function ($browser) {
                // Check that some piano keys are active/pressed
                $browser->waitFor('.piano-key.active', 5)
                        ->assertPresent('.piano-key.active');
            });

            // Verify the progress bar is moving
            $initialProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');
            $browser->pause(2000); // Wait for progress
            $updatedProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');
            
            // Progress should have changed
            $this->assertNotEquals($initialProgress, $updatedProgress, 'Progress bar should be moving during playback');

            // Test pause functionality
            $browser->click('button[title="Pause"]')
                    ->pause(500);

            // Verify playback paused
            $browser->assertVisible('button[title="Play"]')
                    ->assertMissing('button[title="Pause"]');

            // Test stop functionality
            $browser->click('button[title="Play"]')
                    ->pause(1000)
                    ->click('button[title="Stop"]')
                    ->pause(500);

            // Verify playback stopped and reset
            $browser->assertVisible('button[title="Play"]')
                    ->assertMissing('.border-orange-500')
                    ->assertMissing('.piano-key.active');

            // Verify progress bar reset to 0
            $resetProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');
            $this->assertEquals('0px', $resetProgress, 'Progress bar should reset to 0 when stopped');
        });
    }

    /**
     * Test that clicking individual chords plays them.
     */
    public function testClickingChordsPlaysThemIndividually(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(1000);

            // Click on the first chord slot
            $browser->within('.timeline-grid', function ($browser) {
                $browser->click('[data-chord-position="1"]')
                        ->pause(500);
            });

            // Verify piano keys are highlighted for the clicked chord
            $browser->within('.piano-player', function ($browser) {
                $browser->waitFor('.piano-key.active', 5)
                        ->assertPresent('.piano-key.active');
                
                // Check that the current chord is displayed
                $browser->assertDontSeeIn('.text-zinc-500', 'No chord selected');
            });

            // Click another chord slot
            $browser->within('.timeline-grid', function ($browser) {
                $browser->click('[data-chord-position="2"]')
                        ->pause(500);
            });

            // Verify different piano keys are now active
            $browser->within('.piano-player', function ($browser) {
                $browser->assertPresent('.piano-key.active');
            });
        });
    }

    /**
     * Test tempo control affects playback speed.
     */
    public function testTempoControlAffectsPlaybackSpeed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(1000);

            // Set tempo to a faster speed (180 BPM)
            $browser->clear('input[type="number"][min="60"][max="200"]')
                    ->type('input[type="number"][min="60"][max="200"]', '180')
                    ->keys('input[type="number"][min="60"][max="200"]', '{tab}') // Trigger change event
                    ->pause(500);

            // Start playback
            $browser->click('button[title="Play"]')
                    ->pause(500);

            // Measure progress after 2 seconds at fast tempo
            $browser->pause(2000);
            $fastProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');

            // Stop and reset
            $browser->click('button[title="Stop"]')
                    ->pause(500);

            // Set tempo to slower speed (60 BPM)
            $browser->clear('input[type="number"][min="60"][max="200"]')
                    ->type('input[type="number"][min="60"][max="200"]', '60')
                    ->keys('input[type="number"][min="60"][max="200"]', '{tab}')
                    ->pause(500);

            // Start playback again
            $browser->click('button[title="Play"]')
                    ->pause(500);

            // Measure progress after 2 seconds at slow tempo
            $browser->pause(2000);
            $slowProgress = $browser->element('.relative.h-2.bg-zinc-800 > div')->getCSSValue('width');

            // Stop playback
            $browser->click('button[title="Stop"]');

            // Fast tempo should have progressed more than slow tempo
            $fastProgressValue = (float) str_replace('px', '', $fastProgress);
            $slowProgressValue = (float) str_replace('px', '', $slowProgress);
            
            $this->assertGreaterThan($slowProgressValue, $fastProgressValue, 
                'Fast tempo (180 BPM) should progress further than slow tempo (60 BPM) in the same time');
        });
    }

    /**
     * Test that chord progression loops continuously.
     */
    public function testChordProgressionLoops(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(1000);

            // Set tempo to very fast for quicker testing (200 BPM)
            $browser->clear('input[type="number"][min="60"][max="200"]')
                    ->type('input[type="number"][min="60"][max="200"]', '200')
                    ->keys('input[type="number"][min="60"][max="200"]', '{tab}')
                    ->pause(500);

            // Start playback
            $browser->click('button[title="Play"]')
                    ->pause(500);

            // Wait for first chord to be highlighted (orange border)
            $browser->waitFor('[data-chord-position="1"] .border-orange-500', 5);

            // Wait for progression to complete and loop back
            // At 200 BPM, 4 beats per chord, 4 chords = 16 beats
            // 16 beats at 200 BPM = 4.8 seconds
            $browser->pause(5000);

            // Verify it looped back to the first chord
            $browser->assertPresent('[data-chord-position="1"] .border-orange-500');

            // Stop playback
            $browser->click('button[title="Stop"]');
        });
    }
}