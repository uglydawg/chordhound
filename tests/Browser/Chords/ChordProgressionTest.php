<?php

declare(strict_types=1);

namespace Tests\Browser\Chords;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordProgressionTest extends DuskTestCase
{
    public function test_can_select_preset_progression(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-IV-V')
                ->pause(500)
                ->assertSee('G')
                ->assertSee('C')
                ->assertSee('D');
        });
    }

    public function test_can_change_key(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('button:text("C")')
                ->pause(500)
                ->select('select[wire:change*="setProgression"]', 'I-IV-V')
                ->pause(500)
                ->assertSee('C')
                ->assertSee('F')
                ->assertSee('G');
        });
    }

    public function test_can_toggle_major_minor(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('Minor')
                ->pause(500)
                ->assertSeeIn('button.bg-blue-600', 'Minor');
        });
    }

    public function test_can_optimize_voice_leading(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-V-vi-IV')
                ->pause(500)
                ->press('Optimize')
                ->pause(500)
                ->assertPresent('.chord-block');
        });
    }

    public function test_shows_roman_numeral_analysis(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('Analysis')
                ->pause(500)
                ->assertSee('I')
                ->assertPresent('.bg-zinc-800.rounded-lg');
        });
    }

    public function test_voice_leading_toggle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSeeIn('button:has(span:text("Voice Leading"))', 'Voice Leading')
                ->press('Voice Leading')
                ->pause(500)
                ->assertPresent('button.text-secondary:has(span:text("Voice Leading"))');
        });
    }
}
