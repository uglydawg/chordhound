<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\ChordGrid;
use App\Livewire\PianoPlayer;

class ChordPlaybackIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_piano_player_receives_chord_data_from_chord_grid(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('chords', [
                1 => ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
                2 => ['position' => 2, 'tone' => 'G', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
                3 => ['position' => 3, 'tone' => 'A', 'semitone' => 'minor', 'inversion' => 'root', 'is_blue_note' => false],
                4 => ['position' => 4, 'tone' => 'F', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false],
            ])
            ->call('sendChordState')
            ->assertDispatched('chordsUpdated');
    }

    public function test_piano_player_playback_settings(): void
    {
        $component = Livewire::test(PianoPlayer::class)
            ->assertSet('tempo', 120)
            ->assertSet('duration', 16) // 4 beats per chord * 4 chords
            ->assertSet('isPlaying', false);

        // Test tempo update
        $component->call('updateTempo', 140)
            ->assertSet('tempo', 140)
            ->assertDispatched('tempo-changed', tempo: 140);

        // Test playback toggle
        $component->call('togglePlayback')
            ->assertSet('isPlaying', true)
            ->assertDispatched('toggle-playback', isPlaying: true);

        // Test stop
        $component->call('stop')
            ->assertSet('isPlaying', false)
            ->assertSet('currentTime', 0)
            ->assertDispatched('stop-playback');
    }

    public function test_chord_grid_highlights_playing_position(): void
    {
        $component = Livewire::test(ChordGrid::class)
            ->assertSet('playingPosition', null);

        // Simulate chord position highlight
        $component->call('highlightChordPosition', 2)
            ->assertSet('playingPosition', 2);

        // Clear highlight on stop
        $component->call('clearHighlight')
            ->assertSet('playingPosition', null);
    }

    public function test_chord_click_sends_play_event(): void
    {
        Livewire::test(ChordGrid::class)
            ->set('chords.1', ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false])
            ->call('playChord', 1)
            ->assertDispatched('play-chord', chord: ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root', 'is_blue_note' => false]);
    }

    public function test_beat_indicators_show_correct_beats(): void
    {
        $component = Livewire::test(ChordGrid::class);
        
        $component->assertSee('Beat 1-4')
            ->assertSee('Beat 5-8')
            ->assertSee('Beat 9-12')
            ->assertSee('Beat 13-16');
    }
}