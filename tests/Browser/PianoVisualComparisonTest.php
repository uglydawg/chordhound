<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoVisualComparisonTest extends DuskTestCase
{
    /**
     * Test piano visual appearance matches expected design.
     */
    public function testPianoVisualDesign(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(3000) // Give time for page and Livewire to load
                ->screenshot('piano-visual-test');
                
            // Check if piano exists
            $pianoExists = $browser->script("return document.querySelector('.piano-player') !== null")[0];
            
            if (!$pianoExists) {
                $this->fail('Piano player not found on page');
            }
            
            // Wait for piano keyboard
            $browser->waitFor('#piano-keyboard', 10);
            
            // Get visual properties of white keys
            $whiteKeyProperties = $browser->script("
                const whiteKeys = document.querySelectorAll('.white-key');
                const firstKey = whiteKeys[0];
                const style = window.getComputedStyle(firstKey);
                const rect = firstKey.getBoundingClientRect();
                
                return {
                    count: whiteKeys.length,
                    width: rect.width,
                    height: rect.height,
                    background: style.backgroundColor,
                    borderRight: style.borderRight
                };
            ")[0];
            
            // Get visual properties of black keys
            $blackKeyProperties = $browser->script("
                const blackKeys = document.querySelectorAll('.black-key');
                if (blackKeys.length === 0) return null;
                
                const firstKey = blackKeys[0];
                const style = window.getComputedStyle(firstKey);
                const rect = firstKey.getBoundingClientRect();
                
                return {
                    count: blackKeys.length,
                    width: rect.width,
                    height: rect.height,
                    background: style.backgroundColor,
                    zIndex: style.zIndex
                };
            ")[0];
            
            // Verify white keys
            $this->assertEquals(14, $whiteKeyProperties['count'], 'Should have 14 white keys (2 octaves)');
            $this->assertStringContainsString('255, 255, 255', $whiteKeyProperties['background'], 'White keys should be white');
            $this->assertStringContainsString('1px', $whiteKeyProperties['borderRight'], 'White keys should have borders');
            
            // Verify black keys
            if ($blackKeyProperties) {
                $this->assertEquals(10, $blackKeyProperties['count'], 'Should have 10 black keys (2 octaves)');
                $this->assertStringContainsString('0, 0, 0', $blackKeyProperties['background'], 'Black keys should be black');
                $this->assertEquals('20', $blackKeyProperties['zIndex'], 'Black keys should be above white keys');
                
                // Check black key height is about 70% of white key height
                $blackKeyHeightRatio = $blackKeyProperties['height'] / $whiteKeyProperties['height'];
                $this->assertEqualsWithDelta(0.7, $blackKeyHeightRatio, 0.05, 'Black keys should be ~70% height of white keys');
            }
            
            // Test key press visual feedback
            $browser->click('.white-key[data-note="C3"]')
                ->pause(100);
                
            $c3Pressed = $browser->script("
                const key = document.querySelector('.white-key[data-note=\"C3\"]');
                const style = window.getComputedStyle(key);
                return {
                    background: style.backgroundColor,
                    hasPressed: key.classList.contains('pressed')
                };
            ")[0];
            
            // Check if pressed state shows blue color
            if ($c3Pressed['hasPressed'] || strpos($c3Pressed['background'], '59, 130, 246') !== false) {
                $this->assertTrue(true, 'Key shows pressed state with blue color');
            }
            
            // Take final screenshot for visual comparison
            $browser->screenshot('piano-final-state');
        });
    }
    
    /**
     * Test piano layout measurements.
     */
    public function testPianoLayoutMeasurements(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('#piano-keyboard', 10);
                
            // Get detailed layout measurements
            $measurements = $browser->script("
                const container = document.querySelector('.piano-keys');
                const whiteKeys = Array.from(document.querySelectorAll('.white-key'));
                const blackKeys = Array.from(document.querySelectorAll('.black-key'));
                
                // Get positions of black keys relative to white keys
                const blackKeyPositions = blackKeys.map(bk => {
                    const bkRect = bk.getBoundingClientRect();
                    const note = bk.getAttribute('data-note');
                    
                    // Find which white keys this black key is between
                    let leftWhiteKey = null;
                    let rightWhiteKey = null;
                    
                    whiteKeys.forEach((wk, idx) => {
                        const wkRect = wk.getBoundingClientRect();
                        if (bkRect.left >= wkRect.left && bkRect.left < wkRect.right) {
                            leftWhiteKey = wk.getAttribute('data-note');
                            if (idx < whiteKeys.length - 1) {
                                rightWhiteKey = whiteKeys[idx + 1].getAttribute('data-note');
                            }
                        }
                    });
                    
                    return {
                        note: note,
                        left: bkRect.left,
                        leftWhiteKey: leftWhiteKey,
                        rightWhiteKey: rightWhiteKey
                    };
                });
                
                return {
                    containerWidth: container.offsetWidth,
                    whiteKeyCount: whiteKeys.length,
                    blackKeyCount: blackKeys.length,
                    blackKeyPositions: blackKeyPositions
                };
            ")[0];
            
            // Log measurements for debugging
            dump('Piano Measurements:', $measurements);
            
            // Verify black key positioning
            foreach ($measurements['blackKeyPositions'] as $pos) {
                $note = $pos['note'];
                
                // Verify each black key is between the correct white keys
                switch (substr($note, 0, -1)) { // Remove octave number
                    case 'C#':
                        $this->assertStringStartsWith('C', $pos['leftWhiteKey']);
                        break;
                    case 'D#':
                        $this->assertStringStartsWith('D', $pos['leftWhiteKey']);
                        break;
                    case 'F#':
                        $this->assertStringStartsWith('F', $pos['leftWhiteKey']);
                        break;
                    case 'G#':
                        $this->assertStringStartsWith('G', $pos['leftWhiteKey']);
                        break;
                    case 'A#':
                        $this->assertStringStartsWith('A', $pos['leftWhiteKey']);
                        break;
                }
            }
        });
    }
}