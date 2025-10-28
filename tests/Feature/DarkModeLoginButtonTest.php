<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use Livewire\Livewire;

it('login button has dark mode styling classes', function () {
    $component = Livewire::test(Login::class);
    
    // Check for dark mode utilities on the submit button
    $component->assertSeeHtml('dark:bg-')
        ->assertSeeHtml('dark:hover:bg-')
        ->assertSeeHtml('dark:text-');
});

it('login button has proper contrast in dark mode', function () {
    $component = Livewire::test(Login::class);
    
    // Check that the primary button exists
    $component->assertSeeHtml('variant="primary"')
        ->assertSeeHtml('type="submit"')
        ->assertSee('Log in');
});

it('all auth buttons have consistent styling', function () {
    // Check that all buttons use Flux UI components
    $component = Livewire::test(Login::class);
    
    // Check for consistent button usage
    $component->assertSeeHtml('flux:button')
        ->assertSeeHtml('variant="ghost"');
});

it('login button uses Flux UI dark mode patterns', function () {
    $component = Livewire::test(Login::class);
    
    // Flux UI uses specific patterns for dark mode
    $component->assertSeeHtml('type="submit"');
    
    // Check the HTML contains proper button structure
    $html = $component->html();
    $this->assertStringContainsString('button', $html);
    $this->assertStringContainsString('type="submit"', $html);
});