<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PopProgressionInversionsDisplayTest extends DuskTestCase
{
    /**
     * Test that I-vi-IV-V progression displays correct inversions
     */
    public function test_pop_progression_displays_correct_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 5);

            // Enable voice leading first
            $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                ->pause(300);

            // Verify voice leading is enabled (button should be green)
            $classes = $browser->attribute('button[wire\\:click="toggleVoiceLeading"]', 'class');
            $this->assertStringContainsString('text-green-500', $classes, 'Voice leading should be enabled');

            // Select C major and I-vi-IV-V progression
            $browser->click('button[wire\\:click*="setKey(\'C\')"]')
                ->pause(200)
                ->select('select[wire\\:change*="setProgression"]', 'I-vi-IV-V')
                ->pause(500);

            // Verify the chords and inversions displayed
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('Am')
                    ->assertSee('1st inv');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('F')
                    ->assertSee('2nd inv');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertSee('1st inv');
            });

            echo "✅ I-vi-IV-V progression displays correct inversions!\n";

            // Test in another key
            $browser->click('button[wire\\:click*="setKey(\'G\')"]')
                ->pause(500);

            // Verify in G major
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertDontSee('Gm')
                    ->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('Em')
                    ->assertSee('1st inv');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertDontSee('Cm')
                    ->assertSee('2nd inv');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('D')
                    ->assertSee('1st inv');
            });

            echo "✅ Inversions maintained correctly when key changes!\n";
        });
    }

    /**
     * Test voice leading toggle effect on inversions
     */
    public function test_voice_leading_toggle_updates_display(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 5);

            // Set progression first with voice leading OFF
            $browser->select('select[wire\\:change*="setProgression"]', 'I-vi-IV-V')
                ->pause(500);

            // Verify all show root position when voice leading is off
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('Root');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('Root');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('Root');
            });

            // Enable voice leading
            $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                ->pause(500);

            // Verify inversions updated
            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('1st inv');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('2nd inv');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('1st inv');
            });

            echo "✅ Voice leading toggle correctly updates inversion display!\n";
        });
    }
}