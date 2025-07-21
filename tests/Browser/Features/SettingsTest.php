<?php

declare(strict_types=1);

namespace Tests\Browser\Features;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SettingsTest extends DuskTestCase
{
    public function test_can_access_settings(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->clickLink('Settings')
                ->assertUrlIs('/settings/profile')
                ->assertSee('Profile')
                ->assertSee('Password')
                ->assertSee('Appearance');
        });
    }

    public function test_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/profile')
                ->clear('input[name="name"]')
                ->type('input[name="name"]', 'Updated Name')
                ->press('Save')
                ->waitForText('Profile updated')
                ->assertSee('Profile updated');
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_change_password(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/password')
                ->type('input[name="current_password"]', 'password')
                ->type('input[name="password"]', 'newpassword123')
                ->type('input[name="password_confirmation"]', 'newpassword123')
                ->press('Update password')
                ->waitForText('Password updated')
                ->assertSee('Password updated');
        });
    }

    public function test_can_change_appearance(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/appearance')
                ->assertSee('Appearance')
                ->assertPresent('button[wire\\:click*="setTheme"]');
        });
    }
}
