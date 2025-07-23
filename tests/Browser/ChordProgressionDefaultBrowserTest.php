<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordProgressionDefaultBrowserTest extends DuskTestCase
{
    /**
     * Test that default chord progression uses C major key
     */
    public function test_default_chord_progression_is_in_c_major(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Check default key is C
            $browser->within('.timeline-grid', function ($browser) {
                // Find the C key button and verify it's selected (has bg-blue-600 class)
                $cButton = $browser->element('button[wire\\:click*="setKey(\'C\')"]');
                $this->assertNotNull($cButton, 'C key button should exist');
                
                $classes = $browser->attribute('button[wire\\:click*="setKey(\'C\')"]', 'class');
                $this->assertStringContainsString('bg-blue-600', $classes, 'C key should be selected by default');
            });

            // Verify default progression I-V-vi-IV in C major
            // Should be: C - G - Am - F
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertDontSee('Cm')
                    ->assertDontSee('Cdim');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertDontSee('Gm')
                    ->assertDontSee('Gdim');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('Am');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('F')
                    ->assertDontSee('Fm')
                    ->assertDontSee('Fdim');
            });

            // Verify progression selector shows I-V-vi-IV
            $progressionSelect = $browser->value('select[wire\\:change*="setProgression"]');
            $this->assertEquals('I-V-vi-IV', $progressionSelect, 'Default progression should be I-V-vi-IV');

            // Verify the transposed chord display shows the correct chords
            $browser->assertSee('= C - G - Am - F');

            echo "✅ Default chord progression correctly shows C major!\n";
        });
    }

    /**
     * Test changing to a different key updates chords correctly
     */
    public function test_changing_key_updates_chord_progression(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Change to G major
            $browser->click('button[wire\\:click*="setKey(\'G\')"]')
                ->pause(500);

            // Verify G key is selected
            $classes = $browser->attribute('button[wire\\:click*="setKey(\'G\')"]', 'class');
            $this->assertStringContainsString('bg-blue-600', $classes, 'G key should be selected');

            // Verify progression in G major: G - D - Em - C
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('G');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('D');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('Em');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('C');
            });

            // Verify the transposed chord display
            $browser->assertSee('= G - D - Em - C');

            echo "✅ Chord progression correctly transposes to G major!\n";
        });
    }
}