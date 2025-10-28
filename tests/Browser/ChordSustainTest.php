<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordSustainTest extends DuskTestCase
{
    /**
     * Test that chord buttons sustain sound when held down.
     */
    public function test_chord_button_sustains_sound_when_held(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->assertSee('Chord Progression');

            // Wait for components to load
            $browser->waitFor('[wire\\:id]', 10)
                    ->pause(2000); // Give time for Livewire and audio to initialize

            // Click anywhere to initialize audio context (required by browsers)
            $browser->click('body')
                    ->pause(500);

            // Find the first chord button with content
            $browser->within('.timeline-grid', function ($browser) {
                // Wait for chord buttons to load
                $browser->waitFor('[dusk="chord-button-1"]', 5);

                // Check that window.startChordSustain exists
                $sustainFunctionExists = $browser->script('return typeof window.startChordSustain === "function"');
                $this->assertTrue($sustainFunctionExists[0], 'window.startChordSustain function should exist');

                $stopFunctionExists = $browser->script('return typeof window.stopChordSustain === "function"');
                $this->assertTrue($stopFunctionExists[0], 'window.stopChordSustain function should exist');

                // Get the chord data
                $chordElement = $browser->element('[dusk="chord-button-1"]');
                $this->assertNotNull($chordElement, 'First chord button should exist');

                // Simulate mousedown on the chord button
                $browser->script('
                    const chordButton = document.querySelector(\'[dusk="chord-button-1"]\');
                    const event = new MouseEvent("mousedown", {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    chordButton.dispatchEvent(event);
                ');

                $browser->pause(500); // Wait for sustain to start

                // Check console for sustain start message
                $logs = $browser->driver->manage()->getLog('browser');
                $foundStartLog = false;
                foreach ($logs as $log) {
                    if (str_contains($log['message'], 'startChordSustain called')) {
                        $foundStartLog = true;
                        break;
                    }
                }

                $this->assertTrue($foundStartLog, 'Should log startChordSustain call');

                // Check if piano keys are highlighted (indicating sound is playing)
                $activeKeys = $browser->script('return document.querySelectorAll(".piano-key.active").length');
                $this->assertGreaterThan(0, $activeKeys[0], 'Piano keys should be active during chord sustain');

                // Wait 2.5 seconds to check if the chord retriggered
                $browser->pause(2600);

                // Check console for retrigger message
                $logs = $browser->driver->manage()->getLog('browser');
                $foundRetriggerLog = false;
                foreach ($logs as $log) {
                    if (str_contains($log['message'], 'Re-triggering sustained chord')) {
                        $foundRetriggerLog = true;
                        break;
                    }
                }

                $this->assertTrue($foundRetriggerLog, 'Should retrigger chord after 2.5 seconds');

                // Simulate mouseup to stop the sustain
                $browser->script('
                    const chordButton = document.querySelector(\'[dusk="chord-button-1"]\');
                    const event = new MouseEvent("mouseup", {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    chordButton.dispatchEvent(event);
                ');

                $browser->pause(200);

                // Check that piano keys are no longer active
                $activeKeysAfter = $browser->script('return document.querySelectorAll(".piano-key.active").length');
                $this->assertEquals(0, $activeKeysAfter[0], 'Piano keys should not be active after mouseup');

                // Check console for stop message
                $logs = $browser->driver->manage()->getLog('browser');
                $foundStopLog = false;
                foreach ($logs as $log) {
                    if (str_contains($log['message'], 'stopChordSustain called')) {
                        $foundStopLog = true;
                        break;
                    }
                }

                $this->assertTrue($foundStopLog, 'Should log stopChordSustain call');
            });
        });
    }

    /**
     * Test that mouseleave also stops the sustain.
     */
    public function test_chord_button_stops_sustain_on_mouseleave(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('ChordHound', 10)
                    ->pause(2000);

            // Initialize audio
            $browser->click('body')->pause(500);

            $browser->within('.timeline-grid', function ($browser) {
                $browser->waitFor('[dusk="chord-button-1"]', 5);

                // Start sustain with mousedown
                $browser->script('
                    const chordButton = document.querySelector(\'[dusk="chord-button-1"]\');
                    chordButton.dispatchEvent(new MouseEvent("mousedown", { bubbles: true }));
                ');

                $browser->pause(500);

                // Verify keys are active
                $activeKeys = $browser->script('return document.querySelectorAll(".piano-key.active").length');
                $this->assertGreaterThan(0, $activeKeys[0], 'Piano keys should be active during sustain');

                // Trigger mouseleave
                $browser->script('
                    const chordButton = document.querySelector(\'[dusk="chord-button-1"]\');
                    chordButton.dispatchEvent(new MouseEvent("mouseleave", { bubbles: true }));
                ');

                $browser->pause(200);

                // Verify keys are no longer active
                $activeKeysAfter = $browser->script('return document.querySelectorAll(".piano-key.active").length');
                $this->assertEquals(0, $activeKeysAfter[0], 'Piano keys should not be active after mouseleave');
            });
        });
    }
}
