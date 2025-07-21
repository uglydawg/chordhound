<?php

declare(strict_types=1);

use App\Livewire\ChordDisplay;
use Livewire\Livewire;

it('generates piano keys with correct black key positioning', function () {
    $component = Livewire::test(ChordDisplay::class);

    // Get the generated piano keys
    $pianoKeys = $component->instance()->generatePianoKeys();

    // Filter white and black keys
    $whiteKeys = collect($pianoKeys)->where('type', 'white')->values();
    $blackKeys = collect($pianoKeys)->where('type', 'black')->values();

    // Check we have the correct number of keys for 3 octaves
    expect($whiteKeys)->toHaveCount(21); // 7 white keys * 3 octaves
    expect($blackKeys)->toHaveCount(15); // 5 black keys * 3 octaves

    // Check first octave black keys are positioned correctly
    $firstOctaveBlacks = $blackKeys->take(5);

    // C# should be between C and D (positions 0 and 1)
    $cSharp = $firstOctaveBlacks[0];
    expect($cSharp['note'])->toBe('C#');
    expect($cSharp['x'])->toBeGreaterThan($whiteKeys[0]['x']); // After C
    expect($cSharp['x'])->toBeLessThan($whiteKeys[1]['x']);    // Before D

    // D# should be between D and E (positions 1 and 2)
    $dSharp = $firstOctaveBlacks[1];
    expect($dSharp['note'])->toBe('D#');
    expect($dSharp['x'])->toBeGreaterThan($whiteKeys[1]['x']); // After D
    expect($dSharp['x'])->toBeLessThan($whiteKeys[2]['x']);    // Before E

    // There should be no black key between E and F (positions 2 and 3)
    // F# should be between F and G (positions 3 and 4)
    $fSharp = $firstOctaveBlacks[2];
    expect($fSharp['note'])->toBe('F#');
    expect($fSharp['x'])->toBeGreaterThan($whiteKeys[3]['x']); // After F
    expect($fSharp['x'])->toBeLessThan($whiteKeys[4]['x']);    // Before G

    // There should be no black key between B and C
});
