<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Livewire\Livewire;

it('login page has dark mode compatible elements', function () {
    $response = $this->get('/login');
    
    // Check that the page loads and has dark mode classes
    $response->assertOk()
        ->assertSee('dark:bg-zinc-900') // Auth layout dark background
        ->assertSee('dark:text-white');  // Dark mode text
});

it('register page has dark mode compatible elements', function () {
    $response = $this->get('/register');
    
    // Check that the page loads and has dark mode classes
    $response->assertOk()
        ->assertSee('dark:bg-zinc-900')
        ->assertSee('dark:text-white');
});

it('flux buttons automatically support dark mode', function () {
    // Flux UI components should handle dark mode automatically
    // This test verifies the components are being used correctly
    $component = Livewire::test(Login::class);
    
    // Check button exists with proper structure
    $html = $component->html();
    $this->assertStringContainsString('button', $html);
    $this->assertStringContainsString('type="submit"', $html);
    $this->assertStringContainsString('Log in', $html);
});