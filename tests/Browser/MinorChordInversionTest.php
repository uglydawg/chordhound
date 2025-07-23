<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MinorChordInversionTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Minor chord voicing specifications
     * Format: [root position, first inversion, second inversion]
     */
    private array $minorChordVoicings = [
        'C' => [
            'root' => ['C4', 'Eb4', 'G4'],
            'first' => ['Eb4', 'G4', 'C5'],
            'second' => ['G3', 'C4', 'Eb4']
        ],
        'C#' => [
            'root' => ['C#4', 'E4', 'G#4'],
            'first' => ['E3', 'G#3', 'C#4'],
            'second' => ['G#3', 'C#4', 'E4']
        ],
        'D' => [
            'root' => ['D4', 'F4', 'A4'],
            'first' => ['F3', 'A3', 'D4'],
            'second' => ['A3', 'D4', 'F4']
        ],
        'D#' => [
            'root' => ['D#4', 'F#4', 'A#4'],
            'first' => ['F#3', 'A#3', 'D#4'],
            'second' => ['A#3', 'D#4', 'F#4']
        ],
        'E' => [
            'root' => ['E4', 'G4', 'B4'],
            'first' => ['G3', 'B3', 'E4'],
            'second' => ['B3', 'E4', 'G4']
        ],
        'F' => [
            'root' => ['F4', 'Ab4', 'C5'],
            'first' => ['Ab3', 'C4', 'F4'],
            'second' => ['C4', 'F4', 'Ab4']
        ],
        'F#' => [
            'root' => ['F#3', 'A3', 'C#4'],
            'first' => ['A3', 'C#4', 'F#4'],
            'second' => ['C#4', 'F#4', 'A4']
        ],
        'G' => [
            'root' => ['G3', 'Bb3', 'D4'],
            'first' => ['Bb3', 'D4', 'G4'],
            'second' => ['D4', 'G4', 'Bb4']
        ],
        'G#' => [
            'root' => ['G#3', 'B3', 'D#4'],
            'first' => ['B3', 'D#4', 'G#4'],
            'second' => ['D#4', 'G#4', 'B4']
        ],
        'A' => [
            'root' => ['A3', 'C4', 'E4'],
            'first' => ['C4', 'E4', 'A4'],
            'second' => ['E3', 'A3', 'C4']
        ],
        'A#' => [
            'root' => ['A#3', 'C#4', 'F4'],
            'first' => ['C#4', 'F4', 'A#4'],
            'second' => ['F3', 'A#3', 'C#4']
        ],
        'B' => [
            'root' => ['B3', 'D4', 'F#4'],
            'first' => ['D4', 'F#4', 'B4'],
            'second' => ['F#3', 'B3', 'D4']
        ]
    ];

    /**
     * Test all minor chords in root position
     */
    public function test_all_minor_chords_root_position(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000); // Wait for page and audio to load

            foreach ($this->minorChordVoicings as $tone => $voicings) {
                $this->testMinorChord($browser, $tone, 'root', $voicings['root']);
            }
        });
    }

    /**
     * Test all minor chords in first inversion
     */
    public function test_all_minor_chords_first_inversion(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->minorChordVoicings as $tone => $voicings) {
                $this->testMinorChord($browser, $tone, 'first', $voicings['first']);
            }
        });
    }

    /**
     * Test all minor chords in second inversion
     */
    public function test_all_minor_chords_second_inversion(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->minorChordVoicings as $tone => $voicings) {
                $this->testMinorChord($browser, $tone, 'second', $voicings['second']);
            }
        });
    }

    /**
     * Test E minor specifically (the chord mentioned in the issue)
     */
    public function test_e_minor_all_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Test Em root position
            $this->testMinorChord($browser, 'E', 'root', ['E4', 'G4', 'B4']);
            
            // Test Em first inversion
            $this->testMinorChord($browser, 'E', 'first', ['G3', 'B3', 'E4']);
            
            // Test Em second inversion
            $this->testMinorChord($browser, 'E', 'second', ['B3', 'E4', 'G4']);
        });
    }

    /**
     * Test chord sustain timing for minor chords
     */
    public function test_minor_chord_sustain_timing(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Set Am chord
            $this->setChordInGrid($browser, 1, 'A', 'minor', 'root');
            
            // Click to play
            $browser->click('[data-chord-position="1"]')
                ->pause(100);

            // Verify keys are active
            $browser->assertPresent('#key-A3.active')
                ->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active');

            // Wait 1.4 seconds - should still be active
            $browser->pause(1400);
            $browser->assertPresent('#key-A3.active')
                ->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active');

            // Wait another 200ms (total 1.6s) - should be inactive
            $browser->pause(200);
            $browser->assertMissing('#key-A3.active')
                ->assertMissing('#key-C4.active')
                ->assertMissing('#key-E4.active');
        });
    }

    /**
     * Test rapid chord changes between minor chords
     */
    public function test_rapid_minor_chord_changes(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Set up two different minor chords
            $this->setChordInGrid($browser, 1, 'A', 'minor', 'root');
            $this->setChordInGrid($browser, 2, 'D', 'minor', 'root');

            // Play first chord
            $browser->click('[data-chord-position="1"]')
                ->pause(200);

            // Verify Am keys
            $browser->assertPresent('#key-A3.active')
                ->assertPresent('#key-C4.active')
                ->assertPresent('#key-E4.active');

            // Quickly play second chord
            $browser->click('[data-chord-position="2"]')
                ->pause(100);

            // Previous chord should be cleared
            $browser->assertMissing('#key-A3.active')
                ->assertMissing('#key-C4.active')
                ->assertMissing('#key-E4.active');

            // New chord should be active
            $browser->assertPresent('#key-D4.active')
                ->assertPresent('#key-F4.active')
                ->assertPresent('#key-A4.active');
        });
    }

    /**
     * Helper method to test a specific minor chord
     */
    private function testMinorChord(Browser $browser, string $tone, string $inversion, array $expectedNotes): void
    {
        // Set the chord in the grid
        $this->setChordInGrid($browser, 1, $tone, 'minor', $inversion);
        
        // Click the chord to play it
        $browser->click('[data-chord-position="1"]')
            ->pause(200); // Wait for chord to start playing

        // Verify all expected keys are active
        foreach ($expectedNotes as $note) {
            // Convert flats to sharps for piano key lookup
            $pianoNote = $this->convertFlatToSharp($note);
            $browser->assertPresent("#key-{$pianoNote}")
                ->assertHasClass("#key-{$pianoNote}", 'active');
        }

        // Verify no unexpected keys are active
        $this->verifyOnlyExpectedKeysActive($browser, $expectedNotes);

        // Wait for chord to finish
        $browser->pause(1400);

        // Verify keys are no longer active after sustain
        foreach ($expectedNotes as $note) {
            $pianoNote = $this->convertFlatToSharp($note);
            $browser->assertMissing("#key-{$pianoNote}.active");
        }
        
        echo "✓ Tested {$tone}m ({$inversion} inversion): " . implode(', ', $expectedNotes) . "\n";
    }

    /**
     * Helper method to set a chord in the grid
     */
    private function setChordInGrid(Browser $browser, int $position, string $tone, string $semitone, string $inversion): void
    {
        // Click the chord position to select it
        $browser->click("[data-chord-position=\"{$position}\"]");
        
        // Set the tone
        $browser->click("button[wire\\:click=\"setChord('{$tone}')\"]");
        
        // Set to minor
        $browser->click("button[wire\\:click=\"setChord('{$tone}', 'minor')\"]");
        
        // Set the inversion
        if ($inversion !== 'root') {
            $inversionButton = match($inversion) {
                'first' => 'I',
                'second' => 'II',
                default => 'R'
            };
            
            // Find and click the inversion button for this specific chord position
            $selector = "[data-chord-position=\"{$position}\"] button:contains('{$inversionButton}')";
            $browser->click($selector);
        }
        
        $browser->pause(500); // Wait for changes to propagate
    }

    /**
     * Verify only expected keys are active
     */
    private function verifyOnlyExpectedKeysActive(Browser $browser, array $expectedNotes): void
    {
        // Get a sample of notes that should NOT be active
        $allNotes = ['C3', 'D3', 'E3', 'F3', 'G3', 'A3', 'B3',
                     'C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4',
                     'C5', 'D5', 'E5', 'F5', 'G5'];
        
        // Convert expected notes to piano key format
        $expectedNoteIds = array_map(function($note) {
            return $this->convertFlatToSharp($note);
        }, $expectedNotes);
        
        // Check a few notes that should NOT be active
        $unexpectedNotes = array_diff($allNotes, $expectedNoteIds);
        $sampleUnexpected = array_slice($unexpectedNotes, 0, 5);
        
        foreach ($sampleUnexpected as $note) {
            if ($browser->element("#key-{$note}")) {
                $browser->assertMissing("#key-{$note}.active");
            }
        }
    }

    /**
     * Convert flat notes to sharp equivalents for piano key lookup
     */
    private function convertFlatToSharp(string $note): string
    {
        return str_replace(
            ['Db', 'Eb', 'Gb', 'Ab', 'Bb'],
            ['C#', 'D#', 'F#', 'G#', 'A#'],
            $note
        );
    }
}