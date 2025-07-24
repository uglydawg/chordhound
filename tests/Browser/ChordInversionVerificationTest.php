<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordInversionVerificationTest extends DuskTestCase
{
    /**
     * Test that inversions are correctly displayed for different keys
     */
    public function testInversionsDisplayCorrectlyForDifferentKeys(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Set key to C major first
            $browser->click('@key-C')
                ->click('@key-type-major')
                ->pause(500);

            // Test I-IV-V progression in C major
            $browser->select('@progression-selector', 'I-IV-V')
                ->pause(500);

            // Verify C major inversions
            $browser->assertSeeIn('@chord-1-display', 'C')
                ->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-display', 'F')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-display', 'G')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Test in G major
            $browser->click('@key-G')
                ->pause(500);

            $browser->assertSeeIn('@chord-1-display', 'G')
                ->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-display', 'C')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-display', 'D')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');

            // Test in D major
            $browser->click('@key-D')
                ->pause(500);

            $browser->assertSeeIn('@chord-1-display', 'D')
                ->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-display', 'G')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-display', 'A')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion');
        });
    }

    /**
     * Test that each progression maintains its specific inversions across keys
     */
    public function testProgressionSpecificInversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Test I-V-vi-IV progression
            $browser->select('@progression-selector', 'I-V-vi-IV')
                ->pause(500);

            // Expected inversions for I-V-vi-IV
            $expectedInversions = [
                'Root Inversion',
                'First Inversion',
                'First Inversion',
                'Second Inversion'
            ];

            // Test in C major
            $this->verifyInversions($browser, $expectedInversions);

            // Test in F major
            $browser->click('@key-F')
                ->pause(500);
            $this->verifyInversions($browser, $expectedInversions);

            // Test in A major
            $browser->click('@key-A')
                ->pause(500);
            $this->verifyInversions($browser, $expectedInversions);
        });
    }

    /**
     * Test that manually setting inversions persists until progression change
     */
    public function testManualInversionPersistence(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/chords')
                ->waitFor('.timeline-grid');

            // Start with a progression
            $browser->select('@progression-selector', 'I-vi-IV-V')
                ->pause(500);

            // Manually set all chords to root position
            for ($i = 1; $i <= 4; $i++) {
                $browser->click("@chord-button-{$i}")
                    ->within("@chord-{$i}", function ($chord) {
                        $chord->click('@inversion-root');
                    })
                    ->pause(200);
            }

            // Verify all are root position
            for ($i = 1; $i <= 4; $i++) {
                $browser->assertSeeIn("@chord-{$i}-inversion", 'Root Inversion');
            }

            // Change to a different progression
            $browser->select('@progression-selector', 'I-IV-V')
                ->pause(500);

            // Verify inversions are reset to progression defaults
            // Note: I-IV-V only has 3 chords, so chord 4 will be empty
            $browser->assertSeeIn('@chord-1-inversion', 'Root Inversion')
                ->assertSeeIn('@chord-2-inversion', 'Second Inversion')
                ->assertSeeIn('@chord-3-inversion', 'First Inversion')
                ->assertNotPresent('@chord-4-display'); // Fourth chord should be empty
        });
    }

    /**
     * Helper method to verify inversions
     */
    private function verifyInversions(Browser $browser, array $expectedInversions): void
    {
        foreach ($expectedInversions as $index => $inversion) {
            $position = $index + 1;
            $browser->assertSeeIn("@chord-{$position}-inversion", $inversion);
        }
    }
}