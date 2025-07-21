<?php

declare(strict_types=1);

namespace Tests\Browser\Chords;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChordSaveLoadTest extends DuskTestCase
{
    public function test_save_button_only_visible_when_authenticated(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertDontSee('Save');
        });

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee('Save');
        });
    }

    public function test_can_save_chord_progression(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-IV-V')
                ->pause(500)
                ->press('Save')
                ->waitFor('#save-chord-set-modal')
                ->type('#chord-set-name', 'My Test Progression')
                ->type('#chord-set-description', 'A test chord progression')
                ->press('Save Chord Set')
                ->waitForText('Chord set saved successfully!')
                ->assertSee('Chord set saved successfully!');
        });
    }

    public function test_save_modal_shows_chord_preview(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->select('select[wire:change*="setProgression"]', 'I-V-vi-IV')
                ->pause(500)
                ->press('Save')
                ->waitFor('#save-chord-set-modal')
                ->assertSee('Chords to save:')
                ->assertPresent('#chord-preview span');
        });
    }

    public function test_can_load_saved_chord_set(): void
    {
        $user = User::factory()->create();
        $chordSet = \App\Models\ChordSet::create([
            'user_id' => $user->id,
            'name' => 'Test Progression',
            'description' => 'A test',
        ]);

        $chordSet->chords()->createMany([
            ['position' => 1, 'tone' => 'C', 'semitone' => 'major', 'inversion' => 'root'],
            ['position' => 2, 'tone' => 'F', 'semitone' => 'major', 'inversion' => 'root'],
            ['position' => 3, 'tone' => 'G', 'semitone' => 'major', 'inversion' => 'root'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $chordSet) {
            $browser->loginAs($user)
                ->visit('/my-chord-sets')
                ->assertSee('Test Progression')
                ->click('a:has(svg) span:text("Play")')
                ->assertUrlIs('/chords?load='.$chordSet->id)
                ->assertSee('C')
                ->assertSee('F')
                ->assertSee('G');
        });
    }

    public function test_can_delete_chord_set(): void
    {
        $user = User::factory()->create();
        $chordSet = \App\Models\ChordSet::create([
            'user_id' => $user->id,
            'name' => 'To Delete',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/my-chord-sets')
                ->assertSee('To Delete')
                ->press('Delete')
                ->acceptDialog()
                ->pause(500)
                ->assertDontSee('To Delete');
        });
    }
}
