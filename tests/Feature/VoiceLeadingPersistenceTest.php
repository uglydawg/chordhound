<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceLeadingPersistenceTest extends TestCase
{
    /**
     * Test that voice leading is off by default
     */
    public function test_voice_leading_off_by_default(): void
    {
        // Clear session
        session()->forget('chord_grid');

        Livewire::test(ChordGrid::class)
            ->assertSet('showVoiceLeading', false);
    }

    /**
     * Test that voice leading toggle persists across sessions
     */
    public function test_voice_leading_toggle_persists(): void
    {
        // Enable voice leading
        Livewire::test(ChordGrid::class)
            ->call('toggleVoiceLeading')
            ->assertSet('showVoiceLeading', true);

        // New instance should remember the toggle state
        Livewire::test(ChordGrid::class)
            ->assertSet('showVoiceLeading', true);

        // Disable and test again
        Livewire::test(ChordGrid::class)
            ->call('toggleVoiceLeading')
            ->assertSet('showVoiceLeading', false);

        // Should remember disabled state
        Livewire::test(ChordGrid::class)
            ->assertSet('showVoiceLeading', false);
    }

    /**
     * Test that preset progressions always apply recommended inversions
     */
    public function test_preset_progressions_apply_inversions(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V');

        // Preset progressions should always use recommended inversions
        // regardless of voice leading toggle state
        $component->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'second')
            ->assertSet('chords.3.inversion', 'first');

        // Verify voice leading is still off by default
        $component->assertSet('showVoiceLeading', false);
    }
}