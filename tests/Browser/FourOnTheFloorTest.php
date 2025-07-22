<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FourOnTheFloorTest extends DuskTestCase
{
    /**
     * Test that the Four on the Floor pattern plays C2 bass note, not C3
     */
    public function test_bass_note_is_c2_not_c3(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10)
                ->assertSee('ChordHound');

            // Start playback
            $browser->click('.transport-button')
                ->wait(200); // Wait for initialization and first beat

            // On beat 1, we should see C2 bass note active, NOT C3
            $browser->waitFor('.piano-key[data-note="C2"].active', 3)
                ->assertPresent('.piano-key[data-note="C2"].active');

            // Verify C3 is NOT active (this would indicate the bug)
            $browser->assertMissing('.piano-key[data-note="C3"].active');

            // Also verify that the chord notes in C4 range are active
            // For a C major chord: C4, E4, G4
            $browser->assertPresent('.piano-key[data-note="C4"].active')
                ->assertPresent('.piano-key[data-note="E4"].active') 
                ->assertPresent('.piano-key[data-note="G4"].active');

            // Wait for beat 2 (should still show C2 bass sustained + new chord hit)
            $browser->pause(500); // Wait for next beat at 120 BPM
            
            // C2 should still be active (sustaining)
            $browser->assertPresent('.piano-key[data-note="C2"].active');
            // C3 should still NOT be active
            $browser->assertMissing('.piano-key[data-note="C3"].active');

            // Stop playback
            $browser->click('.transport-button');

            echo "✅ Bass note confirmed as C2, not C3!\n";
        });
    }

    /**
     * Test the complete Four on the Floor pattern timing
     */
    public function test_four_on_the_floor_pattern(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Start playback 
            $browser->click('.transport-button')
                ->wait(200);

            // Measure 1, Beat 1: Should have C2 bass + C4 chord
            $browser->waitFor('.piano-key[data-note="C2"].active', 2);
            echo "✅ Measure 1, Beat 1: C2 bass active\n";
            
            // Wait for beats 2-4 of measure 1 (C2 should remain active)
            $browser->pause(1500); // 3 beats at 120 BPM
            $browser->assertPresent('.piano-key[data-note="C2"].active');
            echo "✅ Measure 1, Beats 2-4: C2 bass still sustained\n";

            // Wait for measure 2 to start (next chord in progression)
            $browser->pause(500); // Beat 1 of measure 2
            
            // Now we should see the next chord's bass note (depends on progression)
            // The exact note depends on what chord is in position 2
            // But we should see a bass note in the C2 octave range
            $bassNotes = ['C2', 'D2', 'E2', 'F2', 'G2', 'A2', 'B2'];
            $foundBassNote = false;
            
            foreach ($bassNotes as $bassNote) {
                if ($browser->element(".piano-key[data-note=\"$bassNote\"].active")) {
                    $foundBassNote = true;
                    echo "✅ Measure 2, Beat 1: Found bass note $bassNote active\n";
                    break;
                }
            }
            
            $this->assertTrue($foundBassNote, "Should find an active bass note in C2 octave range");

            // Stop playback
            $browser->click('.transport-button');

            echo "✅ Four on the Floor pattern timing confirmed!\n";
        });
    }

    /**
     * Test that no C3 notes are ever activated during playback
     */
    public function test_no_c3_notes_during_playback(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Start playback
            $browser->click('.transport-button')
                ->wait(100);

            // Check multiple times over several beats that C3 octave notes are not active
            $c3Notes = ['C3', 'D3', 'E3', 'F3', 'G3', 'A3', 'B3'];
            
            for ($i = 0; $i < 8; $i++) { // Check over 8 beats (2 measures)
                $browser->pause(250); // Quarter beat intervals
                
                foreach ($c3Notes as $c3Note) {
                    $browser->assertMissing(".piano-key[data-note=\"$c3Note\"].active");
                }
            }

            // Stop playback  
            $browser->click('.transport-button');

            echo "✅ No C3 octave notes found during playback - bass is correctly in C2!\n";
        });
    }

    /**
     * Debug test to log exactly which notes are active during playback
     */
    public function test_debug_active_notes_during_playback(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.piano-player', 10);

            // Start playback
            $browser->click('.transport-button')
                ->wait(300); // Wait for first beat

            // Get all active piano keys and log them
            $activeKeys = $browser->elements('.piano-key.active');
            $activeNotes = [];
            
            foreach ($activeKeys as $key) {
                $dataNote = $key->getAttribute('data-note');
                if ($dataNote) {
                    $activeNotes[] = $dataNote;
                }
            }

            echo "\n=== DEBUG: Active notes during playback ===\n";
            echo "Active notes found: " . implode(', ', $activeNotes) . "\n";
            
            // Check specifically for bass notes
            $bassNotesFound = array_filter($activeNotes, function($note) {
                return preg_match('/^[A-G]#?2$/', $note); // C2 octave
            });
            
            $c3NotesFound = array_filter($activeNotes, function($note) {
                return preg_match('/^[A-G]#?3$/', $note); // C3 octave  
            });

            $chordNotesFound = array_filter($activeNotes, function($note) {
                return preg_match('/^[A-G]#?4$/', $note); // C4 octave
            });

            echo "Bass notes (C2 octave): " . implode(', ', $bassNotesFound) . "\n";
            echo "C3 octave notes: " . implode(', ', $c3NotesFound) . "\n"; 
            echo "Chord notes (C4 octave): " . implode(', ', $chordNotesFound) . "\n";
            echo "=== END DEBUG ===\n\n";

            // Assertions
            $this->assertNotEmpty($bassNotesFound, "Should have bass notes in C2 octave");
            $this->assertEmpty($c3NotesFound, "Should NOT have any C3 octave notes"); 
            $this->assertNotEmpty($chordNotesFound, "Should have chord notes in C4 octave");

            // Stop playback
            $browser->click('.transport-button');
        });
    }
}