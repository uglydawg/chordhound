<?php

declare(strict_types=1);

namespace Tests\Browser\Features;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VoiceLeadingTest extends DuskTestCase
{
    public function test_voice_leading_toggle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Voice Leading')
                ->assertPresent('button.text-green-500') // Should be on by default
                ->press('Voice Leading')
                ->pause(300)
                ->assertPresent('button.text-secondary:has(span:text("Voice Leading"))');
        });
    }

    public function test_voice_leading_animation_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-V-vi-IV')
                ->pause(500)
                ->assertPresent('[data-voice-position]');
        });
    }

    public function test_optimize_voice_leading(): void
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

    public function test_voice_leading_updates_on_chord_change(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-IV-V')
                ->pause(500)
                ->assertPresent('[data-voice-position="1"]')
                ->click('.chord-block:nth-child(2)')
                ->click('button:text("F")')
                ->pause(500)
                ->assertPresent('[data-voice-position="1"]');
        });
    }
}
