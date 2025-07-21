<?php

declare(strict_types=1);

namespace Tests\Browser\Features;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BlueNotesTest extends DuskTestCase
{
    public function test_blue_notes_highlighted(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-IV-V')
                ->pause(500)
                ->assertPresent('.ring-purple-500');
        });
    }

    public function test_blue_notes_calculation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:nth-child(1)')
                ->click('button:text("C")')
                ->click('.chord-block:nth-child(2)')
                ->click('button:text("G")')
                ->pause(500)
                ->assertPresent('.chord-block.ring-2');
        });
    }

    public function test_blue_notes_update_on_chord_change(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-V-vi-IV')
                ->pause(500)
                ->click('.chord-block:nth-child(3)')
                ->click('button:text("A")')
                ->click('button:text("Minor")')
                ->pause(500)
                ->assertPresent('.ring-purple-500');
        });
    }
}
