<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VoiceLeadingInversionsDisplayTest extends DuskTestCase
{
    /**
     * Test that voice-leading inversions are displayed correctly
     */
    public function test_voice_leading_inversions_display(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 5);

            // Ensure voice leading is enabled
            $voiceLeadingButton = $browser->element('button[wire\\:click="toggleVoiceLeading"]');
            if ($voiceLeadingButton) {
                $classes = $browser->attribute('button[wire\\:click="toggleVoiceLeading"]', 'class');
                if (!str_contains($classes, 'text-green-500')) {
                    $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                        ->pause(300);
                }
            }

            // Set key to C and progression to I-IV-V
            $browser->click('button[wire\\:click*="setKey(\'C\')"]')
                ->pause(200)
                ->select('select[wire\\:change*="setProgression"]', 'I-IV-V')
                ->pause(500);

            // Verify inversions are displayed correctly
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('F')
                    ->assertSee('2nd inv'); // Second inversion
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertSee('1st inv'); // First inversion
            });

            echo "✅ Voice-leading inversions displayed correctly for I-IV-V!\n";

            // Test ii-V-I jazz progression
            $browser->select('select[wire\\:change*="setProgression"]', 'ii-V-I')
                ->pause(500);

            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('Dm')
                    ->assertSee('2nd inv'); // Second inversion
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertSee('1st inv'); // First inversion
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertSee('Root');
            });

            echo "✅ Voice-leading inversions displayed correctly for ii-V-I!\n";
        });
    }

    /**
     * Test that inversions update when key changes
     */
    public function test_inversions_update_with_key_change(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 5);

            // Ensure voice leading is enabled
            $voiceLeadingButton = $browser->element('button[wire\\:click="toggleVoiceLeading"]');
            if ($voiceLeadingButton) {
                $classes = $browser->attribute('button[wire\\:click="toggleVoiceLeading"]', 'class');
                if (!str_contains($classes, 'text-green-500')) {
                    $browser->click('button[wire\\:click="toggleVoiceLeading"]')
                        ->pause(300);
                }
            }

            // Set to vi-IV-I-V in G
            $browser->click('button[wire\\:click*="setKey(\'G\')"]')
                ->pause(200)
                ->select('select[wire\\:change*="setProgression"]', 'vi-IV-I-V')
                ->pause(500);

            // Verify inversions for G major
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('Em')
                    ->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('C')
                    ->assertSee('1st inv');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertSee('2nd inv');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('D')
                    ->assertSee('Root');
            });

            // Change key to D
            $browser->click('button[wire\\:click*="setKey(\'D\')"]')
                ->pause(500);

            // Verify chords updated but inversions remain the same
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('Bm')
                    ->assertSee('Root');
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('G')
                    ->assertSee('1st inv');
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('D')
                    ->assertSee('2nd inv');
            });

            $browser->within('[data-chord-position="4"]', function ($browser) {
                $browser->assertSee('A')
                    ->assertSee('Root');
            });

            echo "✅ Inversions maintained correctly when key changes!\n";
        });
    }
}