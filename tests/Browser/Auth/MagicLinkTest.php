<?php

declare(strict_types=1);

namespace Tests\Browser\Auth;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MagicLinkTest extends DuskTestCase
{
    public function test_can_open_magic_link_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Magic Link')
                ->waitFor('[data-flux-modal="magic-link"]')
                ->assertVisible('[data-flux-modal="magic-link"]')
                ->assertVisible('[data-flux-modal="magic-link"] input[type="email"]');
        });
    }

    public function test_can_request_magic_link(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Magic Link')
                ->waitFor('[data-flux-modal="magic-link"]')
                ->type('[data-flux-modal="magic-link"] input[type="email"]', 'test@example.com')
                ->press('Send Magic Link')
                ->waitForText('Magic link sent!')
                ->assertSee('Magic link sent!');
        });
    }

    public function test_validates_email_format(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Magic Link')
                ->waitFor('[data-flux-modal="magic-link"]')
                ->type('[data-flux-modal="magic-link"] input[type="email"]', 'invalid-email')
                ->press('Send Magic Link')
                ->assertSee('valid email');
        });
    }
}
