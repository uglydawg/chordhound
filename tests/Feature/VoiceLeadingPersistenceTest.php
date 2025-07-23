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
     * Test that enabling voice leading applies inversions
     */
    public function test_enabling_voice_leading_applies_inversions(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V');

        // Initially voice leading is off, so all inversions should be root
        $component->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'root')
            ->assertSet('chords.3.inversion', 'root');

        // Enable voice leading
        $component->call('toggleVoiceLeading')
            ->assertSet('showVoiceLeading', true);

        // Should re-apply progression with voice-leading inversions
        $component->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'second')
            ->assertSet('chords.3.inversion', 'first');
    }
}