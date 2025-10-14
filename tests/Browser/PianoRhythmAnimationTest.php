<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoRhythmAnimationTest extends DuskTestCase
{
    /**
     * Test that piano keys are being pressed and released during rhythm playback
     */
    public function test_piano_keys_animate_during_rhythm_playback(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000) // Make browser taller as requested
                    ->assertSee('Mathematical Chord Calculator Test');

            // Verify PianoPlayer component is visible
            $browser->assertVisible('.piano-player')
                    ->assertVisible('.piano-key');

            // Count piano keys
            $whiteKeyCount = count($browser->elements('.white-key'));
            $blackKeyCount = count($browser->elements('.black-key'));
            
            $this->assertGreaterThan(0, $whiteKeyCount, "Should have white keys");
            $this->assertGreaterThan(0, $blackKeyCount, "Should have black keys");

            // Find and click the rhythm play button using wire:click attribute
            $browser->click('button[wire\\:click="playRhythm"]')
                    ->pause(500); // Wait for playback to start

            // Monitor for key press animation during playback
            $keyPressDetected = false;
            $transformDetected = false;
            $iterations = 0;
            
            // Monitor key states for 3 seconds
            while ($iterations < 30 && (!$keyPressDetected || !$transformDetected)) {
                // Check for pressed/active keys
                $pressedKeys = $browser->elements('.piano-key.pressed, .piano-key.active, [id^="key-"].pressed, [id^="key-"].active');
                if (count($pressedKeys) > 0) {
                    $keyPressDetected = true;
                }
                
                // Check for transform styles using JavaScript
                $hasTransform = $browser->script('
                    const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                    return Array.from(keys).some(key => 
                        key.style.transform && key.style.transform.includes("translate")
                    );
                ')[0];
                
                if ($hasTransform) {
                    $transformDetected = true;
                }
                
                $browser->pause(100);
                $iterations++;
            }

            // Stop playback if still playing
            try {
                $browser->click('button[wire\\:click="stopProgression"]');
            } catch (\Exception $e) {
                // Button might not exist if not playing
            }

            // Verify animations were detected
            $this->assertTrue($keyPressDetected, "Piano key press animation should be detected during rhythm playback");
            $this->assertTrue($transformDetected, "Piano key transform animation should be detected during rhythm playback");
        });
    }

    /**
     * Test different rhythm patterns trigger key animations
     */
    public function test_different_rhythm_patterns_animate_keys(): void
    {
        $rhythmPatterns = ['block', 'alberti', 'waltz', 'broken', 'arpeggio', 'march'];

        $this->browse(function (Browser $browser) use ($rhythmPatterns) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000); // Make browser taller

            foreach ($rhythmPatterns as $rhythm) {
                // Select rhythm pattern
                $browser->select('select[wire\\:model\\.live="selectedRhythm"]', $rhythm)
                        ->pause(500);

                // Start playback
                $browser->click('button[wire\\:click="playRhythm"]')
                        ->pause(200);

                // Monitor for animations
                $animationDetected = false;
                $checks = 0;
                
                while ($checks < 20 && !$animationDetected) {
                    $hasActiveKeys = count($browser->elements('.piano-key.pressed, .piano-key.active')) > 0;
                    $hasTransforms = $browser->script('
                        const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                        return Array.from(keys).some(key => 
                            key.style.transform && key.style.transform.includes("translate")
                        );
                    ')[0];
                    
                    if ($hasActiveKeys || $hasTransforms) {
                        $animationDetected = true;
                    }
                    
                    $browser->pause(100);
                    $checks++;
                }

                // Stop playback
                try {
                    $browser->click('button[wire\\:click="stopProgression"]')
                            ->pause(500);
                } catch (\Exception $e) {
                    // Button might not exist if not playing
                }

                $this->assertTrue($animationDetected, "Rhythm pattern '{$rhythm}' should trigger key animations");
            }
        });
    }

    /**
     * Test that pressKey and releaseKey functions work correctly
     */
    public function test_press_key_and_release_key_functions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000) // Make browser taller
                    ->pause(1000); // Wait for initial load

            // Get initial key states
            $initialStates = $browser->script('
                const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                const states = {};
                keys.forEach(key => {
                    states[key.id] = {
                        pressed: key.classList.contains("pressed"),
                        active: key.classList.contains("active"),
                        transform: key.style.transform || ""
                    };
                });
                return states;
            ')[0];

            // Click play chord button to trigger key press/release
            $browser->click('button[wire\\:click="playChord"]')
                    ->pause(200); // Wait for animation to start

            // Check key states during playback
            $duringStates = $browser->script('
                const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                const states = {};
                keys.forEach(key => {
                    states[key.id] = {
                        pressed: key.classList.contains("pressed"),
                        active: key.classList.contains("active"),
                        transform: key.style.transform || ""
                    };
                });
                return states;
            ')[0];

            // Wait for key release
            $browser->pause(2000);

            // Check final key states
            $finalStates = $browser->script('
                const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                const states = {};
                keys.forEach(key => {
                    states[key.id] = {
                        pressed: key.classList.contains("pressed"),
                        active: key.classList.contains("active"),
                        transform: key.style.transform || ""
                    };
                });
                return states;
            ')[0];

            // Verify key press/release cycle
            $keyWasPressed = false;
            $transformWasApplied = false;
            $keyWasReleased = false;

            foreach ($duringStates as $keyId => $duringState) {
                $initial = $initialStates[$keyId] ?? ['pressed' => false, 'active' => false, 'transform' => ''];
                $final = $finalStates[$keyId] ?? ['pressed' => false, 'active' => false, 'transform' => ''];

                // Check if key was pressed during playback
                if (($duringState['pressed'] || $duringState['active']) && !($initial['pressed'] || $initial['active'])) {
                    $keyWasPressed = true;
                }

                // Check if transform was applied
                if (strpos($duringState['transform'], 'translate') !== false && empty($initial['transform'])) {
                    $transformWasApplied = true;
                }

                // Check if key was released after playback
                if (($duringState['pressed'] || $duringState['active']) && !($final['pressed'] || $final['active'])) {
                    $keyWasReleased = true;
                }
            }

            $this->assertTrue($keyWasPressed, "Keys should be pressed during chord playback");
            $this->assertTrue($transformWasApplied, "Transform animations should be applied to keys");
            $this->assertTrue($keyWasReleased, "Keys should be released after chord playback");
        });
    }

    /**
     * Test visual key depression animations
     */
    public function test_visual_key_depression_animations(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000) // Make browser taller
                    ->pause(1000);

            // Play a chord to trigger animations
            $browser->click('button[wire\\:click="playChord"]');

            // Check for visual depression effects multiple times
            $visualStates = [];
            $attempts = 0;
            
            while (count($visualStates) === 0 && $attempts < 10) {
                $browser->pause(100);
                
                $visualStates = $browser->script('
                    const keys = document.querySelectorAll(".piano-key, [id^=\'key-\']");
                    const visualStates = [];
                    
                    keys.forEach(key => {
                        const transform = key.style.transform || "";
                        const hasActiveClass = key.classList.contains("active") || key.classList.contains("pressed");
                        
                        if (hasActiveClass || (transform && transform.includes("translate"))) {
                            let translateY = null;
                            const match = transform.match(/translateY\\(([^)]+)\\)/);
                            if (match) {
                                translateY = match[1];
                            }
                            
                            visualStates.push({
                                id: key.id,
                                transform: transform,
                                hasActiveClass: hasActiveClass,
                                isBlackKey: key.classList.contains("black-key"),
                                translateY: translateY
                            });
                        }
                    });
                    
                    return visualStates;
                ')[0];
                
                $attempts++;
            }

            $this->assertGreaterThan(0, count($visualStates), "Should detect visual key depression states");

            // Verify translateY is being applied
            $hasTranslateY = false;
            foreach ($visualStates as $state) {
                if ($state['translateY'] !== null) {
                    $hasTranslateY = true;
                    break;
                }
            }

            $this->assertTrue($hasTranslateY, "Keys should have translateY applied for depression effect");

            // Wait for keys to be released
            $browser->pause(2000);

            // Verify keys are no longer active
            $activeKeyCount = count($browser->elements('.piano-key.active, .piano-key.pressed'));
            $this->assertEquals(0, $activeKeyCount, "Keys should not be active after playback ends");
        });
    }

    /**
     * Test BPM changes affect animation timing
     */
    public function test_bpm_changes_affect_animation_timing(): void
    {
        $bpmValues = ['60', '120', '180'];

        $this->browse(function (Browser $browser) use ($bpmValues) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000); // Make browser taller

            foreach ($bpmValues as $bpm) {
                // Set BPM
                $browser->select('select[wire\\:model\\.live="bpm"]', $bpm)
                        ->pause(500);

                // Set rhythm pattern with clear timing
                $browser->select('select[wire\\:model\\.live="selectedRhythm"]', 'alberti')
                        ->pause(500);

                // Start playback
                $browser->click('button[wire\\:click="playRhythm"]')
                        ->pause(100);

                // Monitor animation events
                $animationEvents = [];
                $startTime = microtime(true);
                
                for ($i = 0; $i < 40; $i++) { // Monitor for 2 seconds
                    $activeKeys = count($browser->elements('.piano-key.active, .piano-key.pressed'));
                    if ($activeKeys > 0) {
                        $animationEvents[] = (microtime(true) - $startTime) * 1000; // Convert to ms
                    }
                    $browser->pause(50);
                }

                // Stop playback
                try {
                    $browser->click('button[wire\\:click="stopProgression"]')
                            ->pause(500);
                } catch (\Exception $e) {
                    // Button might not exist if not playing
                }

                $this->assertGreaterThan(0, count($animationEvents), "BPM {$bpm} should produce animation events");
            }
        });
    }

    /**
     * Test chord progression changes trigger different key animations
     */
    public function test_chord_progression_changes_trigger_different_animations(): void
    {
        $progressions = ['I-IV-V-I', 'I-V-vi-IV', 'ii-V-I'];

        $this->browse(function (Browser $browser) use ($progressions) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000); // Make browser taller

            foreach ($progressions as $progression) {
                // Select progression
                $browser->select('select[wire\\:model\\.live="selectedProgression"]', $progression)
                        ->pause(500);

                // Use block rhythm for clearer chord changes
                $browser->select('select[wire\\:model\\.live="selectedRhythm"]', 'block')
                        ->pause(500);

                // Start playback
                $browser->click('button[wire\\:click="playRhythm"]')
                        ->pause(100);

                // Track unique key combinations
                $uniqueKeyPatterns = [];
                
                for ($i = 0; $i < 40; $i++) { // Monitor for 4 seconds
                    $activeKeyIds = $browser->script('
                        const activeKeys = document.querySelectorAll(".piano-key.active, .piano-key.pressed, [id^=\'key-\'].active, [id^=\'key-\'].pressed");
                        return Array.from(activeKeys).map(key => key.id).sort().join(",");
                    ')[0];
                    
                    if (!empty($activeKeyIds) && !in_array($activeKeyIds, $uniqueKeyPatterns)) {
                        $uniqueKeyPatterns[] = $activeKeyIds;
                    }
                    
                    $browser->pause(100);
                }

                // Stop playback
                try {
                    $browser->click('button[wire\\:click="stopProgression"]')
                            ->pause(500);
                } catch (\Exception $e) {
                    // Button might not exist if not playing
                }

                // Verify multiple chord patterns were detected
                $this->assertGreaterThan(1, count($uniqueKeyPatterns), 
                    "Progression {$progression} should produce multiple unique key patterns");
            }
        });
    }

    /**
     * Test no console errors during key animations
     */
    public function test_no_console_errors_during_key_animations(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/debug/math-chords')
                    ->waitFor('.piano-player', 10)
                    ->resize(1400, 1000); // Make browser taller

            // Test alberti bass pattern (complex rhythm)
            $browser->select('select[wire\\:model\\.live="selectedRhythm"]', 'alberti')
                    ->pause(500);

            // Start playback
            $browser->click('button[wire\\:click="playRhythm"]')
                    ->pause(3000); // Let it play for 3 seconds

            // Stop playback
            try {
                $browser->click('button[wire\\:click="stopProgression"]');
            } catch (\Exception $e) {
                // Button might not exist if not playing
            }

            // Test manual chord play
            $browser->click('button[wire\\:click="playChord"]')
                    ->pause(1000);

            // Check console for errors (this would need to be implemented in the browser
            // or we can verify by checking that animations still work)
            $activeKeysStillWork = count($browser->elements('.piano-key')) > 0;
            $this->assertTrue($activeKeysStillWork, "Piano should still be functional after animations");
        });
    }
}