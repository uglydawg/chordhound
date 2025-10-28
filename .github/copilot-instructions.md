# ChordHound Copilot Instructions

## Project Overview
ChordHound is a Laravel 12.x application for piano chord generation and music education. It features dog-themed branding, real-time chord visualization, and a comprehensive learning system with gamification.

## Architecture
- **Backend**: Laravel 12.x with Octane/FrankenPHP for high-performance serving
- **Frontend**: Livewire + Volt for reactive components, Flux UI library, Tailwind CSS v4
- **Database**: SQLite with focus on chord storage and learning progress tracking
- **Real-time**: Laravel Reverb for WebSocket broadcasting, Echo for client-side
- **Audio**: Tone.js for piano sound synthesis

## Development Workflow

### Starting Development
```bash
composer dev  # Runs Laravel server, queue worker, and Vite concurrently
```

### Key Commands
- `php artisan test` - Run Pest test suite
- `./vendor/bin/pint` - Laravel Pint code formatting (pre-commit hook)
- `pnpm run dev` - Vite development server
- `php artisan migrate:fresh` - Reset database with fresh migrations

## Code Standards

### PHP Files
- **MUST** include `declare(strict_types=1);` after opening `<?php` tag
- Use Laravel Pint for formatting (4 spaces, UTF-8, LF endings)
- Example:
```php
<?php

declare(strict_types=1);

namespace App\Services;
```

## Key Architectural Patterns

### Livewire Component Structure
All interactive features use Livewire components in `app/Livewire/`:
- `ChordDisplay` - Main chord visualization grid
- `ChordSelector` - Chord palette selection interface  
- `InteractivePiano` - Visual piano keyboard display
- `LearningDashboard` - Student progress tracking
- `LessonViewer` - Interactive lesson content

### Service Layer
Core business logic in `app/Services/`:
- `ChordService` - Chord note calculations, inversions, voice leading
- `LearningPathService` - Progress tracking, lesson sequencing
- `ProgressTrackingService` - Analytics and skill assessment
- `ChordVoicingService` - Automatic chord inversion optimization

### Authentication
Multiple auth methods via `app/Http/Controllers/SocialAuthController.php`:
- Email/password (default Laravel)
- Google OAuth (Laravel Socialite)
- Magic links via email
- Auth codes via email/SMS

## Data Models

### Chord System
- Chords stored as JSON with `root_note`, `chord_type`, `inversion`
- ChordService calculates MIDI note arrays for display
- Voice leading optimizes chord transitions automatically

### Learning System (8-table structure)
- `lesson_modules` → `lessons` → `quizzes` → `quiz_questions`
- Progress tracking via `lesson_progress`, `quiz_attempts`, `quiz_answers`
- Gamification through `achievements` and `user_achievements`

## Testing

### Structure
- **Feature tests**: `/tests/Feature/` using RefreshDatabase
- **Browser tests**: `/tests/Browser/` using Laravel Dusk
- **Unit tests**: `/tests/Unit/` for service layer logic

### Key Test Patterns
```php
// Livewire component testing
test('chord display updates when chord selected', function () {
    Livewire::test(ChordDisplay::class)
        ->call('selectChord', 1, 'C', 'major', 'root')
        ->assertSet('chords.1.root_note', 'C');
});
```

## Frontend Integration

### Livewire + Alpine.js
- Livewire handles server-side reactivity
- Alpine.js for client-side interactions (via Livewire)
- Flux UI components in `resources/views/components/flux/`

### Audio Integration
- Tone.js integrated via Vite for piano sound synthesis
- Piano component connects MIDI note data to audio playback

## Database Patterns

### JSON Storage
Chord progressions stored as JSON arrays:
```php
// Migration pattern
$table->json('chord_data'); // Stores chord configurations
$table->json('settings')->nullable(); // User preferences
```

### Progress Tracking
Learning system uses time-based progress tracking:
```php
'time_spent' => 'integer', // Seconds spent on lesson
'completion_percentage' => 'decimal:5,2', // 0.00 to 100.00
```

## Common Gotchas

### Chord Calculations
- ChordService expects 12-tone equal temperament (chromatic scale)
- Inversion calculations assume specific octave ranges (default: 3-5)
- Blue notes calculated relative to chord progression context, not individual chords

### Livewire State Management
- Chord state stored in arrays indexed by position (1-4, not 0-3)
- Use `#[On('chordSelected')]` attribute for component communication
- Piano component listens for chord updates via Livewire events

### Performance Considerations
- ChordService calculations cached when possible
- Learning progress updates batched to avoid excessive database writes
- Livewire components use lazy loading for heavy computational tasks

## File Patterns
- Controllers: Thin, delegate to services
- Services: Stateless, focused on single responsibility
- Livewire components: Handle UI state and user interactions
- Models: Use strict typing, JSON casts for complex data