<?php

declare(strict_types=1);

namespace Tests\Browser\Auth;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationTest extends DuskTestCase
{
    public function test_can_register_new_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('input[name="name"]', 'Test User')
                ->type('input[name="email"]', 'newuser@example.com')
                ->type('input[name="password"]', 'password123')
                ->type('input[name="password_confirmation"]', 'password123')
                ->press('Register')
                ->assertAuthenticated()
                ->assertUrlIs('/dashboard');
        });
    }

    public function test_shows_validation_errors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->press('Register')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertSee('The password field is required');
        });
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('input[name="name"]', 'Test User')
                ->type('input[name="email"]', 'test@example.com')
                ->type('input[name="password"]', 'password123')
                ->type('input[name="password_confirmation"]', 'different')
                ->press('Register')
                ->assertSee('password confirmation does not match');
        });
    }

    public function test_can_navigate_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->clickLink('Already registered?')
                ->assertUrlIs('/login');
        });
    }
}
