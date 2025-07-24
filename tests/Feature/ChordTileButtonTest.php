<?php

declare(strict_types=1);

use App\Livewire\ChordGrid;
use Livewire\Livewire;

it('chord tiles have button role and proper ARIA attributes', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check that chord tiles have proper button attributes
    $component->assertSeeHtml('role="button"')
        ->assertSeeHtml('tabindex="0"')
        ->assertSeeHtml('aria-label=');
});

it('chord tiles have 3D appearance with gradient and border', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for 3D styling
    $component->assertSee('bg-gradient-to-b') // Gradient background
        ->assertSee('border-b-4') // 3D border
        ->assertSee('shadow-md') // Shadow for depth
        ->assertSee('select-none'); // Prevent text selection
});

it('chord tiles respond to keyboard events', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Verify keyboard event handlers are present
    $component->assertSeeHtml('@keydown.enter')
        ->assertSeeHtml('@keydown.space');
});

it('chord tile maintains focus state for keyboard navigation', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C') 
        ->call('setProgression', 'I-IV-V');
    
    // Verify focus-related classes are present
    $component->assertSee('focus:outline')
        ->assertSee('focus:ring');
});

it('chord tile aria-label includes chord name and inversion', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for descriptive aria-labels
    $component->assertSeeHtml('aria-label="Select C major chord, root position"')
        ->assertSeeHtml('aria-label="Select F major chord, root position"')
        ->assertSeeHtml('aria-label="Select G major chord, root position"');
});

it('chord tiles have depression animation on click', function () {
    $component = Livewire::test(ChordGrid::class)
        ->call('setKey', 'C')
        ->call('setProgression', 'I-IV-V');
    
    // Check for mouse and touch event handlers for depression
    $component->assertSeeHtml('@mousedown')
        ->assertSeeHtml('@mouseup')
        ->assertSeeHtml('@touchstart')
        ->assertSeeHtml('@touchend')
        ->assertSee('active:translate-y-1')
        ->assertSee('active:border-b-2');
});