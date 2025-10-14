# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12.x application for ChordHound, a piano chord generator. The application features dog-themed branding and provides an intuitive interface for creating and managing piano chord progressions.

## Development Commands

### Starting Development Environment
```bash
composer dev  # Runs Laravel server, queue, logs, and Vite concurrently
```

### Building Assets
```bash
pnpm run dev    # Start Vite dev server
pnpm run build  # Build for production
```

### Testing
```bash
php artisan test  # Run all tests
php artisan test --filter TestName  # Run specific test
composer test     # Alternative test command
```

### Database
```bash
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Reset and re-run migrations
php artisan db:seed       # Run database seeders
```

### Code Quality
```bash
./vendor/bin/pint          # Laravel Pint for code style
./vendor/bin/phpstan       # PHPStan for static analysis (if configured)
```

## Architecture Overview

### Frontend Stack
- **Livewire + Volt**: All interactive components use Livewire for reactivity
- **Flux UI**: Livewire's UI component library (not Material UI as specified in PRD)
- **Tailwind CSS v4**: Styling via Vite plugin
- **Alpine.js**: Available through Livewire for client-side interactions

### Key Directories
- `app/Livewire/`: Livewire components for auth, settings, and future chord components
- `resources/views/livewire/`: Livewire component views
- `resources/views/components/flux/`: Flux UI component overrides
- `routes/web.php`: Main application routes

### Authentication Architecture
The app uses Livewire components for all auth flows:
- Login: `app/Livewire/Login.php`
- Register: `app/Livewire/Register.php`
- Password Reset: `app/Livewire/ResetPassword.php`

## Implementation Notes from PRD

The following features need to be implemented:
1. **Chord Logic**: Up to 8 chords with tone, semitone, and inversion selection
2. **Blue Notes**: Calculate and highlight blue notes based on chord selections
3. **Authentication Methods**: Add Google OAuth, Magic Link, and Auth Code (currently only email/password)
4. **Chord Persistence**: Save/load chord sets for authenticated users
5. **Print Functionality**: Generate printable chord sheets
6. **UI Migration**: Consider migrating from Flux to Material UI as specified in PRD

## Database Considerations

New migrations needed for:
- `chord_sets` table: Store user's saved chord combinations
- `chord_set_chords` table: Individual chords within a set
- Consider using JSON columns for chord data (tone, semitone, inversion)

## Coding Standards

### PHP Files:
- **All new PHP files MUST include `declare(strict_types=1);` immediately after the opening `<?php` tag**
- This enforces strict type checking for scalar type declarations

Example:
```php
<?php

declare(strict_types=1);

namespace App\Models;
```

### Code Formatting:
- **Character set**: UTF-8
- **End of line**: LF (Unix-style)
- **Final newline**: Always insert
- **Indentation**: 4 spaces (2 for YAML files)
- **Trailing whitespace**: Always trim (except in Markdown files)
- **Laravel Pint**: Use as the default PHP formatter (`./vendor/bin/pint`)

## Testing Approach

### Testing Framework:
- Use Pest for all tests (not PHPUnit syntax)
- Feature tests should cover user flows
- Unit tests for chord calculation logic
- Database tests use in-memory SQLite

### Test Pattern:
```php
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

it('does something', function () {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Act
    $response = $this->post('/chords', ['data' => 'value']);
    
    // Assert
    $response->assertOk()
        ->assertJsonStructure(['id', 'created_at']);
});
```

### Testing Guidelines:
- Use Pest 3 style with `it()` functions (never use `test()`)
- Follow the Arrange-Act-Assert pattern
- Use database transactions for isolation
- Test both authenticated and guest states when applicable
- Verify response structure in API tests
- Use data providers with `->with()` for comprehensive coverage

## Laravel Conventions & Best Practices

### Naming Conventions:
- **Controllers**: Singular, PascalCase with `Controller` suffix (e.g., `ChordController`)
- **Models**: Singular, PascalCase (e.g., `Chord`, `ChordSet`)
- **Database Tables**: Plural, snake_case (e.g., `chords`, `chord_sets`)
- **Database Columns**: snake_case (e.g., `created_at`, `user_id`)
- **Routes**: Plural for resource routes, kebab-case (e.g., `/chords`, `/chord-sets`)
- **Blade Views**: snake_case (e.g., `show_chord.blade.php`)
- **Form Requests**: PascalCase with `Request` suffix (e.g., `StoreChordRequest`)
- **Livewire Components**: PascalCase (e.g., `ChordSelector`, `ChordDisplay`)

### Eloquent Conventions:
- **Relationships**: Use proper naming (e.g., `chords()` for hasMany, `user()` for belongsTo)
- **Scopes**: Prefix with `scope` (e.g., `scopeActive()`, `scopePublished()`)
- **Accessors/Mutators**: Use Laravel 11 attribute syntax with `Attribute` cast
- **Model Properties**: Always define `$fillable` or `$guarded`

### Service Classes:
- Create service classes for complex business logic (e.g., chord calculations)
- Keep controllers thin - delegate to services
- Place in `app/Services` directory
- Use dependency injection

Example structure for chord logic:
```php
// app/Services/ChordService.php
class ChordService
{
    public function calculateBlueNotes(array $chords): array
    {
        // Complex chord calculation logic
    }
}
```

