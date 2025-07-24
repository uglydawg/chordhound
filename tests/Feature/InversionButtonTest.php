<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('inversion buttons have compact visual styling', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for compact button classes
    $component->assertSee('text-xs') // Small text
        ->assertSee('w-6 h-6'); // 24px x 24px visual size
});

it('inversion buttons maintain minimum touch targets', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for touch target enhancements
    $component->assertSee('min-w-[44px]')
        ->assertSee('min-h-[44px]');
});

it('inversion buttons show clear active states', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for active state styling
    $component->assertSee('bg-blue-500 text-white') // Active state
        ->assertSee('bg-zinc-700'); // Inactive state
});

it('inversion buttons have proper hover states', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for hover classes
    $component->assertSee('hover:bg-zinc-600')
        ->assertSee('hover:text-white');
});

it('inversion buttons are properly spaced', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for spacing between buttons
    $component->assertSee('space-y-1'); // Updated from space-y-0.5
});