<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChordSetChord extends Model
{
    protected $fillable = [
        'chord_set_id',
        'position',
        'tone',
        'semitone',
        'inversion',
        'is_blue_note',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_blue_note' => 'boolean',
    ];

    public const TONES = [
        'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B',
    ];

    public const SEMITONES = [
        'major' => 'Major',
        'minor' => 'Minor',
        'diminished' => 'Diminished',
        'augmented' => 'Augmented',
    ];

    public const INVERSIONS = [
        'root' => 'Root Position',
        'first' => 'First Inversion',
        'second' => 'Second Inversion',
        'third' => 'Third Inversion',
    ];

    public function chordSet(): BelongsTo
    {
        return $this->belongsTo(ChordSet::class);
    }

    public function getNotesAttribute(): array
    {
        $chordService = app(\App\Services\ChordService::class);

        return $chordService->getChordNotes($this->tone, $this->semitone, $this->inversion);
    }
}
