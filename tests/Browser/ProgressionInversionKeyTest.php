<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProgressionInversionKeyTest extends DuskTestCase
{
    use DatabaseMigrations;

    private array $testKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
    
    private array $progressions = [
        'I-IV-V' => 'Classic Rock/Blues',
        'I-vi-IV-V' => '50s Doo-Wop', 
        'vi-IV-I-V' => 'Alternative Pop',
        'I-vi-ii-V' => 'Jazz Standard',
        'ii-V-I' => 'Jazz Cadence'
    ];

    /**
     * Test I-IV-V progression inversions in all keys
     */
    public function test_i_iv_v_progression_inversions_across_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->testKeys as $key) {
                $this->testProgressionInKey($browser, 'I-IV-V', $key, [
                    1 => 'root',    // I
                    2 => 'second',  // IV 
                    3 => 'first'    // V
                ]);
            }
        });
    }

    /**
     * Test I-vi-IV-V progression inversions in all keys
     */
    public function test_i_vi_iv_v_progression_inversions_across_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->testKeys as $key) {
                $this->testProgressionInKey($browser, 'I-vi-IV-V', $key, [
                    1 => 'root',    // I
                    2 => 'first',   // vi
                    3 => 'second',  // IV
                    4 => 'first'    // V
                ]);
            }
        });
    }

    /**
     * Test vi-IV-I-V progression inversions in all keys
     */
    public function test_vi_iv_i_v_progression_inversions_across_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->testKeys as $key) {
                $this->testProgressionInKey($browser, 'vi-IV-I-V', $key, [
                    1 => 'root',    // vi
                    2 => 'first',   // IV
                    3 => 'second',  // I
                    4 => 'root'     // V
                ]);
            }
        });
    }

    /**
     * Test I-vi-ii-V progression inversions in all keys
     */
    public function test_i_vi_ii_v_progression_inversions_across_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->testKeys as $key) {
                $this->testProgressionInKey($browser, 'I-vi-ii-V', $key, [
                    1 => 'root',    // I
                    2 => 'first',   // vi
                    3 => 'root',    // ii
                    4 => 'first'    // V
                ]);
            }
        });
    }

    /**
     * Test ii-V-I progression inversions in all keys
     */
    public function test_ii_v_i_progression_inversions_across_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            foreach ($this->testKeys as $key) {
                $this->testProgressionInKey($browser, 'ii-V-I', $key, [
                    1 => 'second',  // ii
                    2 => 'first',   // V
                    3 => 'root'     // I
                ]);
            }
        });
    }

    /**
     * Test key changes maintain progression inversions
     */
    public function test_key_changes_maintain_progression_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Set initial progression
            $browser->select('select[wire\\:change="setProgression($event.target.value)"]', 'I-IV-V')
                ->pause(1000);

            foreach ($this->testKeys as $key) {
                // Change key
                $browser->click("button[wire\\:click=\"setKey('{$key}')\"]")
                    ->pause(1000);

                // Verify inversions are maintained
                $this->verifyChordInversions($browser, [
                    1 => 'root',
                    2 => 'second', 
                    3 => 'first'
                ]);

                // Test each chord plays with correct notes
                for ($position = 1; $position <= 3; $position++) {
                    $browser->click("[data-chord-position=\"{$position}\"]")
                        ->pause(200);

                    // Verify some keys are active (basic check)
                    $browser->assertPresent('.piano-key.active');
                    
                    // Wait for chord to finish
                    $browser->pause(1400);
                    
                    // Verify keys are no longer active
                    $browser->assertMissing('.piano-key.active');
                }
            }
        });
    }

    /**
     * Test progression changes update inversions correctly
     */
    public function test_progression_changes_update_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Set key to C for consistent testing
            $browser->click("button[wire\\:click=\"setKey('C')\"]")
                ->pause(500);

            foreach ($this->progressions as $progression => $description) {
                // Select progression
                $browser->select('select[wire\\:change="setProgression($event.target.value)"]', $progression)
                    ->pause(1000);

                // Get expected inversions for this progression
                $expectedInversions = $this->getExpectedInversions($progression);

                // Verify inversions are set correctly
                $this->verifyChordInversions($browser, $expectedInversions);

                // Test each chord position plays correctly
                foreach ($expectedInversions as $position => $expectedInversion) {
                    $browser->click("[data-chord-position=\"{$position}\"]")
                        ->pause(200);

                    // Verify chord plays (keys become active)
                    $browser->assertPresent('.piano-key.active');
                    
                    // Test sustain duration (1.5 seconds)
                    $browser->pause(1400); // Just before 1.5s
                    $browser->assertPresent('.piano-key.active');
                    
                    $browser->pause(200); // Complete 1.6s total
                    $browser->assertMissing('.piano-key.active');
                }
                
                echo "✓ Tested progression: {$progression} in C major\n";
            }
        });
    }

    /**
     * Test major/minor key type changes
     */
    public function test_major_minor_key_type_changes(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            
            $browser->loginAs($user)
                ->visit('/chords')
                ->pause(2000);

            // Test with I-vi-IV-V progression
            $browser->select('select[wire\\:change="setProgression($event.target.value)"]', 'I-vi-IV-V')
                ->pause(1000);

            foreach (['C', 'G', 'F'] as $key) {
                $browser->click("button[wire\\:click=\"setKey('{$key}')\"]")
                    ->pause(500);

                // Test major key
                $browser->click("button[wire\\:click=\"setKeyType('major')\"]")
                    ->pause(1000);

                // Test first chord (should be major)
                $browser->click('[data-chord-position="1"]')
                    ->pause(200);
                $browser->assertPresent('.piano-key.active');
                $browser->pause(1400);
                $browser->assertMissing('.piano-key.active');

                // Test minor key
                $browser->click("button[wire\\:click=\"setKeyType('minor')\"]")
                    ->pause(1000);

                // Test first chord (should be minor now)
                $browser->click('[data-chord-position="1"]')
                    ->pause(200);
                $browser->assertPresent('.piano-key.active');
                $browser->pause(1400);
                $browser->assertMissing('.piano-key.active');
                
                echo "✓ Tested {$key} major/minor key type changes\n";
            }
        });
    }

    /**
     * Helper method to test a progression in a specific key
     */
    private function testProgressionInKey(Browser $browser, string $progression, string $key, array $expectedInversions): void
    {
        // Set the key
        $browser->click("button[wire\\:click=\"setKey('{$key}')\"]")
            ->pause(500);

        // Set the progression
        $browser->select('select[wire\\:change="setProgression($event.target.value)"]', $progression)
            ->pause(1000);

        // Verify inversions are set correctly
        $this->verifyChordInversions($browser, $expectedInversions);

        // Test each chord plays with correct timing
        foreach ($expectedInversions as $position => $expectedInversion) {
            $browser->click("[data-chord-position=\"{$position}\"]")
                ->pause(200);

            // Verify chord plays
            $browser->assertPresent('.piano-key.active');
            
            // Test sustain timing
            $browser->pause(1400); // 1.4s - should still be active
            $browser->assertPresent('.piano-key.active');
            
            $browser->pause(200); // Total 1.6s - should be inactive
            $browser->assertMissing('.piano-key.active');
        }
        
        echo "✓ Tested {$progression} in {$key} major\n";
    }

    /**
     * Helper method to verify chord inversions in the UI
     */
    private function verifyChordInversions(Browser $browser, array $expectedInversions): void
    {
        foreach ($expectedInversions as $position => $expectedInversion) {
            $inversionText = match($expectedInversion) {
                'root' => 'Root Inversion',
                'first' => 'First Inversion', 
                'second' => 'Second Inversion'
            };
            
            $browser->assertSeeIn("[data-chord-position=\"{$position}\"]", $inversionText);
        }
    }

    /**
     * Get expected inversions for a progression
     */
    private function getExpectedInversions(string $progression): array
    {
        return match($progression) {
            'I-IV-V' => [1 => 'root', 2 => 'second', 3 => 'first'],
            'I-vi-IV-V' => [1 => 'root', 2 => 'first', 3 => 'second', 4 => 'first'],
            'vi-IV-I-V' => [1 => 'root', 2 => 'first', 3 => 'second', 4 => 'root'],
            'I-vi-ii-V' => [1 => 'root', 2 => 'first', 3 => 'root', 4 => 'first'],
            'ii-V-I' => [1 => 'second', 2 => 'first', 3 => 'root'],
            default => [1 => 'root', 2 => 'root', 3 => 'root', 4 => 'root']
        };
    }
}