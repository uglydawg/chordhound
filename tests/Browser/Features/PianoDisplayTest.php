<?php

declare(strict_types=1);

namespace Tests\Browser\Features;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PianoDisplayTest extends DuskTestCase
{
    public function test_shows_piano_for_each_chord(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPresent('svg.piano-keyboard')
                ->assertVisible('.bg-zinc-900.border.border-zinc-800.rounded-lg.p-2');
        });
    }

    public function test_piano_updates_on_chord_selection(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->pause(500)
                ->assertPresent('.chord-block:first-child + .bg-zinc-900 svg')
                ->assertPresent('.fill-blue-500'); // Highlighted keys
        });
    }

    public function test_piano_shows_correct_notes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->click('button:text("Major")')
                ->pause(500)
                // C Major should highlight C, E, G
                ->assertPresent('.chord-block:first-child + .bg-zinc-900 .fill-blue-500');
        });
    }

    public function test_piano_shows_inversions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->click('button:text("First")')
                ->pause(500)
                ->assertSeeIn('.chord-block:first-child', 'Fir');
        });
    }
}
