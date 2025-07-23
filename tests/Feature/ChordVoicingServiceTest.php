<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\ChordVoicingService;
use Tests\TestCase;

class ChordVoicingServiceTest extends TestCase
{
    private ChordVoicingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChordVoicingService();
    }

    /**
     * Test that voicings are returned for I-IV-V progression
     */
    public function test_i_iv_v_voicings(): void
    {
        $voicing = $this->service->getVoicing('I-IV-V', 'C', 1, 'C');
        $this->assertEquals(['C4', 'E4', 'G4'], $voicing);
        
        $voicing = $this->service->getVoicing('I-IV-V', 'C', 2, 'F');
        $this->assertEquals(['C4', 'F4', 'A4'], $voicing);
        
        $voicing = $this->service->getVoicing('I-IV-V', 'C', 3, 'G');
        $this->assertEquals(['B3', 'D4', 'G4'], $voicing);
    }

    /**
     * Test chord symbols
     */
    public function test_chord_symbols(): void
    {
        $symbol = $this->service->getChordSymbol('I-IV-V', 'C', 2);
        $this->assertEquals('F/C', $symbol);
        
        $symbol = $this->service->getChordSymbol('I-vi-IV-V', 'G', 2);
        $this->assertEquals('Em/G', $symbol);
    }

    /**
     * Test different keys
     */
    public function test_different_keys(): void
    {
        // Test G major I-IV-V
        $voicing = $this->service->getVoicing('I-IV-V', 'G', 1, 'G');
        $this->assertEquals(['G3', 'B3', 'D4'], $voicing);
        
        $voicing = $this->service->getVoicing('I-IV-V', 'G', 2, 'C');
        $this->assertEquals(['G3', 'C4', 'E4'], $voicing);
    }

    /**
     * Test all progressions are available
     */
    public function test_all_progressions_available(): void
    {
        $progressions = ['I-IV-V', 'I-vi-IV-V', 'vi-IV-I-V', 'I-vi-ii-V', 'ii-V-I'];
        
        foreach ($progressions as $progression) {
            $voicings = $this->service->getProgressionVoicings($progression);
            $this->assertNotEmpty($voicings, "Voicings should exist for $progression");
            $this->assertArrayHasKey('C', $voicings, "C key should exist for $progression");
        }
    }
}