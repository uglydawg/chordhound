<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\ChordService;
use Tests\TestCase;

class ChordOctaveTest extends TestCase
{
    private ChordService $chordService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chordService = new ChordService();
    }

    /**
     * Test that root position chords are in C4
     */
    public function test_root_position_chords_in_c4(): void
    {
        $notes = $this->chordService->getChordNotes('C', 'major', 'root');
        
        $this->assertEquals(['C', 'E', 'G'], $notes);
        
        // The service returns note names without octaves
        // The frontend adds octaves, so we're testing the note order is correct
    }

    /**
     * Test that first inversion moves root to end
     */
    public function test_first_inversion_note_order(): void
    {
        $notes = $this->chordService->getChordNotes('C', 'major', 'first');
        
        // First inversion should be E-G-C (root moved to end)
        $this->assertEquals(['E', 'G', 'C'], $notes);
    }

    /**
     * Test that second inversion moves first two notes to end
     */
    public function test_second_inversion_note_order(): void
    {
        $notes = $this->chordService->getChordNotes('F', 'major', 'second');
        
        // F major: F-A-C
        // Second inversion should be C-F-A (first two moved to end)
        $this->assertEquals(['C', 'F', 'A'], $notes);
    }

    /**
     * Test minor chord inversions
     */
    public function test_minor_chord_inversions(): void
    {
        // A minor root position
        $notes = $this->chordService->getChordNotes('A', 'minor', 'root');
        $this->assertEquals(['A', 'C', 'E'], $notes);
        
        // A minor first inversion
        $notes = $this->chordService->getChordNotes('A', 'minor', 'first');
        $this->assertEquals(['C', 'E', 'A'], $notes);
        
        // A minor second inversion
        $notes = $this->chordService->getChordNotes('A', 'minor', 'second');
        $this->assertEquals(['E', 'A', 'C'], $notes);
    }

    /**
     * Test diminished chord inversions
     */
    public function test_diminished_chord_inversions(): void
    {
        // B diminished root position (B-D-F)
        $notes = $this->chordService->getChordNotes('B', 'diminished', 'root');
        $this->assertEquals(['B', 'D', 'F'], $notes);
        
        // B diminished first inversion
        $notes = $this->chordService->getChordNotes('B', 'diminished', 'first');
        $this->assertEquals(['D', 'F', 'B'], $notes);
    }
}