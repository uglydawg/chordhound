<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Chord Calculation Engine
    |--------------------------------------------------------------------------
    |
    | This option controls which chord calculation engine is used for
    | generating chord voicings. The 'legacy' engine uses hardcoded
    | voicing tables, while 'mathematical' uses algorithmic calculation.
    |
    | Supported: "legacy", "mathematical"
    |
    */

    'calculation_engine' => env('CHORD_CALCULATION_ENGINE', 'legacy'),

    /*
    |--------------------------------------------------------------------------
    | Voice Leading Optimization
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic voice leading optimization for chord
    | progressions. When enabled, the system will automatically calculate
    | optimal inversions to minimize movement between chords.
    |
    */

    'optimize_voice_leading' => env('CHORD_OPTIMIZE_VOICE_LEADING', true),

    /*
    |--------------------------------------------------------------------------
    | Bass Note Generation
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic bass note generation. When enabled,
    | the mathematical engine will add bass notes one octave below
    | the chord root for fuller harmonic support.
    |
    */

    'generate_bass_notes' => env('CHORD_GENERATE_BASS_NOTES', true),

    /*
    |--------------------------------------------------------------------------
    | Chord Calculation Cache
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching of chord calculations. This can improve
    | performance by storing frequently used chord voicings.
    |
    */

    'cache_calculations' => env('CHORD_CACHE_CALCULATIONS', false),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration (seconds)
    |--------------------------------------------------------------------------
    |
    | How long to cache chord calculations when caching is enabled.
    | Default is 3600 seconds (1 hour).
    |
    */

    'cache_duration' => env('CHORD_CACHE_DURATION', 3600),

];
