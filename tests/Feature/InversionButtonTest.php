<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('inversion buttons have compact visual styling with 3D effect', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for button styling
    $component->assertSee('text-xs') // Small text
        ->assertSee('w-8 h-8') // 32px x 32px visual size
        ->assertSee('bg-gradient-to-b') // 3D gradient effect
        ->assertSee('border-b-4'); // 3D border effect
});

it('inversion buttons have proper size without overlap', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for proper sizing and spacing
    $component->assertSee('w-8 h-8') // 32px buttons
        ->assertSee('space-y-2'); // Proper spacing between buttons
});

it('inversion buttons show clear active states with 3D effect', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for active state styling with 3D effects
    $component->assertSee('from-blue-400 to-blue-600') // Active gradient
        ->assertSee('from-zinc-600 to-zinc-700') // Inactive gradient
        ->assertSee('scale-105'); // Active state scale
});

it('inversion buttons have 3D hover and active states', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for 3D hover and active effects
    $component->assertSee('hover:translate-y-[1px]') // Hover press effect
        ->assertSee('active:translate-y-[2px]') // Active press effect
        ->assertSee('hover:border-b-2'); // 3D border change on hover
});

it('inversion buttons are properly spaced without overlap', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for proper spacing between buttons
    $component->assertSee('space-y-2') // Increased spacing
        ->assertDontSee('-m-2.5'); // No negative margins causing overlap
});