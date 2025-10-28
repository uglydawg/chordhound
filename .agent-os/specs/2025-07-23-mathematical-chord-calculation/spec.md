# Spec Requirements Document

> Spec: Mathematical Chord Calculation Engine
> Created: 2025-07-23
> Status: Planning

## Overview

Replace the current hardcoded chord voicing tables with a mathematical algorithm that dynamically calculates chord notes based on intervals and voice leading principles. This will enable flexible chord generation, easier addition of new chord types, and include bass note calculation one octave below the root.

## User Stories

### Dynamic Chord Calculation

As a music learner, I want chord voicings to be calculated mathematically based on the starting position, so that I can explore different voicings and understand the underlying music theory principles.

The user selects a chord (e.g., C Major) and a starting position (e.g., G4). The system calculates the closest notes to form the chord using proper voice leading, automatically adds a bass note one octave below the root, and displays all notes (e.g., C3 C4 E4 G4) with the keys depressed and appropriate sounds playing.

### Chord Note Visualization

As a piano student, I want to see the note names displayed prominently when keys are pressed, so that I can learn note recognition while practicing chord progressions.

When a chord is played, large letter displays appear below the piano showing each note name (C, E, G, etc.) while the corresponding keys are depressed, helping reinforce the connection between visual keys and note names.

## Spec Scope

1. **Mathematical Chord Engine** - Algorithm-based chord calculation using interval mathematics instead of hardcoded tables
2. **Bass Note Addition** - Automatic calculation and addition of bass notes one octave below the chord root
3. **Voice Leading Optimization** - Mathematical approach to finding the closest chord voicing from any starting position
4. **Note Display Feature** - Large, prominent display of note names below the piano during chord playback
5. **Audio Integration** - Pass calculated notes (including bass) to the piano player for synchronized audio output

## Out of Scope

- Modification of the existing 4-chord progression interface
- Changes to authentication or user management systems
- Advanced chord types (7ths, 9ths) - these will be easier to add later with the new system
- MIDI export functionality
- Changes to the print functionality

## Expected Deliverable

1. Mathematical chord calculation function that replaces hardcoded voicing tables and produces consistent, musically correct voicings
2. Visual note name display that appears below piano keys when chords are played, enhancing the educational value
3. Integrated bass note calculation that automatically adds appropriate bass notes to all chord voicings

## Spec Documentation

- Tasks: @.agent-os/specs/2025-07-23-mathematical-chord-calculation/tasks.md
- Technical Specification: @.agent-os/specs/2025-07-23-mathematical-chord-calculation/sub-specs/technical-spec.md
- Tests Specification: @.agent-os/specs/2025-07-23-mathematical-chord-calculation/sub-specs/tests.md