## Common Patterns

### Creating Livewire Components
```bash
php artisan make:livewire ChordSelector
```

### Adding Routes
Routes should be added to `routes/web.php` with appropriate middleware:
```php
Route::middleware('auth')->group(function () {
    Route::get('/chords', [ChordController::class, 'index'])->name('chords.index');
    Route::post('/chords', [ChordController::class, 'store'])->name('chords.store');
});
```

### Using Flux Components
Flux components are used throughout for UI consistency. Example:
```blade
<flux:button variant="primary" wire:click="save">Save</flux:button>
<flux:input wire:model="chordName" label="Chord Name" />
```

## Performance Considerations:
- Use eager loading to prevent N+1 queries
- Implement proper caching strategies for chord calculations
- Use background jobs for heavy operations (e.g., generating print layouts)
- Consider caching blue note calculations

## Security Considerations:
- Never commit secrets or API keys
- Use environment variables for sensitive configuration
- Validate all user input for chord selections
- Implement proper authorization for saved chord sets

## Agent OS Documentation

### Product Context
- **Mission & Vision:** @.agent-os/product/mission.md
- **Technical Architecture:** @.agent-os/product/tech-stack.md
- **Development Roadmap:** @.agent-os/product/roadmap.md
- **Decision History:** @.agent-os/product/decisions.md

### Development Standards
- **Code Style:** @~/.agent-os/standards/code-style.md
- **Best Practices:** @~/.agent-os/standards/best-practices.md

### Project Management
- **Active Specs:** @.agent-os/specs/
- **Spec Planning:** Use `@~/.agent-os/instructions/create-spec.md`
- **Tasks Execution:** Use `@~/.agent-os/instructions/execute-tasks.md`

## Workflow Instructions

When asked to work on this codebase:

1. **First**, check @.agent-os/product/roadmap.md for current priorities
2. **Then**, follow the appropriate instruction file:
   - For new features: @.agent-os/instructions/create-spec.md
   - For tasks execution: @.agent-os/instructions/execute-tasks.md
3. **Always**, adhere to the standards in the files listed above

## Important Notes

- Product-specific files in `.agent-os/product/` override any global standards
- User's specific instructions override (or amend) instructions found in `.agent-os/specs/...`
- Always adhere to established patterns, code style, and best practices documented above.

## Chord Progression Guidance

### Chord Progression Reference Tables

The following chord progression tables provide comprehensive guidance for chord inversions across different musical keys and progression types:

#### Progression Tables
- **I-IV-V (Classic Rock/Blues)**
- **I-vi-IV-V (Pop Progression / 50s Doo-Wop)**
- **vi-IV-I-V (Alternative Pop)**
- **I-vi-ii-V (Jazz Standard)**
- **ii-V-I (Jazz Cadence)**

Each table includes:
- Key of the progression
- Chord names with specific note inversions
- Recommended voicings for different musical contexts

### Implementation Notes

- Use these tables as a reference for generating chord progressions
- Consider implementing a system that can dynamically generate chord inversions based on these patterns
- Validate that chord generation follows the specified note ranges and inversions
- Ensure that the chord generation logic supports transposition across different keys

## Chord Inversions Reference

### Chord Inversion Table
Here's a comprehensive reference for chord inversions across different musical keys:

#### Minor Chord Inversions
| Chord | Root Position | First Inversion | Second Inversion |
|-------|--------------|----------------|-----------------|
| Cm    | (C4-E♭4-G4)  | (E♭4-G4-C5)    | (G3-C4-E♭4)     |
| C♯m   | (C♯4-E4-G♯4) | (E3-G♯3-C♯4)   | (G♯3-C♯4-E4)    |
| Dm    | (D4-F4-A4)   | (F3-A3-D4)     | (A3-D4-F4)      |
| D♯m   | (D♯4-F♯4-A♯4)| (F♯3-A♯3-D♯4)  | (A♯3-D♯4-F♯4)   |
| Em    | (E4-G4-B4)   | (G3-B3-E4)     | (B3-E4-G4)      |
| Fm    | (F4-A♭4-C5)  | (A♭3-C4-F4)    | (C4-F4-A♭4)     |
| F♯m   | (F♯3-A3-C♯4) | (A3-C♯4-F♯4)   | (C♯4-F♯4-A4)    |
| Gm    | (G3-B♭3-D4)  | (B♭3-D4-G4)    | (D4-G4-B♭4)     |
| G♯m   | (G♯3-B3-D♯4) | (B3-D♯4-G♯4)   | (D♯4-G♯4-B4)    |
| Am    | (A3-C4-E4)   | (C4-E4-A4)     | (E3-A3-C4)      |
| A♯m   | (A♯3-C♯4-F4) | (C♯4-F4-A♯4)   | (F3-A♯3-C♯4)    |
| Bm    | (B3-D4-F♯4)  | (D4-F♯4-B4)    | (F♯3-B3-D4)     |

## Memory Notes

### Misc Clarifications
- If i mention Playwright I meant Dusk

### Development Server Clarifications
- Remember I am already running php artisan server, don't try to start another one. The access is at http://localhost:8000/.