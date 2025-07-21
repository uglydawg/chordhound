<?php

declare(strict_types=1);

namespace Tests\Browser\UI;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationTest extends DuskTestCase
{
    public function test_guest_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee("Uglydawg's Piano Generator")
                ->clickLink('Log in')
                ->assertUrlIs('/login')
                ->back()
                ->clickLink('Register')
                ->assertUrlIs('/register');
        });
    }

    public function test_authenticated_navigation(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee($user->name)
                ->click('[aria-label="Open user menu"]')
                ->clickLink('Dashboard')
                ->assertUrlIs('/dashboard')
                ->clickLink('Piano Chords')
                ->assertUrlIs('/chords');
        });
    }

    public function test_sidebar_navigation(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSeeLink('Dashboard')
                ->assertSeeLink('Piano Chords')
                ->assertSeeLink('My Chord Sets')
                ->clickLink('My Chord Sets')
                ->assertUrlIs('/my-chord-sets');
        });
    }

    public function test_mobile_menu_toggle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(375, 667)
                ->press('[aria-label="Open menu"]')
                ->pause(300)
                ->assertVisible('nav')
                ->press('[aria-label="Close menu"]')
                ->pause(300)
                ->assertNotVisible('nav');
        });
    }

    public function test_logout_functionality(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->click('[aria-label="Open user menu"]')
                ->press('Log out')
                ->assertGuest()
                ->assertUrlIs('/');
        });
    }
}
