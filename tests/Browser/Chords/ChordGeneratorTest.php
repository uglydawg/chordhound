<?php

declare(strict_types=1);

namespace Tests\Browser\Chords;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordGeneratorTest extends DuskTestCase
{
    public function test_can_access_chord_generator(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Chord Progression')
                ->assertVisible('.chord-block')
                ->assertSee('Chord Palette');
        });
    }

    public function test_can_select_chord(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->assertSeeIn('.chord-block:first-child', 'C');
        });
    }

    public function test_can_select_chord_type(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->click('button:text("Minor")')
                ->assertSeeIn('.chord-block:first-child', 'Cm');
        });
    }

    public function test_can_select_inversion(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->click('button:text("First")')
                ->assertSeeIn('.chord-block:first-child', 'Fir');
        });
    }

    public function test_can_clear_chord(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.chord-block:first-child')
                ->click('button:text("C")')
                ->assertSeeIn('.chord-block:first-child', 'C')
                ->mouseover('.chord-block:first-child')
                ->click('.chord-block:first-child button.bg-zinc-700')
                ->assertDontSeeIn('.chord-block:first-child', 'C');
        });
    }

    public function test_shows_mini_piano_for_each_chord(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertVisible('.chord-block + .bg-zinc-900')
                ->assertPresent('svg.piano-keyboard');
        });
    }
}
