<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CompactChordDisplayTest extends DuskTestCase
{
    /**
     * Test that chord boxes are compact and show inversions
     */
    public function test_chord_boxes_are_compact_with_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Apply a progression to see inversions
            $browser->select('select[wire\\:change*="setKey"]', 'C')
                ->pause(100)
                ->select('select[wire\\:change*="setProgression"]', 'I-IV-V')
                ->pause(500);

            // Verify chord boxes show chord names and inversions
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('C')       // Chord name
                    ->assertSee('Root');       // Inversion display
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('F')       // Chord name
                    ->assertSee('2nd inv');    // Second inversion
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('G')       // Chord name
                    ->assertSee('1st inv');    // First inversion
            });

            // Check that chord notes are displayed in smaller text
            $chordBox = $browser->element('[data-chord-position="1"] .chord-button');
            $this->assertNotNull($chordBox, 'Chord box should exist');

            // Verify compact size - should have p-3 class (padding: 0.75rem)
            $classes = $browser->attribute('[data-chord-position="1"] [wire\\:click*="selectChord"]', 'class');
            $this->assertStringContainsString('p-3', $classes);
            $this->assertStringContainsString('min-h-[120px]', $classes);

            echo "✅ Chord boxes are compact with inversions displayed!\n";
        });
    }

    /**
     * Test different progression inversions display correctly
     */
    public function test_different_progressions_show_correct_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Test ii-V-I jazz progression
            $browser->select('select[wire\\:change*="setKey"]', 'F')
                ->pause(100)
                ->select('select[wire\\:change*="setProgression"]', 'ii-V-I')
                ->pause(500);

            // Verify inversions for jazz progression
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('Gm')      // ii chord
                    ->assertSee('2nd inv');    // Second inversion
            });

            $browser->within('[data-chord-position="2"]', function ($browser) {
                $browser->assertSee('C')       // V chord
                    ->assertSee('1st inv');    // First inversion
            });

            $browser->within('[data-chord-position="3"]', function ($browser) {
                $browser->assertSee('F')       // I chord
                    ->assertSee('Root');       // Root position
            });

            echo "✅ Jazz progression inversions display correctly!\n";
        });
    }

    /**
     * Test chord notes display in compact format
     */
    public function test_chord_notes_display_compactly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.chord-grid', 5);

            // Set a simple C major chord
            $browser->click('[data-chord-position="1"]')
                ->waitFor('.chord-selector', 2)
                ->select('select[wire\\:model*="tone"]', 'C')
                ->pause(100);

            // Close selector
            $browser->click('.close-button')
                ->pause(200);

            // Verify chord notes are shown
            $browser->within('[data-chord-position="1"]', function ($browser) {
                $browser->assertSee('C E G'); // Notes should be space-separated
            });

            // Check text size is small (text-[10px] class)
            $notesElement = $browser->element('[data-chord-position="1"] .text-\\[10px\\]');
            $this->assertNotNull($notesElement, 'Notes should be displayed in small text');

            echo "✅ Chord notes displayed in compact format!\n";
        });
    }
}