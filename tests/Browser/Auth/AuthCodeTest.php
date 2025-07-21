<?php

declare(strict_types=1);

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthCodeTest extends DuskTestCase
{
    public function test_can_open_auth_code_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Auth Code')
                ->waitFor('[data-flux-modal="auth-code"]')
                ->assertVisible('[data-flux-modal="auth-code"]')
                ->assertVisible('[data-flux-modal="auth-code"] input[type="email"]');
        });
    }

    public function test_can_request_auth_code(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Auth Code')
                ->waitFor('[data-flux-modal="auth-code"]')
                ->type('[data-flux-modal="auth-code"] input[type="email"]', 'test@example.com')
                ->press('Send Code')
                ->waitForText('code has been sent')
                ->assertSee('code has been sent');
        });
    }

    public function test_can_verify_auth_code(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create a test auth code
        $authCode = \App\Models\AuthCode::create([
            'email' => 'test@example.com',
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Continue with Auth Code')
                ->waitFor('[data-flux-modal="auth-code"]')
                ->type('[data-flux-modal="auth-code"] input[type="email"]', 'test@example.com')
                ->press('Send Code')
                ->waitFor('input[name="code"]')
                ->type('input[name="code"]', '123456')
                ->press('Verify')
                ->assertAuthenticated();
        });
    }
}
