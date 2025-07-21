<?php

declare(strict_types=1);

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_can_login_with_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('input[type="email"]', 'test@example.com')
                ->type('input[type="password"]', 'password')
                ->press('Log in')
                ->assertUrlIs('/dashboard')
                ->assertAuthenticated();
        });
    }

    public function test_can_access_google_oauth(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Continue with Google')
                ->assertPresent('a[href*="/auth/google"]');
        });
    }

    public function test_shows_validation_errors_on_invalid_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('input[type="email"]', 'invalid@example.com')
                ->type('input[type="password"]', 'wrongpassword')
                ->press('Log in')
                ->assertSee('These credentials do not match our records');
        });
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('input[type="email"]', $user->email)
                ->type('input[type="password"]', 'password')
                ->check('remember')
                ->press('Log in')
                ->assertAuthenticated()
                ->assertCookie('remember_web_'.sha1(env('APP_NAME', 'Laravel').'_session'));
        });
    }
}
