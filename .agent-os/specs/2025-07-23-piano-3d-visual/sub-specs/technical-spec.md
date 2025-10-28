# Technical Specification

This is the technical specification for the spec detailed in @.agent-os/specs/2025-07-23-piano-3d-visual/spec.md

> Created: 2025-07-23
> Version: 1.0.0

## Technical Requirements

### 3D Piano Key Styling

- White keys: Gradient from light to darker shade, with bottom border for depth
- Black keys: Darker gradient with more pronounced shadows
- Key spacing: Maintain current layout but add visual depth cues
- Border radius: Subtle rounding at bottom of keys for realism
- Shadows: Drop shadows between keys and under black keys

### Key Press Animation

- Depression depth: 2-4px translate on Y-axis when pressed
- Border reduction: Similar to chord buttons, reduce bottom border when pressed
- Timing: Instant depression, smooth release (100-150ms transition)
- Visual feedback: Darker gradient or shade when pressed
- Multiple keys: Support simultaneous key depression

### Visual Depth Implementation

- Use CSS transforms for 3D perspective
- Layer black keys above white keys with proper z-index
- Add subtle gradient to piano container for depth
- Implement box-shadows for realistic key separation
- Consider transform-style: preserve-3d for true 3D rendering

### Responsive Considerations

- Maintain current responsive behavior
- Ensure 3D effects don't break on mobile
- Touch targets remain accessible
- Performance optimization for animations

## Approach Options

**Option A: Pure CSS 3D Transforms**
- Pros: Best performance, no JavaScript overhead
- Cons: Limited animation control, complex for dynamic states

**Option B: CSS with Alpine.js Enhancements** (Selected)
- Pros: Better animation control, easier state management
- Cons: Slight JavaScript overhead

**Rationale:** Option B provides the best balance of visual quality and maintainability, allowing for smooth animations while leveraging Alpine.js which is already part of the Livewire stack.

## Implementation Details

### White Key Styling

```css
.piano-key-white {
    background: linear-gradient(to bottom, #ffffff 0%, #f8f8f8 100%);
    border: 1px solid #d0d0d0;
    border-bottom: 4px solid #b0b0b0;
    border-radius: 0 0 5px 5px;
    box-shadow: 
        inset 0 1px 0 rgba(255,255,255,0.8),
        inset 0 -1px 0 rgba(0,0,0,0.1),
        0 2px 3px rgba(0,0,0,0.1);
    transform-style: preserve-3d;
    transition: all 0.05s ease-out;
}

.piano-key-white:active,
.piano-key-white.pressed {
    transform: translateY(2px);
    border-bottom-width: 2px;
    background: linear-gradient(to bottom, #f0f0f0 0%, #e8e8e8 100%);
}
```

### Black Key Styling

```css
.piano-key-black {
    background: linear-gradient(to bottom, #333 0%, #000 100%);
    border-bottom: 4px solid #000;
    border-radius: 0 0 3px 3px;
    box-shadow: 
        inset 0 -1px 0 rgba(255,255,255,0.1),
        0 4px 6px rgba(0,0,0,0.3);
    z-index: 2;
    transform: translateZ(5px);
}

.piano-key-black:active,
.piano-key-black.pressed {
    transform: translateY(2px) translateZ(5px);
    border-bottom-width: 2px;
    background: linear-gradient(to bottom, #222 0%, #000 100%);
}
```

### Container Perspective

```css
.piano-container {
    perspective: 1000px;
    background: linear-gradient(to bottom, #2a2a2a 0%, #1a1a1a 100%);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 
        inset 0 2px 5px rgba(0,0,0,0.3),
        0 5px 10px rgba(0,0,0,0.2);
}
```

### Alpine.js Integration

```javascript
// For dynamic key press handling
Alpine.data('piano3d', () => ({
    pressedKeys: new Set(),
    
    pressKey(note) {
        this.pressedKeys.add(note);
    },
    
    releaseKey(note) {
        this.pressedKeys.delete(note);
    },
    
    isPressed(note) {
        return this.pressedKeys.has(note);
    }
}));
```

## Performance Considerations

- Use CSS transforms instead of position changes
- Minimize repaints with transform and opacity only
- Use will-change sparingly on interactive elements
- Batch DOM updates through Alpine.js reactivity
- Consider GPU acceleration with transform3d

## Browser Compatibility

- Modern browsers support all proposed CSS features
- Fallback to 2D appearance for older browsers
- Test on mobile Safari for touch interactions
- Verify performance on lower-end devices

## Integration Points

- Coordinate with existing PianoPlayer Livewire component
- Maintain compatibility with chord highlighting
- Preserve keyboard navigation functionality
- Ensure Tone.js audio playback remains synchronized

## External Dependencies

No new dependencies required - implementation uses existing:
- Tailwind CSS for utility classes
- Alpine.js for interactivity (included with Livewire)
- Modern CSS3 for 3D effects