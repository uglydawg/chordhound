# ChordHound Code Style & Conventions

## PHP Files
- **MANDATORY**: All new PHP files MUST include `declare(strict_types=1);` immediately after `<?php`
- **Namespace**: Follow PSR-4 autoloading standard
- **Classes**: Located in appropriate directories (Controllers, Models, Services, Livewire)

## Code Formatting
- **Character set**: UTF-8
- **End of line**: LF (Unix-style)
- **Final newline**: Always insert
- **Indentation**: 4 spaces (2 for YAML files)
- **Trailing whitespace**: Always trim (except in Markdown files)
- **Laravel Pint**: Default PHP formatter (`./vendor/bin/pint`)

## Laravel Naming Conventions
- **Controllers**: Singular, PascalCase with `Controller` suffix (e.g., `ChordController`)
- **Models**: Singular, PascalCase (e.g., `Chord`, `ChordSet`)
- **Database Tables**: Plural, snake_case (e.g., `chords`, `chord_sets`)
- **Database Columns**: snake_case (e.g., `created_at`, `user_id`)
- **Routes**: Plural for resource routes, kebab-case (e.g., `/chords`, `/chord-sets`)
- **Blade Views**: snake_case (e.g., `show_chord.blade.php`)
- **Form Requests**: PascalCase with `Request` suffix (e.g., `StoreChordRequest`)
- **Livewire Components**: PascalCase (e.g., `ChordSelector`, `ChordDisplay`)
- **Jobs**: Descriptive PascalCase (e.g., `ProcessChordCalculation`)
- **Events**: Past tense or noun (e.g., `ChordSaved`, `ChordCreated`)
- **Listeners**: Present tense action (e.g., `SendChordNotification`)
- **Middleware**: PascalCase (e.g., `EnsureUserIsTeacher`)
- **Traits**: Descriptive adjective or verb (e.g., `HasChords`, `Searchable`)

## Architecture Patterns
- **Service Classes**: Complex business logic in `app/Services/` directory
- **Thin Controllers**: Controllers delegate to services
- **Dependency Injection**: Use Laravel's service container
- **Eloquent Conventions**: 
  - Relationships: `posts()` for hasMany, `user()` for belongsTo
  - Scopes: Prefix with `scope` (e.g., `scopeActive()`)
  - Model Properties: Always define `$fillable` or `$guarded`

## Testing Conventions (CRITICAL)
- **NEVER use RefreshDatabase, DatabaseMigrations, or DatabaseTransactions traits!**
- **NEVER use Schema::create() or manually create tables in tests!**
- **NEVER run migrations in test files!**
- Use Pest 3 style with `it()` functions (never use `test()`)
- Follow Arrange-Act-Assert pattern
- Use database transactions for isolation:
  ```php
  beforeEach(function () {
      DB::beginTransaction();
  });
  
  afterEach(function () {
      DB::rollBack();
  });
  ```
- Use `Passport::actingAs($user)` for authentication in API tests
- All API routes should start with `/v3` (if applicable)

## Security Best Practices
- Never commit secrets or API keys
- Use environment variables for sensitive configuration
- Validate all user input
- Use database transactions for data integrity