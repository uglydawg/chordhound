Product Requirements Document (PRD) for Piano Chord Generator Website
1. Overview
This website allows users to select up to eight chords (with tone, semitone, and inversion options) and view a real-time piano keyboard display of the chords. Users can toggle blue note highlights, order chords, print chord diagrams, and save named chord sets with user accounts. The goal is to provide a simple, intuitive tool for musicians and learners to visualize and reference piano chords.
2. Goals

Deliver an intuitive tool for generating and visualizing piano chord diagrams.
Support music education with accurate chord and blue note displays.
Enable users to save and manage chord sets with accounts.
Ensure fast, responsive performance on desktop and mobile.

3. Features
Chord Selection and Display

Users can select up to eight chords via a dropdown menu with chord types (e.g., major, minor, seventh, diminished, augmented, suspended).
For each chord, users pick a root note from the seven tones (A, B, C, D, E, F, G) and semitone (natural, sharp, flat) via dropdowns or buttons.
Users select inversions (root, first, second for triads; up to third for seventh chords) for each chord.
A “Highlight Blue Note” toggle identifies and highlights a blue note (e.g., flattened third, fifth, or seventh, based on chord context) in blue on the piano keyboard visual.
Duplicates (same chord-tone-semitone-inversion combo) are blocked with an error message like “Chord already added!”
The chord list is orderable via drag-and-drop, and the piano keyboard visual (SVG or canvas) updates instantly to reflect selected chords, inversions, and blue notes (if toggled).
A “Show Display” button toggles a full-screen keyboard view, showing chords in sequence with names, inversion labels, and blue note highlights (if enabled).

Print Functionality

A “Print Chords” button generates a one-page printout with the ordered chord list, including chord names, tones, semitones, inversions, and keyboard diagrams. Blue note highlights are included if toggled.

User Account Creation and Authentication

Users can create accounts and log in via:
Google Login: OAuth integration with Google for single-sign-on.
Magic Link: Users enter their email to receive a secure, one-time login link.
Auth Code: Users receive a one-time code via email for authentication.


Accounts allow users to save chord sets with custom names (e.g., “My Blues Progression”) and access them later.
Saved chord sets are listed in a user dashboard, where they can be edited, renamed, or deleted.

4. User Flow

User lands on the homepage, sees dropdowns/buttons for chord type, tone, semitone, and inversion, plus a blue note toggle.
User selects a chord (e.g., C Major, first inversion), and the piano keyboard visual updates instantly to highlight the chord’s notes.
User adds up to eight chords, drags to reorder, and toggles blue notes as needed; duplicates are blocked.
User clicks “Show Display” for a full-screen view of all chords or “Print Chords” for a printable output.
Logged-in users can save chord sets with a custom name via a “Save” button.
Users log in via Google, Magic Link, or auth code to access saved chord sets in a dashboard.

5. Technical Specifications

Framework: Laravel (PHP) for backend routing, logic, and data processing.
Frontend: Livewire for reactive, real-time component rendering, enabling instant chord display updates without page reloads.
UI Library: Material UI components for a modern, consistent look, including dropdowns (for chord type, tone, semitone, inversion), buttons (“Show Display,” “Print Chords”), toggles (blue note), and drag-and-drop for chord ordering.
Database: SQLite for lightweight storage of user accounts and saved chord sets.
Chord Logic: Laravel service calculates chord notes, inversions, and blue notes using a music theory library (e.g., Tonic) or custom logic.
Display Rendering: Piano keyboard visual uses SVG or canvas, styled with Material UI, updated via Livewire to highlight chord notes and blue notes.
Print Functionality: Laravel generates a print-friendly HTML view, styled with Material UI’s print-specific CSS.
Authentication: Laravel handles Google OAuth, Magic Link (email-based), and auth code (email OTP) for secure login.
Data Validation: Laravel validates inputs to prevent duplicates and ensures data integrity for saved chord sets.
Responsive Design: Material UI ensures mobile- and desktop-friendly layouts for the chord list and keyboard visual.
Accessibility: Keyboard navigation and screen reader support via Material UI’s ARIA-compliant components.

6. Constraints

Keep the interface simple, avoiding complex features beyond chord selection, display, and saving.
Support modern browsers (Chrome, Firefox, Safari).
Page load time under two seconds.
SQLite database for minimal setup and maintenance.

7. Success Metrics

Users can generate a chord diagram in under five seconds.
Positive feedback via an onsite survey (e.g., “Was this helpful?”) with 80%+ positive responses.
500 unique users in the first month post-launch.
50% of logged-in users save at least one chord set.
