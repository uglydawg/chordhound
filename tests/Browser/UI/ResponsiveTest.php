<?php

declare(strict_types=1);

namespace Tests\Browser\UI;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResponsiveTest extends DuskTestCase
{
    public function test_mobile_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(375, 667)
                ->assertVisible('.chord-block')
                ->assertPresent('[aria-label="Open menu"]');
        });
    }

    public function test_tablet_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(768, 1024)
                ->assertVisible('.chord-block')
                ->assertPresent('.grid-cols-2');
        });
    }

    public function test_desktop_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(1920, 1080)
                ->assertVisible('.chord-block')
                ->assertPresent('.grid-cols-4');
        });
    }

    public function test_chord_grid_responsive(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(375, 667)
                ->assertPresent('.grid.grid-cols-4')
                ->resize(1920, 1080)
                ->assertPresent('.grid.grid-cols-4');
        });
    }
}
