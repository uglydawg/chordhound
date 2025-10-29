<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ChordGrid;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceLeadingProgressionsTest extends TestCase
{
    /**
     * Test I-IV-V progression uses optimal inversions
     */
    public function test_i_iv_v_progression_uses_optimal_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-IV-V')
            ->assertSet('chords.1.tone', 'C')
            ->assertSet('chords.1.inversion', 'root')      // I - root position
            ->assertSet('chords.2.tone', 'F')
            ->assertSet('chords.2.inversion', 'second')    // IV - second inversion (F/C)
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.3.inversion', 'first');     // V - first inversion (G/B)
    }

    /**
     * Test I-vi-IV-V progression uses optimal inversions
     */
    public function test_i_vi_iv_v_progression_uses_optimal_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'G')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-vi-IV-V')
            ->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.1.inversion', 'root')      // I - root position
            ->assertSet('chords.2.tone', 'E')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.2.inversion', 'first')     // vi - first inversion (Em/G)
            ->assertSet('chords.3.tone', 'C')
            ->assertSet('chords.3.inversion', 'second')    // IV - second inversion (C/G)
            ->assertSet('chords.4.tone', 'D')
            ->assertSet('chords.4.inversion', 'first');     // V - first inversion (D/F#)
    }

    /**
     * Test ii-V-I progression uses optimal inversions
     */
    public function test_ii_v_i_progression_uses_optimal_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'F')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'ii-V-I')
            ->assertSet('chords.1.tone', 'G')
            ->assertSet('chords.1.semitone', 'minor')
            ->assertSet('chords.1.inversion', 'second')    // ii - second inversion (Gm/D)
            ->assertSet('chords.2.tone', 'C')
            ->assertSet('chords.2.inversion', 'first')     // V - first inversion (C/E)
            ->assertSet('chords.3.tone', 'F')
            ->assertSet('chords.3.inversion', 'root');      // I - root position
    }

    /**
     * Test voice leading toggle does not affect preset progression inversions
     * Voice leading toggle only affects visual lines, not the inversions themselves
     */
    public function test_voice_leading_toggle_switches_inversions(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'C')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-IV-V')
            ->assertSet('chords.2.inversion', 'second')    // IV inversion
            ->call('toggleVoiceLeading')
            ->assertSet('showVoiceLeading', false)
            ->assertSet('chords.2.inversion', 'second');    // IV inversion remains the same
    }

    /**
     * Test different keys work correctly
     */
    public function test_voice_leading_works_in_different_keys(): void
    {
        // Test in D major
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'D')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'I-vi-IV-V')
            ->assertSet('chords.1.tone', 'D')
            ->assertSet('chords.1.inversion', 'root')
            ->assertSet('chords.2.tone', 'B')
            ->assertSet('chords.2.semitone', 'minor')
            ->assertSet('chords.2.inversion', 'first')     // Bm/D
            ->assertSet('chords.3.tone', 'G')
            ->assertSet('chords.3.inversion', 'second')    // G/D
            ->assertSet('chords.4.tone', 'A')
            ->assertSet('chords.4.inversion', 'first');     // A/C#

        // Test in A major
        Livewire::test(ChordGrid::class)
            ->set('selectedKey', 'A')
            ->set('showVoiceLeading', true)
            ->call('setProgression', 'vi-IV-I-V')
            ->assertSet('chords.1.tone', 'F#')
            ->assertSet('chords.1.semitone', 'minor')
            ->assertSet('chords.1.inversion', 'root')      // F#m - root
            ->assertSet('chords.2.tone', 'D')
            ->assertSet('chords.2.inversion', 'first')     // D/F#
            ->assertSet('chords.3.tone', 'A')
            ->assertSet('chords.3.inversion', 'second')    // A/E
            ->assertSet('chords.4.tone', 'E')
            ->assertSet('chords.4.inversion', 'root');      // E - root
    }
}