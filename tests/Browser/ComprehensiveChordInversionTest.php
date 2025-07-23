<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ComprehensiveChordInversionTest extends DuskTestCase
{
    use DatabaseMigrations;

    private array $allTones = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    
    private array $chordTypes = [
        'major' => 'major',
        'minor' => 'minor', 
        'diminished' => 'diminished',
        'augmented' => 'augmented'
    ];
    
    private array $inversions = ['root', 'first', 'second'];

    /**
     * Test all major chords in all inversions
     */
    public function test_all_major_chords_activate_correct_piano_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000); // Wait for page to load completely

            foreach ($this->allTones as $tone) {
                foreach ($this->inversions as $inversion) {
                    $this->testChordPlayback($browser, $tone, 'major', $inversion);
                }
            }
        });
    }

    /**
     * Test all minor chords in all inversions
     */
    public function test_all_minor_chords_activate_correct_piano_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->allTones as $tone) {
                foreach ($this->inversions as $inversion) {
                    $this->testChordPlayback($browser, $tone, 'minor', $inversion);
                }
            }
        });
    }

    /**
     * Test all diminished chords in all inversions
     */
    public function test_all_diminished_chords_activate_correct_piano_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->allTones as $tone) {
                foreach ($this->inversions as $inversion) {
                    $this->testChordPlayback($browser, $tone, 'diminished', $inversion);
                }
            }
        });
    }

    /**
     * Test all augmented chords in all inversions
     */
    public function test_all_augmented_chords_activate_correct_piano_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->allTones as $tone) {
                foreach ($this->inversions as $inversion) {
                    $this->testChordPlayback($browser, $tone, 'augmented', $inversion);
                }
            }
        });
    }

    /**
     * Test chord sustain timing (1.5 seconds)
     */
    public function test_chord_sustain_duration(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Test with C major chord
            $this->setChordInGrid($browser, 1, 'C', 'major', 'root');
            
            // Click the chord to play it
            $browser->click('[data-chord-position="1"]')
                ->pause(100); // Brief pause for chord to start

            // Verify keys are active immediately after clicking
            $expectedNotes = $this->getExpectedNotes('C', 'major', 'root');
            foreach ($expectedNotes as $note) {
                $browser->assertHasClass("#key-{$note}", 'active');
            }

            // Wait 1.4 seconds (just before 1.5s) - keys should still be active
            $browser->pause(1400);
            foreach ($expectedNotes as $note) {
                $browser->assertHasClass("#key-{$note}", 'active');
            }

            // Wait another 200ms (total 1.6s) - keys should no longer be active
            $browser->pause(200);
            foreach ($expectedNotes as $note) {
                $browser->assertMissing("#key-{$note}.active");
            }
        });
    }

    /**
     * Test rapid chord changes clear previous keys
     */
    public function test_rapid_chord_changes_clear_previous_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Set up two different chords
            $this->setChordInGrid($browser, 1, 'C', 'major', 'root');
            $this->setChordInGrid($browser, 2, 'G', 'major', 'root');

            // Play first chord
            $browser->click('[data-chord-position="1"]')
                ->pause(200);

            $cMajorNotes = $this->getExpectedNotes('C', 'major', 'root');
            foreach ($cMajorNotes as $note) {
                $browser->assertHasClass("#key-{$note}", 'active');
            }

            // Quickly play second chord
            $browser->click('[data-chord-position="2"]')
                ->pause(100);

            // Previous chord keys should be cleared
            foreach ($cMajorNotes as $note) {
                $browser->assertMissing("#key-{$note}.active");
            }

            // New chord keys should be active
            $gMajorNotes = $this->getExpectedNotes('G', 'major', 'root');
            foreach ($gMajorNotes as $note) {
                $browser->assertHasClass("#key-{$note}", 'active');
            }
        });
    }

    /**
     * Helper method to test a specific chord playback
     */
    private function testChordPlayback(Browser $browser, string $tone, string $semitone, string $inversion): void
    {
        // Set the chord in the grid
        $this->setChordInGrid($browser, 1, $tone, $semitone, $inversion);
        
        // Click the chord to play it
        $browser->click('[data-chord-position="1"]')
            ->pause(200); // Wait for chord to start playing

        // Get expected notes for this chord and inversion
        $expectedNotes = $this->getExpectedNotes($tone, $semitone, $inversion);
        
        // Verify all expected keys are active
        foreach ($expectedNotes as $note) {
            $browser->assertPresent("#key-{$note}")
                ->assertHasClass("#key-{$note}", 'active');
        }

        // Verify no unexpected keys are active (check a few common notes that shouldn't be)
        $allPossibleNotes = $this->getAllPossibleNotes();
        $unexpectedNotes = array_diff($allPossibleNotes, $expectedNotes);
        
        // Test a sample of unexpected notes to ensure they're not active
        $sampleUnexpected = array_slice($unexpectedNotes, 0, 5);
        foreach ($sampleUnexpected as $note) {
            if ($browser->element("#key-{$note}")) {
                $browser->assertMissing("#key-{$note}.active");
            }
        }

        // Wait for chord to finish (1.5 seconds total)
        $browser->pause(1400); // Additional wait to complete 1.5s total

        // Verify keys are no longer active after sustain period
        foreach ($expectedNotes as $note) {
            $browser->assertMissing("#key-{$note}.active");
        }
        
        echo "✓ Tested {$tone} {$semitone} ({$inversion} inversion): " . implode(', ', $expectedNotes) . "\n";
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
        
        // Set the chord type if not major
        if ($semitone !== 'major') {
            $browser->click("button[wire\\:click=\"setChord('{$tone}', '{$semitone}')\"]");
        }
        
        // Set the inversion
        if ($inversion !== 'root') {
            $inversionButton = match($inversion) {
                'first' => 'I',
                'second' => 'II',
                default => 'R'
            };
            $browser->click("button:contains('{$inversionButton}')");
        }
        
        $browser->pause(500); // Wait for changes to propagate
    }

    /**
     * Get expected notes for a chord and inversion
     */
    private function getExpectedNotes(string $tone, string $semitone, string $inversion): array
    {
        // Base chord mappings (root position in octave 4)
        $baseChords = [
            'C' => ['C4', 'E4', 'G4'],
            'C#' => ['C#4', 'F4', 'G#4'],
            'D' => ['D4', 'F#4', 'A4'],
            'D#' => ['D#4', 'G4', 'A#4'],
            'E' => ['E4', 'G#4', 'B4'],
            'F' => ['F4', 'A4', 'C5'],
            'F#' => ['F#4', 'A#4', 'C#5'],
            'G' => ['G4', 'B4', 'D5'],
            'G#' => ['G#4', 'C5', 'D#5'],
            'A' => ['A4', 'C#5', 'E5'],
            'A#' => ['A#4', 'D5', 'F5'],
            'B' => ['B4', 'D#5', 'F#5']
        ];

        $notes = $baseChords[$tone] ?? ['C4', 'E4', 'G4'];

        // Apply chord type modifications
        switch ($semitone) {
            case 'minor':
                $notes[1] = $this->lowerNote($notes[1]); // Lower the third
                break;
            case 'diminished':
                $notes[1] = $this->lowerNote($notes[1]); // Lower the third
                $notes[2] = $this->lowerNote($notes[2]); // Lower the fifth
                break;
            case 'augmented':
                $notes[2] = $this->raiseNote($notes[2]); // Raise the fifth
                break;
        }

        // Apply inversion
        switch ($inversion) {
            case 'first':
                // Move root to top with higher octave
                $root = array_shift($notes);
                $notes[] = $this->raiseOctave($root);
                break;
            case 'second':
                // Move root and third to top with higher octaves
                $root = array_shift($notes);
                $third = array_shift($notes);
                $notes[] = $this->raiseOctave($root);
                $notes[] = $this->raiseOctave($third);
                break;
        }

        return $notes;
    }

    /**
     * Get all possible note names for checking unexpected activations
     */
    private function getAllPossibleNotes(): array
    {
        $notes = [];
        $noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        
        for ($octave = 1; $octave <= 5; $octave++) {
            foreach ($noteNames as $noteName) {
                $notes[] = $noteName . $octave;
            }
        }
        
        return $notes;
    }

    /**
     * Lower a note by a semitone
     */
    private function lowerNote(string $note): string
    {
        $noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        
        if (preg_match('/([A-G]#?)(\d+)/', $note, $matches)) {
            $noteName = $matches[1];
            $octave = (int) $matches[2];
            
            $index = array_search($noteName, $noteOrder);
            $newIndex = ($index - 1 + 12) % 12;
            
            // Handle octave change when going from C to B
            if ($noteName === 'C' && $newIndex === 11) {
                $octave--;
            }
            
            return $noteOrder[$newIndex] . $octave;
        }
        
        return $note;
    }

    /**
     * Raise a note by a semitone
     */
    private function raiseNote(string $note): string
    {
        $noteOrder = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        
        if (preg_match('/([A-G]#?)(\d+)/', $note, $matches)) {
            $noteName = $matches[1];
            $octave = (int) $matches[2];
            
            $index = array_search($noteName, $noteOrder);
            $newIndex = ($index + 1) % 12;
            
            // Handle octave change when going from B to C
            if ($noteName === 'B' && $newIndex === 0) {
                $octave++;
            }
            
            return $noteOrder[$newIndex] . $octave;
        }
        
        return $note;
    }

    /**
     * Raise the octave of a note by 1
     */
    private function raiseOctave(string $note): string
    {
        return preg_replace('/(\d+)/', function($matches) {
            return (int) $matches[1] + 1;
        }, $note);
    }
}