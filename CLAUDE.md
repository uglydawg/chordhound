# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12.x application for a Piano Chord Generator. Currently, it's a base Laravel installation with authentication scaffolding that needs the piano chord functionality implemented according to the PRD.

## Development Commands

### Starting Development Environment
```bash
composer dev  # Runs Laravel server, queue, logs, and Vite concurrently
```

### Building Assets
```bash
npm run dev    # Start Vite dev server
npm run build  # Build for production
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