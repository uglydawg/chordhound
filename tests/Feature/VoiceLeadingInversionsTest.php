<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceLeadingInversionsTest extends TestCase
{
    /**
     * Test that I-IV-V progression applies correct inversions
     */
    public function test_i_iv_v_progression_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-IV-V')
            ->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.2.inversion', 'second')
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.3.inversion', 'first');
    }

    /**
     * Test that I-vi-IV-V progression applies correct inversions
     */
    public function test_i_vi_iv_v_progression_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'G')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-vi-IV-V')
            ->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.tone', 'E')
            ->assertSet('chords.2.inversion', 'first')
            ->assertSet('chords.3.tone', 'C')
            ->assertSet('chords.3.inversion', 'second')
            ->assertSet('chords.4.tone', 'D')
            ->assertSet('chords.4.inversion', 'first');
    }

    /**
     * Test that vi-IV-I-V progression applies correct inversions
     */
    public function test_vi_iv_i_v_progression_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'F')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'vi-IV-I-V')
            ->assertSet('chords.1.tone', 'D')
            ->assertSet('chords.1.semitone', 'minor')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.tone', 'A#')
            ->assertSet('chords.2.inversion', 'first')
            ->assertSet('chords.3.tone', 'F')
            ->assertSet('chords.3.inversion', 'second')
            ->assertSet('chords.4.tone', 'C')
            ->assertSet('chords.4.inversion', 'root');
    }

    /**
     * Test that I-vi-ii-V progression applies correct inversions
     */
    public function test_i_vi_ii_v_progression_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'D')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-vi-ii-V')
            ->assertSet('chords.1.tone', 'D')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.tone', 'B')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.2.inversion', 'first')
            ->assertSet('chords.3.tone', 'E')
            ->assertSet('chords.3.semitone', 'minor')
            ->assertSet('chords.3.inversion', 'root')
            ->assertSet('chords.4.tone', 'A')
            ->assertSet('chords.4.inversion', 'first');
    }

    /**
     * Test that ii-V-I progression applies correct inversions
     */
    public function test_ii_v_i_progression_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'A#')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'ii-V-I')
            ->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.1.semitone', 'minor')
            ->assertSet('chords.1.inversion', 'second')
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.2.inversion', 'first')
            ->assertSet('chords.3.tone', 'A#')
            ->assertSet('chords.3.inversion', 'root');
    }

    /**
     * Test that inversions update when key changes
     */
    public function test_inversions_update_when_key_changes(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->set('showVoiceLeading', true)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V');

        // Verify initial state in C
        $component->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.2.inversion', 'second')
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.3.inversion', 'first');

        // Change key to E
        $component->call('setKey', 'E')
            ->assertSet('chords.1.tone', 'E')
            ->assertSet('chords.2.tone', 'A')
            ->assertSet('chords.2.inversion', 'second')
            ->assertSet('chords.3.tone', 'B')
            ->assertSet('chords.3.inversion', 'first');
    }

    /**
     * Test that inversions are not applied when voice leading is disabled
     */
    public function test_inversions_not_applied_when_voice_leading_disabled(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('showVoiceLeading', false)
            ->set('selectedKey', 'C')
            ->call('setProgression', 'I-IV-V')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.inversion', 'root')
            ->assertSet('chords.3.inversion', 'root');
    }
}