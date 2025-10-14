# Tests Specification

This is the tests coverage details for the spec detailed in @.agent-os/specs/2025-07-23-chord-ui-improvements/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Test Coverage

### Unit Tests

**ChordGrid Component**
- Test showVoiceLeading property defaults to false
- Test toggleVoiceLeading method toggles the boolean value
- Test session persistence of voice leading preference
- Test component mounting with existing session value

### Integration Tests

**Chord Tile Interaction**
- Test chord selection via click event triggers selectChord method
- Test keyboard navigation (Enter and Space keys) triggers selection
- Test ARIA labels are properly generated for each chord state
- Test focus management when navigating between chord tiles

**Inversion Button Functionality**
- Test inversion button clicks update chord inversion state
- Test visual feedback for active inversion selection
- Test inversion changes trigger voice leading recalculation

**Voice Leading Toggle**
- Test toggle button shows/hides voice leading visualization
- Test preference persists across page reloads
- Test voice leading calculations still occur when hidden (for audio playback)

### Feature Tests

**Mobile Touch Interaction**
- Test touch events on chord tiles don't trigger text selection
- Test rapid tapping between chords works smoothly
- Test touch targets meet minimum size requirements
- Test pinch-to-zoom doesn't interfere with chord selection

**Dark Mode Consistency**
- Test login button adapts to dark mode class on body/html element
- Test all auth buttons maintain proper contrast in dark mode
- Test dark mode styling persists across authentication flow
- Test browser-level dark mode preference is respected

**Accessibility Flow**
- Test complete keyboard navigation through chord grid
- Test screen reader announcements for chord selection
- Test focus indicators are visible and clear
- Test ARIA labels update with chord state changes

### Browser/Device Testing

**Mobile Browsers**
- iOS Safari: Test text selection prevention and touch interactions
- Chrome Android: Test touch events and dark mode
- Firefox Mobile: Test button styling and animations

**Desktop Browsers**
- Chrome/Edge: Test keyboard navigation and dark mode
- Firefox: Test CSS compatibility and focus states
- Safari: Test touch events on Mac trackpads

### Visual Regression Tests

**Component States**
- Capture chord tile in default, active, playing, and blue note states
- Capture inversion buttons in all selection states
- Capture voice leading visible and hidden states
- Capture login button in light and dark modes

### Performance Tests

**Interaction Responsiveness**
- Measure time from touch/click to visual feedback
- Test rapid chord selection doesn't cause lag
- Verify voice leading toggle doesn't cause layout shift
- Ensure animations run at 60fps on mobile devices

## Mocking Requirements

**Session Storage**
- Mock Laravel session for voice leading preference tests
- Mock session retrieval for component mounting tests

**Browser APIs**
- Mock matchMedia for dark mode preference tests
- Mock touch events for mobile interaction tests

**Livewire Interactions**
- Mock wire:click handlers for unit tests
- Mock Livewire component lifecycle for integration tests

## Test Implementation Examples

### Pest Test for Voice Leading Toggle

```php
it('toggles voice leading visibility and persists preference', function () {
    Livewire::test(ChordGrid::class)
        ->assertSet('showVoiceLeading', false)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', true)
        ->assertSessionHas('showVoiceLeading', true)
        ->call('toggleVoiceLeading')
        ->assertSet('showVoiceLeading', false)
        ->assertSessionHas('showVoiceLeading', false);
});
```

### Pest Test for Chord Tile Interaction

```php
it('allows chord selection via click without text selection', function () {
    Livewire::test(ChordGrid::class)
        ->call('selectChord', 0)
        ->assertSet('selectedChordIndex', 0)
        ->assertEmitted('chordSelected');
});
```

### Laravel Dusk Test for Mobile Interaction

```php
public function testMobileChordSelectionPreventsTextSelection()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/chords')
            ->resize(375, 812) // iPhone dimensions
            ->tap('.chord-tile:first-child')
            ->assertNotPresent('.text-selection-handles')
            ->assertAttribute('.chord-tile:first-child', 'aria-pressed', 'true');
    });
}
```

### Dusk Test for Dark Mode Login Button

```php
public function testLoginButtonAdaptsToDarkMode()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertVisible('button[type="submit"]')
            ->screenshot('login-light-mode')
            ->script('document.documentElement.classList.add("dark")');
        
        $browser->waitFor('.dark')
            ->assertVisible('button[type="submit"]')
            ->screenshot('login-dark-mode');
        
        // Visual regression test would compare screenshots
    });
}
```

## Testing Priorities

1. **Critical Path**: Mobile chord selection without text selection
2. **High Priority**: Dark mode login button styling consistency
3. **Medium Priority**: Voice leading toggle persistence
4. **Lower Priority**: Keyboard navigation enhancements

All new functionality must have test coverage before deployment, with particular emphasis on mobile browser compatibility testing.