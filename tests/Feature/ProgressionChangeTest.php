<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class ProgressionChangeTest extends TestCase
{
    /**
     * Test that progression changes update chords
     */
    public function test_progression_change_updates_chords(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V');

        // Should have C-F-G progression
        $component->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.4.tone', ''); // 4th should be empty for 3-chord progression

        // Change to different progression
        $component->call('setProgression', 'I-vi-IV-V');

        // Should now have C-Am-F-G progression
        $component->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.2.tone', 'A')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.3.tone', 'F')
            ->assertSet('chords.4.tone', 'G');
    }

    /**
     * Test that key changes update chords for current progression
     */
    public function test_key_change_updates_progression_chords(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V');

        // Initial C major progression
        $component->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.3.tone', 'G');

        // Change key to G
        $component->call('setKey', 'G');

        // Should now be G major progression
        $component->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.2.tone', 'C')
            ->assertSet('chords.3.tone', 'D');
    }

    /**
     * Test clearing progression
     */
    public function test_clearing_progression_empties_chords(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'D')
            ->call('setProgression', 'I-vi-IV-V');

        // Should have chords
        $component->assertSet('chords.1.tone', 'D')
            ->assertSet('chords.2.tone', 'B')
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.4.tone', 'A');

        // Clear progression
        $component->call('setProgression', '');

        // Should be empty
        $component->assertSet('chords.1.tone', '')
            ->assertSet('chords.2.tone', '')
            ->assertSet('chords.3.tone', '')
            ->assertSet('chords.4.tone', '');
    }
}