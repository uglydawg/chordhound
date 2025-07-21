<?php

declare(strict_types=1);

use App\Livewire\VoiceLeadingAnimation;
use Livewire\Livewire;

it('renders voice leading animation for valid chord progression', function () {
    $fromChord = [
        'tone' => 'C',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    $toChord = [
        'tone' => 'F',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    Livewire::test(VoiceLeadingAnimation::class, [
        'fromChord' => $fromChord,
        'toChord' => $toChord,
        'position' => 1,
    ])
        ->assertSet('showAnimation', true)
        ->assertSee('C')
        ->assertSee('F')
        ->assertSee('Sequential');
});

it('does not show animation for empty chords', function () {
    $fromChord = [
        'tone' => '',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    $toChord = [
        'tone' => 'F',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    Livewire::test(VoiceLeadingAnimation::class, [
        'fromChord' => $fromChord,
        'toChord' => $toChord,
        'position' => 1,
    ])
        ->assertSet('showAnimation', false);
});

it('shows voice movements in chord progression', function () {
    $fromChord = [
        'tone' => 'G',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    $toChord = [
        'tone' => 'C',
        'semitone' => 'major',
        'inversion' => 'root',
    ];

    Livewire::test(VoiceLeadingAnimation::class, [
        'fromChord' => $fromChord,
        'toChord' => $toChord,
        'position' => 1,
    ])
        ->assertSet('showAnimation', true)
        ->assertSee('G')
        ->assertSee('C')
        ->assertSee('B:')
        ->assertSee('M:')
        ->assertSee('T:');
});
