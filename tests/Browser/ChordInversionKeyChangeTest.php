<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordInversionKeyChangeTest extends DuskTestCase
{
    /**
     * Test that chord inversions update when changing keys for major keys
     */
    public function testChordInversionsUpdateWithMajorKeyChange(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Select I-IV-V progression
            $browser->select('@progression-selector', 'I-IV-V')
                ->pause(500);

            // Verify initial inversions for C major
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Change to G major
            $browser->click('@key-G')
                ->pause(500);

            // Verify inversions remain the same (they should)
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Verify the actual chord names changed
            $browser->assertSeeIn('@chord-1-display', 'G')
                ->assertSeeIn('@chord-2-display', 'C')
                ->assertSeeIn('@chord-3-display', 'D');
        });
    }

    /**
     * Test that chord inversions work correctly with minor keys
     */
    public function testChordInversionsWithMinorKeys(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Switch to minor key
            $browser->click('@key-type-minor')
                ->pause(500);

            // Select I-IV-V progression
            $browser->select('@progression-selector', 'I-IV-V')
                ->pause(500);

            // Verify inversions are applied (should default to root if not defined)
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertPresent('@chord-2-inversion')
                ->assertPresent('@chord-3-inversion');

            // Change to A minor
            $browser->click('@key-A')
                ->pause(500)
                ->screenshot('minor-key-chords');

            // Verify chord names for A minor key with I-IV-V progression
            // In minor key context, I-IV-V gives us Am-D-E (the IV and V are major chords)
            $browser->assertSeeIn('@chord-1-display', 'A')
                ->assertSeeIn('@chord-2-display', 'D')
                ->assertSeeIn('@chord-3-display', 'E');
        });
    }

    /**
     * Test that manually set inversions persist when changing keys
     */
    public function testManualInversionsResetWithKeyChange(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Select a progression
            $browser->select('@progression-selector', 'I-V-vi-IV')
                ->pause(500);

            // Manually change an inversion
            $browser->click('@chord-button-2')  // Select second chord
                ->within('@chord-2', function ($chord) {
                    $chord->click('@inversion-root');   // Change to root position
                })
                ->pause(500);

            // Verify manual change
            $browser->assertSeeIn('@chord-2-inversion', 'Root Inversion');

            // Change key
            $browser->click('@key-D')
                ->pause(500);

            // Verify progression inversions are reapplied (manual change should be overridden)
            $browser->assertSeeIn('@chord-2-inversion', 'First Inversion');
        });
    }

    /**
     * Test specific progression inversions across different keys
     */
    public function testProgressionInversionsAcrossKeys(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Test I-vi-IV-V progression in different keys
            $browser->select('@progression-selector', 'I-vi-IV-V')
                ->pause(500);

            $keysToTest = ['C', 'G', 'D', 'A', 'E'];
            $expectedInversions = ['Root Inversion', 'First Inversion', 'Second Inversion', 'First Inversion'];

            foreach ($keysToTest as $key) {
                $browser->click("@key-{$key}")
                    ->pause(500);

                // Verify inversions remain consistent
                foreach ($expectedInversions as $index => $inversion) {
                    $chordPosition = $index + 1;
                    $browser->assertSeeIn("@chord-{$chordPosition}-inversion", $inversion);
                }
            }
        });
    }

    /**
     * Test voice leading toggle interaction with key changes
     */
    public function testVoiceLeadingToggleWithKeyChanges(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Select progression and verify inversions
            $browser->select('@progression-selector', 'I-IV-V')
                ->pause(500);

            // Initial inversions
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Enable voice leading (shouldn't change preset inversions)
            $browser->click('@voice-leading-toggle')
                ->pause(500);

            // Verify inversions remain the same
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Change key with voice leading on
            $browser->click('@key-F')
                ->pause(500);

            // Verify inversions are still applied correctly
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');
        });
    }
}