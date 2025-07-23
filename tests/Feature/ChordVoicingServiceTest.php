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

    /**
     * Test specific voicing examples from user requirements
     */
    public function test_specific_voicing_examples(): void
    {
        // Test I-IV-V in key of C
        $this->assertEquals(['C4', 'E4', 'G4'], $this->service->getVoicing('I-IV-V', 'C', 1, 'C'));
        $this->assertEquals(['C4', 'F4', 'A4'], $this->service->getVoicing('I-IV-V', 'C', 2, 'F/C'));
        $this->assertEquals(['B3', 'D4', 'G4'], $this->service->getVoicing('I-IV-V', 'C', 3, 'G/B'));
        
        // Test I-vi-IV-V in key of D
        $this->assertEquals(['D4', 'F#4', 'A4'], $this->service->getVoicing('I-vi-IV-V', 'D', 1, 'D'));
        $this->assertEquals(['D4', 'F#4', 'B4'], $this->service->getVoicing('I-vi-IV-V', 'D', 2, 'Bm/D'));
        $this->assertEquals(['D4', 'G4', 'B4'], $this->service->getVoicing('I-vi-IV-V', 'D', 3, 'G/D'));
        $this->assertEquals(['C#4', 'E4', 'A4'], $this->service->getVoicing('I-vi-IV-V', 'D', 4, 'A/C#'));
        
        // Test vi-IV-I-V in key of A
        $this->assertEquals(['F#3', 'A3', 'C#4'], $this->service->getVoicing('vi-IV-I-V', 'A', 1, 'F#m'));
        $this->assertEquals(['F#3', 'A3', 'D4'], $this->service->getVoicing('vi-IV-I-V', 'A', 2, 'D/F#'));
        $this->assertEquals(['E3', 'A3', 'C#4'], $this->service->getVoicing('vi-IV-I-V', 'A', 3, 'A/E'));
        $this->assertEquals(['E3', 'G#3', 'B3'], $this->service->getVoicing('vi-IV-I-V', 'A', 4, 'E'));
        
        // Test ii-V-I in key of F
        $this->assertEquals(['D4', 'G4', 'A#4'], $this->service->getVoicing('ii-V-I', 'F', 1, 'Gm/D'));
        $this->assertEquals(['E4', 'G4', 'C5'], $this->service->getVoicing('ii-V-I', 'F', 2, 'C/E'));
        $this->assertEquals(['F4', 'A4', 'C5'], $this->service->getVoicing('ii-V-I', 'F', 3, 'F'));
    }

    /**
     * Test chord symbols match specifications
     */
    public function test_chord_symbols_match_specs(): void
    {
        // Test I-IV-V chord symbols in different keys
        $this->assertEquals('C', $this->service->getChordSymbol('I-IV-V', 'C', 1));
        $this->assertEquals('F/C', $this->service->getChordSymbol('I-IV-V', 'C', 2));
        $this->assertEquals('G/B', $this->service->getChordSymbol('I-IV-V', 'C', 3));
        
        // Test in F# key
        $this->assertEquals('F#', $this->service->getChordSymbol('I-IV-V', 'F#', 1));
        $this->assertEquals('B/F#', $this->service->getChordSymbol('I-IV-V', 'F#', 2));
        $this->assertEquals('C#/F', $this->service->getChordSymbol('I-IV-V', 'F#', 3));
        
        // Test I-vi-ii-V symbols
        $this->assertEquals('Am/C', $this->service->getChordSymbol('I-vi-ii-V', 'C', 2));
        $this->assertEquals('Dm', $this->service->getChordSymbol('I-vi-ii-V', 'C', 3));
    }

    /**
     * Test all keys are covered for each progression
     */
    public function test_all_keys_covered(): void
    {
        $keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $progressions = ['I-IV-V', 'I-vi-IV-V', 'vi-IV-I-V', 'I-vi-ii-V', 'ii-V-I'];
        
        foreach ($progressions as $progression) {
            $voicings = $this->service->getProgressionVoicings($progression);
            
            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $voicings, "Key $key should exist for $progression");
                $this->assertNotEmpty($voicings[$key], "Voicings for key $key in $progression should not be empty");
            }
        }
    }
}