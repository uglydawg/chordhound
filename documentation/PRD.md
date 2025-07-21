# Product Requirements Document (PRD) for Uglydawg's Piano Generator

## 1. Overview
**Uglydawg's Piano Generator** is a dog-themed piano chord application that allows users to select up to eight chords (with tone, semitone, and inversion options) and view a real-time piano keyboard display of the chords. Users can toggle blue note highlights, order chords, print chord diagrams, and save named chord sets with user accounts. The application features custom dog-themed branding with an orange color scheme and provides a simple, intuitive tool for musicians and learners to visualize and reference piano chords.

## 2. Goals

- Deliver an intuitive tool for generating and visualizing piano chord diagrams with unique dog-themed branding
- Support music education with accurate chord and blue note displays
- Enable users to save and manage chord sets with accounts
- Ensure fast, responsive performance on desktop and mobile using Laravel Octane
- Provide real-time updates using WebSocket broadcasting via Laravel Reverb

## 3. Features

### Chord Selection and Display
- Users can select up to **4 chords** (current implementation) via an interactive grid interface
- For each chord, users pick a root note from all twelve tones (C, C#, D, D#, E, F, F#, G, G#, A, A#, B) via a chord palette
- Users select chord types: major, minor, diminished, augmented
- Users select inversions (root, first, second) for each chord
- Blue notes are automatically calculated and highlighted based on chord progression context
- The chord grid displays in a timeline format with beat indicators
- Each chord shows a mini piano keyboard with highlighted notes
- Voice leading animations show note transitions between chords

### Save and Load Functionality
- Authenticated users can save chord progressions with custom names and descriptions
- Save dialog shows chord preview before saving
- Saved chord sets are displayed with chord previews and "Play" buttons
- Users can load saved chord sets back into the editor
- Chord sets show creation time and number of chords

### Print Functionality
- Print view automatically hides navigation and controls
- Only the chord display grid is printed
- Optimized print CSS for clean output

### User Account Creation and Authentication
Users can create accounts and log in via:
- **Email/Password**: Traditional registration with username support
- **Google Login**: OAuth integration with Google for single-sign-on (via Laravel Socialite)
- **Magic Link**: Users enter their email to receive a secure, one-time login link
- **Auth Code**: Users receive a one-time code via email/SMS for authentication
- **Phone Verification**: Optional phone number verification for enhanced security

Accounts allow users to:
- Save chord sets with custom names and descriptions
- View saved chord sets in "My Chord Sets" page
- Edit and delete saved chord sets
- Access account settings and profile management

## 4. User Flow

1. User lands on the homepage featuring Uglydawg's branding and sees the chord progression grid
2. User selects a chord position (1-4) and chooses root note, chord type, and inversion from the palette
3. The mini piano keyboard updates instantly showing the selected chord notes
4. Voice leading animations display transitions between chords when enabled
5. Blue notes are automatically calculated and highlighted in the progression
6. User can apply preset progressions (e.g., I-V-vi-IV) or create custom ones
7. Authenticated users can save chord progressions with names and descriptions
8. Users can print chord sheets or load previously saved progressions
9. Users log in via Email/Password, Google, Magic Link, or Auth Code to access saved features

## 5. Technical Specifications

### Core Framework
- **Laravel 12.x**: PHP framework for backend routing, logic, and data processing
- **Laravel Octane**: High-performance application serving with FrankenPHP server
- **SQLite**: Lightweight database for user accounts and saved chord sets

### Frontend Stack
- **Livewire + Volt**: Reactive components for real-time updates without page reloads
- **Flux UI**: Livewire's UI component library (instead of Material UI)
- **Tailwind CSS v4**: Modern utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework (integrated with Livewire)

### Laravel Packages
- **Laravel Reverb**: WebSocket broadcasting for real-time features
- **Laravel Echo**: Frontend WebSocket integration
- **Laravel Socialite**: OAuth authentication (Google integration)
- **Laravel Telescope**: Debug assistant for local development
- **Laravel Pulse**: Application performance monitoring
- **Laravel Cashier**: Subscription billing with Stripe
- **Laravel Pint**: Code formatting tool (configured as pre-commit hook)

### Key Features Implementation
- **Chord Logic**: Custom ChordService calculates notes, inversions, and blue notes
- **Display Rendering**: SVG-based piano keyboards with Livewire reactivity
- **Print Functionality**: CSS-optimized print view with hidden UI elements
- **Authentication**: Multiple methods including OAuth, Magic Links, and Auth Codes
- **Data Validation**: Laravel validation rules ensure data integrity
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Voice Leading**: Automatic calculation of optimal chord inversions

### Development Tools
- **Pest**: Modern PHP testing framework
- **Vite**: Fast asset bundling and hot module replacement
- **Composer**: PHP dependency management
- **NPM**: JavaScript package management

## 6. Constraints

- Keep the interface simple and intuitive for musicians of all levels
- Support modern browsers (Chrome, Firefox, Safari, Edge)
- Page load time under one second with Laravel Octane
- SQLite database for minimal setup and maintenance
- Maximum 4 chords per progression (current implementation)
- Dog-themed branding must remain consistent throughout

## 7. Success Metrics

- Users can generate a chord progression in under 10 seconds
- Page performance score above 90 on Google Lighthouse
- Positive user feedback with focus on ease of use
- 50% of authenticated users save at least one chord set
- Sub-second response times for chord updates
- Zero downtime deployments with Octane

## 8. Current Implementation Status

### Completed Features
- ✅ Dog-themed branding with custom logo and orange color scheme
- ✅ 4-chord progression grid with interactive selection
- ✅ Chord palette with all 12 tones and 4 chord types
- ✅ Inversion selection (root, first, second)
- ✅ Mini piano keyboard displays for each chord
- ✅ Blue note calculation and highlighting
- ✅ Voice leading animations between chords
- ✅ Preset chord progressions (I-IV-V, I-V-vi-IV, etc.)
- ✅ Save/load functionality for authenticated users
- ✅ Multiple authentication methods (Email, Google, Magic Link, Auth Code)
- ✅ Print-optimized CSS
- ✅ Laravel Octane integration for performance
- ✅ Laravel Reverb for WebSocket support
- ✅ Laravel Telescope for debugging
- ✅ Laravel Pulse for monitoring
- ✅ Laravel Cashier for billing infrastructure
- ✅ Laravel Pint pre-commit hook

### Future Enhancements
- Expand to 8-chord progressions as originally specified
- MIDI export functionality
- Audio playback of chord progressions
- Chord progression sharing via public links
- Mobile app development
- Advanced music theory features (scales, modes)
- Subscription tiers using Laravel Cashier
