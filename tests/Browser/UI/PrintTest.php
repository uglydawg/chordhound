<?php

declare(strict_types=1);

namespace Tests\Browser\UI;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PrintTest extends DuskTestCase
{
    public function test_print_button_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPresent('button[onclick="window.print()"]');
        });
    }

    public function test_print_styles_applied(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSourceHas('@media print');
        });
    }

    public function test_print_hides_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSourceHas('nav,')
                ->assertSourceHas('.print\\:hidden')
                ->assertSourceHas('display: none !important');
        });
    }
}
