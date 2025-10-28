# Spec Requirements Document

> Spec: Bass Note Enhancement
> Created: 2025-07-23
> Status: Planning

## Overview

Enhance the chord playback functionality to include bass notes played two octaves below the root note of each chord, creating a fuller, more professional sound that better represents how chords are typically played on piano with left-hand bass accompaniment.

## User Stories

### Piano Student Learning Experience

As a piano student, I want to hear bass notes when chords are played, so that I can better understand how chords sound in actual piano performance with left-hand bass accompaniment.

Currently, when playing chords through the application, only the chord notes themselves are played in their designated octave. This creates a thin sound that doesn't represent how pianists typically play chords with bass notes in the left hand. Students learning piano need to hear the fuller sound that includes bass reinforcement to develop proper musical understanding and to connect what they're learning in the app with real piano playing techniques.

### Music Educator Teaching Tool

As a music educator, I want the chord playback to include bass notes, so that I can demonstrate proper piano voicing and help students understand the relationship between bass notes and chord harmonies.

Teachers currently have to explain separately that the left hand typically plays bass notes while demonstrating chords. Having the application automatically include bass notes during playback would create a more accurate representation of piano performance, making it easier to teach concepts like root position bass, chord inversions with consistent bass, and the importance of bass line movement in chord progressions.

## Spec Scope

1. **Bass Note Generation** - Automatically calculate and play the root note two octaves below the chord's root when any chord is played
2. **Audio Integration** - Integrate bass notes with existing Tone.js chord playback system
3. **Volume Balance** - Ensure bass notes are appropriately balanced with chord notes for natural sound
4. **Playback Timing** - Synchronize bass notes with chord playback for simultaneous attack
5. **Note Duration** - Match bass note duration to chord duration for consistent sustain

## Out of Scope

- Adding walking bass lines or bass patterns
- Allowing users to customize which bass note is played
- Creating separate controls for bass volume
- Adding bass note visualization on a separate piano keyboard
- Implementing different bass octave options (only two octaves lower)
- Adding bass notes to the printed chord sheets

## Expected Deliverable

1. When any chord is played, the root note sounds two octaves below simultaneously with the chord
2. Bass notes blend naturally with chord voicings without overpowering
3. All chord playback features (single chord, progression playback) include bass notes
4. Performance remains smooth without audio glitches or delays

## Spec Documentation

- Tasks: @.agent-os/specs/2025-07-23-bass-note-enhancement/tasks.md
- Technical Specification: @.agent-os/specs/2025-07-23-bass-note-enhancement/sub-specs/technical-spec.md
- Tests Specification: @.agent-os/specs/2025-07-23-bass-note-enhancement/sub-specs/tests.md