<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BassChordPatternTest extends DuskTestCase
{
    /**
     * Test the new bass-chord pattern with C2-C5 piano range
     */
    public function test_bass_chord_pattern_with_c2_c5_range(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10)
                ->assertSee('ChordHound');

            // Verify piano keyboard shows C2-C5 range
            $browser->assertPresent('[data-note="C2"]')
                ->assertPresent('[data-note="C3"]') 
                ->assertPresent('[data-note="C4"]')
                ->assertPresent('[data-note="C5"]')
                ->assertMissing('[data-note="C1"]') // Should not show C1
                ->assertMissing('[data-note="C6"]'); // Should not show C6

            // Set up a simple chord progression
            $browser->click('[data-position="0"]') // First chord slot
                ->waitFor('.chord-selector')
                ->click('[data-tone="C"]') // C major chord
                ->click('.chord-selector .close-button');

            // Start playback
            $browser->click('.transport-button')
                ->wait(100); // Brief pause for initialization

            // Test Beat 1: Should play bass note in C2
            $this->verifyBeatPattern($browser, 1, 'C2');

            // Wait for Beat 2: Should play bass note in C3  
            $browser->pause(500); // Wait for next beat (assuming 120 BPM = 500ms per beat)
            $this->verifyBeatPattern($browser, 2, 'C3');

            // Wait for Beat 3: Should play chord in C4
            $browser->pause(500);
            $this->verifyChordInC4($browser, 3);

            // Wait for Beat 4: Chord should continue (sostenuto)
            $browser->pause(500);
            $this->verifyChordContinues($browser, 4);

            // Stop playback
            $browser->click('.transport-button');
            
            // Verify no keys are active after stopping
            $browser->assertMissing('.piano-key.active');
        });
    }

    /**
     * Test that single chord clicks also use the new pattern
     */
    public function test_single_chord_click_uses_correct_octave(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Click on a chord in the grid to play it
            $browser->click('[data-position="0"]')
                ->click('[data-tone="F"]') // F major chord
                ->click('.chord-selector .close-button')
                ->click('[data-position="0"]'); // Click the chord to play it

            $browser->pause(100); // Wait for audio to start

            // Should show F chord in C4 octave range (F4, A4, C5)
            $browser->waitFor('.piano-key[data-note="F4"].active', 2)
                ->assertPresent('.piano-key[data-note="A4"].active')
                ->assertPresent('.piano-key[data-note="C5"].active');
        });
    }

    /**
     * Test tempo changes affect the bass-chord pattern timing
     */
    public function test_tempo_affects_bass_chord_timing(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Set up chord and change tempo to slower (60 BPM)
            $browser->click('[data-position="0"]')
                ->click('[data-tone="G"]')
                ->click('.chord-selector .close-button');

            // Change tempo if tempo control exists
            if ($browser->element('.tempo-control')) {
                $browser->type('.tempo-control', '60');
            }

            $startTime = microtime(true);
            
            // Start playback
            $browser->click('.transport-button')
                ->wait(100);

            // Verify G2 bass note appears first
            $browser->waitFor('.piano-key[data-note="G2"].active', 2);
            
            $firstBeatTime = microtime(true);

            // Wait for second beat (should take ~1 second at 60 BPM)
            $browser->pause(950); // Slightly less than 1 second to account for processing
            
            // Verify G3 bass note appears
            $browser->waitFor('.piano-key[data-note="G3"].active', 1);
            
            $secondBeatTime = microtime(true);
            
            // Verify timing is approximately 1 second between beats (60 BPM)
            $beatDuration = $secondBeatTime - $firstBeatTime;
            $this->assertTrue($beatDuration > 0.8 && $beatDuration < 1.2, 
                "Beat duration should be ~1 second at 60 BPM, got: " . $beatDuration);

            $browser->click('.transport-button'); // Stop playback
        });
    }

    /**
     * Verify specific beat pattern shows correct note
     */
    private function verifyBeatPattern(Browser $browser, int $beat, string $expectedNote): void
    {
        $browser->waitFor(".piano-key[data-note=\"{$expectedNote}\"].active", 1)
            ->assertPresent(".piano-key[data-note=\"{$expectedNote}\"].active");
        
        // Log for debugging
        $browser->script("console.log('Beat {$beat}: Verified {$expectedNote} is active')");
    }

    /**
     * Verify chord plays in C4 octave
     */
    private function verifyChordInC4(Browser $browser, int $beat): void
    {
        // For C major chord, should see C4, E4, G4
        $browser->waitFor('.piano-key[data-note="C4"].active', 1)
            ->assertPresent('.piano-key[data-note="E4"].active')
            ->assertPresent('.piano-key[data-note="G4"].active');
            
        $browser->script("console.log('Beat {$beat}: Verified C4 chord is active')");
    }

    /**
     * Verify chord continues playing (sostenuto)
     */
    private function verifyChordContinues(Browser $browser, int $beat): void
    {
        // Chord should still be visible/active
        $browser->assertPresent('.piano-key[data-note="C4"].active')
            ->assertPresent('.piano-key[data-note="E4"].active') 
            ->assertPresent('.piano-key[data-note="G4"].active');
            
        $browser->script("console.log('Beat {$beat}: Verified chord continues (sostenuto)')");
    }

    /**
     * Test multiple chords in sequence
     */
    public function test_chord_progression_bass_pattern(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Set up a simple progression: C - F - G - C
            $browser->click('[data-position="0"]')
                ->click('[data-tone="C"]')
                ->click('.chord-selector .close-button');
                
            $browser->click('[data-position="1"]')
                ->click('[data-tone="F"]')
                ->click('.chord-selector .close-button');

            // Start playback
            $browser->click('.transport-button')
                ->wait(100);

            // First chord: C major
            // Beat 1: C2
            $browser->waitFor('.piano-key[data-note="C2"].active', 2);
            
            $browser->pause(400); // Beat 2: C3
            $browser->waitFor('.piano-key[data-note="C3"].active', 1);
            
            $browser->pause(400); // Beat 3: C4 chord
            $browser->waitFor('.piano-key[data-note="C4"].active', 1)
                ->assertPresent('.piano-key[data-note="E4"].active');

            // Wait for chord change (4 beats total per chord)
            $browser->pause(800); // Beat 4 + transition
            
            // Second chord: F major
            // Beat 1 of new chord: F2
            $browser->waitFor('.piano-key[data-note="F2"].active', 2);

            $browser->click('.transport-button'); // Stop playback
        });
    }
}