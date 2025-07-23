<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class ChordProgressionDefaultTest extends TestCase
{
    /**
     * Test that default progression is applied on load
     */
    public function test_default_progression_is_applied_on_load(): void
    {
        Livewire::test(ChordGrid::class)
            ->assertSet('selectedProgression', 'I-V-vi-IV') // Should be set from session/default
            ->assertSet('selectedKey', 'C')
            // Verify chords are set (not empty strings)
            ->assertNotSet('chords.1.tone', '')
            ->assertNotSet('chords.2.tone', '')
            ->assertNotSet('chords.3.tone', '')
            ->assertNotSet('chords.4.tone', '');
    }

    /**
     * Test that chords are correctly set for default progression
     */
    public function test_default_progression_chords_are_correct(): void
    {
        Livewire::test(ChordGrid::class)
            ->assertSet('selectedKey', 'C')
            ->assertSet('selectedProgression', 'I-V-vi-IV')
            // In C major: I-V-vi-IV = C-G-Am-F
            ->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.1.semitone', 'major')
            ->assertSet('chords.2.tone', 'G')
            ->assertSet('chords.2.semitone', 'major')
            ->assertSet('chords.3.tone', 'A')
            ->assertSet('chords.3.semitone', 'minor')
            ->assertSet('chords.4.tone', 'F')
            ->assertSet('chords.4.semitone', 'major');
    }

    /**
     * Test changing progression updates chords
     */
    public function test_changing_progression_updates_chords(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'G')
            ->call('setProgression', 'I-IV-V')
            ->assertSet('selectedProgression', 'I-IV-V')
            // In G major: I-IV-V = G-C-D
            ->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.2.tone', 'C')
            ->assertSet('chords.3.tone', 'D')
            ->assertSet('chords.4.tone', ''); // 4th chord should be empty for 3-chord progression
    }

    /**
     * Test empty/custom progression
     */
    public function test_custom_progression_clears_chords(): void
    {
        Livewire::test(ChordGrid::class)
            ->call('setProgression', 'I-V-vi-IV') // Set a progression first
            ->assertNotSet('chords.1.tone', '') // Should have a value
            ->call('setProgression', '') // Clear to custom
            ->assertSet('selectedProgression', '')
            ->assertSet('chords.1.tone', '')
            ->assertSet('chords.2.tone', '')
            ->assertSet('chords.3.tone', '')
            ->assertSet('chords.4.tone', '');
    }
}