<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class PopProgressionInversionsTest extends TestCase
{
    /**
     * Test I-vi-IV-V progression with voice leading enabled
     */
    public function test_pop_progression_with_voice_leading(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-vi-IV-V');

        // Verify chords are correct
        $component->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.1.semitone', 'major')
            ->assertSet('chords.2.tone', 'A')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.3.tone', 'F')
            ->assertSet('chords.3.semitone', 'major')
            ->assertSet('chords.4.tone', 'G')
            ->assertSet('chords.4.semitone', 'major');

        // Verify inversions match the table
        $component->assertSet('chords.1.inversion', 'root')   // I - root position
            ->assertSet('chords.2.inversion', 'first')  // vi - first inversion
            ->assertSet('chords.3.inversion', 'second') // IV - second inversion
            ->assertSet('chords.4.inversion', 'first'); // V - first inversion
    }

    /**
     * Test I-vi-IV-V progression without voice leading
     */
    public function test_pop_progression_without_voice_leading(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'G')
            ->set('showVoiceLeading', false)
            ->call('setProgression', 'I-vi-IV-V');

        // Verify chords in G major
        $component->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.2.tone', 'E')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.3.tone', 'C')
            ->assertSet('chords.4.tone', 'D');

        // All inversions should be root when voice leading is off
        $component->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'root')
            ->assertSet('chords.3.inversion', 'root')
            ->assertSet('chords.4.inversion', 'root');
    }

    /**
     * Test toggling voice leading updates inversions
     */
    public function test_toggling_voice_leading_updates_inversions(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'F')
            ->set('showVoiceLeading', false)
            ->call('setProgression', 'I-vi-IV-V');

        // Initially all root position
        $component->assertSet('chords.2.inversion', 'root')
            ->assertSet('chords.3.inversion', 'root');

        // Enable voice leading
        $component->call('toggleVoiceLeading');

        // Should update to proper inversions
        $component->assertSet('showVoiceLeading', true)
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'first')
            ->assertSet('chords.3.inversion', 'second')
            ->assertSet('chords.4.inversion', 'first');

        // Verify the chords in F major
        $component->assertSet('chords.1.tone', 'F')
            ->assertSet('chords.2.tone', 'D')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.3.tone', 'A#')
            ->assertSet('chords.4.tone', 'C');
    }
}