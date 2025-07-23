<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProgressionChangeTest extends DuskTestCase
{
    /**
     * Test that progression changes work in browser
     */
    public function test_progression_change_in_browser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 5);

            // Check initial progression
            $selectedValue = $browser->value('select[wire\\:change*="setProgression"]');
            echo "Initial progression: $selectedValue\n";

            // Verify initial chords
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $chord1 = $browser->text('.text-2xl');
                echo "Initial chord 1: $chord1\n";
            });

            // Change progression
            $browser->select('select[wire\\:change*="setProgression"]', 'I-IV-V')
                ->pause(1000); // Give time for update

            // Check if chords changed
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $chord1 = $browser->text('.text-2xl');
                echo "After change chord 1: $chord1\n";
                $this->assertEquals('C', $chord1);
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $chord2 = $browser->text('.text-2xl');
                echo "After change chord 2: $chord2\n";
                $this->assertEquals('F', $chord2);
            });

            echo "✅ Progression change test completed!\n";
        });
    }

    /**
     * Test key change
     */
    public function test_key_change_in_browser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.timeline-grid', 10)
                ->pause(2000); // Wait for any JavaScript to load

            // Set a known progression first
            $browser->select('select[wire\\:change*="setProgression"]', 'I-IV-V')
                ->pause(500);

            // Check initial key
            $cButtonClasses = $browser->attribute('button[wire\\:click*="setKey(\'C\')"]', 'class');
            echo "C button classes: $cButtonClasses\n";

            // Change to G key
            $browser->click('button[wire\\:click*="setKey(\'G\')"]')
                ->pause(1000);

            // Verify G is selected
            $gButtonClasses = $browser->attribute('button[wire\\:click*="setKey(\'G\')"]', 'class');
            echo "G button classes after click: $gButtonClasses\n";
            $this->assertStringContainsString('bg-blue-600', $gButtonClasses);

            // Verify chords changed to G major
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $chord1 = $browser->text('.text-2xl');
                echo "G major chord 1: $chord1\n";
                $this->assertEquals('G', $chord1);
            });

            echo "✅ Key change test completed!\n";
        });
    }
}