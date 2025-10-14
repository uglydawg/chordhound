# Technical Specification

This is the technical specification for the spec detailed in @.agent-os/specs/2025-07-23-chord-ui-improvements/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Technical Requirements

### Chord Tile Button Conversion

- Convert chord tile containers from `<div>` elements to semantic `<button>` elements
- Implement proper ARIA labels for accessibility (e.g., "Select C minor chord, root position")
- Add CSS `user-select: none` to prevent text selection on all chord UI text elements
- Ensure proper focus states for keyboard navigation
- Maintain existing click handlers and wire:click Livewire bindings
- Preserve visual states (active, playing, blue note) with button-appropriate styling

### Compact Inversion Buttons

- Reduce inversion button visual size while maintaining 44x44px minimum touch targets
- Use CSS padding and negative margins if needed to create visual/touch target separation
- Implement clear active states for selected inversions
- Ensure buttons remain accessible with proper contrast ratios
- Consider using icon-only buttons with tooltips for space efficiency

### Voice Leading Toggle Implementation

- Add `showVoiceLeading` boolean property to ChordGrid Livewire component (default: false)
- Store preference in user session or local storage for persistence
- Create toggle button in the UI header or settings area
- Conditionally render voice leading animations and path visualizations
- Update blade template with `@if($showVoiceLeading)` directives
- Ensure smooth transition when toggling visibility

### Dark Mode Login Button Fix

- Audit Login.php Livewire component and login.blade.php template
- Identify hardcoded colors or missing dark mode utilities
- Update button classes to use Tailwind's dark mode variants
- Ensure proper contrast ratios (WCAG AA minimum 4.5:1)
- Test with Flux UI's dark mode implementation
- Apply consistent theming across all auth-related buttons

## Approach Options

**Option A: Native Button Elements with Custom Styling**
- Pros: Semantic HTML, better accessibility, native button behaviors
- Cons: May require significant CSS overrides for custom styling

**Option B: Enhanced Div Elements with ARIA** (Selected)
- Pros: More control over styling, easier integration with existing code
- Cons: Requires careful ARIA implementation

**Rationale:** While Option A would be ideal for new development, Option B minimizes breaking changes to the existing codebase while still solving the core issues. We can progressively enhance to full button elements in a future refactor.

## Implementation Details

### Livewire Component Updates

```php
// ChordGrid.php additions
public bool $showVoiceLeading = false;

public function mount()
{
    $this->showVoiceLeading = session('showVoiceLeading', false);
}

public function toggleVoiceLeading()
{
    $this->showVoiceLeading = !$this->showVoiceLeading;
    session(['showVoiceLeading' => $this->showVoiceLeading]);
}
```

### CSS Modifications

```css
/* Prevent text selection on chord tiles */
.chord-tile {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    cursor: pointer;
    touch-action: manipulation;
}

/* Compact inversion buttons */
.inversion-btn {
    @apply text-xs px-2 py-1;
    min-width: 2.75rem; /* 44px touch target */
    min-height: 2.75rem;
}

/* Dark mode login button */
.login-btn {
    @apply bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600;
    @apply text-white dark:text-gray-900;
}
```

### Blade Template Structure

```blade
{{-- Chord tile with button behavior --}}
<div 
    role="button"
    tabindex="0"
    aria-label="Select {{ $chordName }} chord, {{ $inversionName }} position"
    class="chord-tile ..."
    wire:click="selectChord({{ $index }})"
    @keydown.enter="$wire.selectChord({{ $index }})"
    @keydown.space.prevent="$wire.selectChord({{ $index }})"
>
    {{-- Chord content --}}
</div>

{{-- Voice leading toggle --}}
@if($showVoiceLeading)
    <div class="voice-leading-container">
        {{-- Voice leading visualizations --}}
    </div>
@endif
```

## Performance Considerations

- Use Alpine.js for client-side toggle interactions to avoid server round trips
- Lazy-load voice leading animations only when visible
- Minimize re-renders by using targeted Livewire updates
- Consider using CSS containment for chord tile animations

## Accessibility Requirements

- All interactive elements must be keyboard accessible
- Maintain focus indicators for keyboard navigation  
- Provide appropriate ARIA labels and roles
- Ensure 4.5:1 contrast ratios for all text
- Support screen reader announcements for state changes

## Browser Compatibility

- Test touch interactions on iOS Safari, Chrome Android
- Verify button styling across modern browsers
- Ensure dark mode works with browser-level dark mode preferences
- Test with mobile screen readers (VoiceOver, TalkBack)

## External Dependencies

None required - all functionality can be implemented with existing Laravel, Livewire, Alpine.js, and Tailwind CSS.