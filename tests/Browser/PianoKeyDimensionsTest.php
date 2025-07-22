<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoKeyDimensionsTest extends DuskTestCase
{
    /**
     * Test that piano keys have realistic dimensions.
     */
    public function testPianoKeyDimensions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(3000) // Give time for page and Livewire to load
                ->screenshot('piano-test-page')
                ->assertPresent('body'); // Basic check
                
            // Check if piano elements exist
            $pianoExists = $browser->script("return document.querySelector('.piano-player') !== null")[0];
            $this->assertTrue($pianoExists, 'Piano player should exist on the page');
            
            if ($pianoExists) {
                $browser->waitFor('#piano-keyboard', 10)
                    ->assertVisible('#piano-keyboard');

                // Get piano container dimensions
                $containerWidth = $browser->script('return document.querySelector("#piano-keyboard .piano-keys").offsetWidth')[0];
                $containerHeight = $browser->script('return document.querySelector("#piano-keyboard .piano-keys").offsetHeight')[0];

                // Assert container has proper dimensions
                $this->assertGreaterThanOrEqual(800, $containerWidth, 'Piano container should be at least 800px wide');
                $this->assertLessThanOrEqual(1200, $containerWidth, 'Piano container should be at most 1200px wide');
                $this->assertEquals(150, $containerHeight, 'Piano container should be 150px tall');

            // Test white key dimensions
            $whiteKeys = $browser->elements('.white-key');
            $this->assertCount(21, $whiteKeys, 'Should have 21 white keys (3 octaves)');

            // Get first white key dimensions
            $firstWhiteKey = $browser->script('
                const key = document.querySelector(".white-key");
                return {
                    width: key.offsetWidth,
                    height: key.offsetHeight,
                    computedStyle: window.getComputedStyle(key)
                };
            ')[0];

            // Calculate expected white key width (container width / 21 keys)
            $expectedWhiteKeyWidth = $containerWidth / 21;
            
            // Allow for small variations due to borders and margins
            $this->assertEqualsWithDelta(
                $expectedWhiteKeyWidth,
                $firstWhiteKey['width'],
                5, // 5px tolerance
                'White keys should be evenly distributed'
            );
            
            // White keys should use full height
            $this->assertGreaterThanOrEqual(145, $firstWhiteKey['height'], 'White keys should be nearly full height');

            // Test black key dimensions
            $blackKeys = $browser->elements('.black-key');
            $this->assertCount(15, $blackKeys, 'Should have 15 black keys (3 octaves)');

            // Get first black key dimensions
            $firstBlackKey = $browser->script('
                const key = document.querySelector(".black-key");
                return {
                    width: key.offsetWidth,
                    height: key.offsetHeight,
                    left: key.offsetLeft,
                    computedStyle: window.getComputedStyle(key)
                };
            ')[0];

            // Black keys should be 55% width of white keys
            $expectedBlackKeyWidth = $expectedWhiteKeyWidth * 0.55;
            $this->assertEqualsWithDelta(
                $expectedBlackKeyWidth,
                $firstBlackKey['width'],
                3, // 3px tolerance
                'Black keys should be 55% width of white keys'
            );

            // Black keys should be 65% height of container
            $expectedBlackKeyHeight = $containerHeight * 0.65;
            $this->assertEqualsWithDelta(
                $expectedBlackKeyHeight,
                $firstBlackKey['height'],
                3, // 3px tolerance
                'Black keys should be 65% height of container'
            );
            }
        });
    }

    /**
     * Test black key positioning.
     */
    public function testBlackKeyPositioning(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10)
                ->waitFor('#piano-keyboard', 10);

            // Test specific black key positions
            $blackKeyPositions = $browser->script('
                const blackKeys = Array.from(document.querySelectorAll(".black-key"));
                return blackKeys.map(key => ({
                    note: key.getAttribute("data-note"),
                    left: key.offsetLeft,
                    width: key.offsetWidth
                }));
            ')[0];

            // Group by octave for easier testing
            $octaveGroups = [];
            foreach ($blackKeyPositions as $key) {
                $octave = substr($key['note'], -1);
                if (!isset($octaveGroups[$octave])) {
                    $octaveGroups[$octave] = [];
                }
                $octaveGroups[$octave][] = $key;
            }

            // Verify each octave has 5 black keys
            foreach ($octaveGroups as $octave => $keys) {
                $this->assertCount(5, $keys, "Octave $octave should have 5 black keys");
            }

            // Test that black keys are properly spaced within each octave
            foreach ($octaveGroups as $octave => $keys) {
                // Sort by position
                usort($keys, fn($a, $b) => $a['left'] <=> $b['left']);

                // Check that keys are in the correct order
                $expectedOrder = ["C#$octave", "D#$octave", "F#$octave", "G#$octave", "A#$octave"];
                $actualOrder = array_map(fn($k) => $k['note'], $keys);
                $this->assertEquals($expectedOrder, $actualOrder, "Black keys in octave $octave should be in correct order");
            }
        });
    }

    /**
     * Test piano key visual appearance.
     */
    public function testPianoKeyVisualAppearance(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10)
                ->waitFor('#piano-keyboard', 10);

            // Test white key styling
            $whiteKeyStyle = $browser->script('
                const key = document.querySelector(".white-key");
                const style = window.getComputedStyle(key);
                return {
                    background: style.background,
                    borderRadius: style.borderRadius,
                    boxShadow: style.boxShadow
                };
            ')[0];

            $this->assertStringContainsString('linear-gradient', $whiteKeyStyle['background'], 'White keys should have gradient background');
            $this->assertStringContainsString('6px', $whiteKeyStyle['borderRadius'], 'White keys should have rounded bottom corners');
            $this->assertStringContainsString('rgba(0, 0, 0, 0.3)', $whiteKeyStyle['boxShadow'], 'White keys should have shadow');

            // Test black key styling
            $blackKeyStyle = $browser->script('
                const key = document.querySelector(".black-key");
                const style = window.getComputedStyle(key);
                return {
                    background: style.background,
                    borderRadius: style.borderRadius,
                    boxShadow: style.boxShadow
                };
            ')[0];

            $this->assertStringContainsString('linear-gradient', $blackKeyStyle['background'], 'Black keys should have gradient background');
            $this->assertStringContainsString('4px', $blackKeyStyle['borderRadius'], 'Black keys should have rounded bottom corners');
            $this->assertStringContainsString('rgba(0, 0, 0, 0.5)', $blackKeyStyle['boxShadow'], 'Black keys should have shadow');
        });
    }

    /**
     * Test responsive behavior of piano.
     */
    public function testPianoResponsiveBehavior(): void
    {
        $this->browse(function (Browser $browser) {
            // Test at different viewport widths
            $viewports = [
                ['width' => 1920, 'height' => 1080],
                ['width' => 1366, 'height' => 768],
                ['width' => 1024, 'height' => 768],
                ['width' => 768, 'height' => 1024],
            ];

            foreach ($viewports as $viewport) {
                $browser->resize($viewport['width'], $viewport['height'])
                    ->visit('/')
                    ->waitFor('.piano-player', 10)
                    ->waitFor('#piano-keyboard', 10);

                // Get container width
                $containerWidth = $browser->script('return document.querySelector("#piano-keyboard .piano-keys").offsetWidth')[0];

                // Container should respect min/max width constraints
                $this->assertGreaterThanOrEqual(800, $containerWidth, "Container should be at least 800px at {$viewport['width']}px viewport");
                $this->assertLessThanOrEqual(1200, $containerWidth, "Container should be at most 1200px at {$viewport['width']}px viewport");

                // Verify keys maintain proper proportions
                $keyData = $browser->script('
                    const whiteKey = document.querySelector(".white-key");
                    const blackKey = document.querySelector(".black-key");
                    return {
                        whiteWidth: whiteKey.offsetWidth,
                        blackWidth: blackKey.offsetWidth,
                        ratio: blackKey.offsetWidth / whiteKey.offsetWidth
                    };
                ')[0];

                $this->assertEqualsWithDelta(
                    0.55,
                    $keyData['ratio'],
                    0.05,
                    "Black/white key ratio should be maintained at {$viewport['width']}px viewport"
                );
            }
        });
    }
}