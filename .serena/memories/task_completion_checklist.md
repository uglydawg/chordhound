# ChordHound Task Completion Checklist

When you complete a coding task, follow these steps in order:

## 1. Code Formatting
```bash
./vendor/bin/pint
```
Run Laravel Pint to ensure code follows project standards. This is configured as a pre-commit hook but should be run manually during development.

## 2. Run Tests
```bash
php artisan test
```
Ensure all tests pass. If you modified specific functionality, also run targeted tests:
- For specific test: `php artisan test --filter TestName`
- For browser tests: `php artisan dusk`

## 3. Build Assets (if frontend changes)
```bash
npm run build
```
Only necessary if you modified JavaScript, CSS, or other frontend assets.

## 4. Check for Type Errors
Verify that all new PHP files include `declare(strict_types=1);` after the opening `<?php` tag.

## 5. Verify Database Changes
If you made database changes:
- Ensure migrations are reversible
- Test rollback: `php artisan migrate:rollback`
- Re-run migrations: `php artisan migrate`

## 6. Clear Caches (if configuration changed)
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 7. Manual Testing
- Test the feature in the browser at http://localhost:8000
- Verify responsive design on mobile viewports
- Check browser console for JavaScript errors

## 8. Documentation
- Update relevant documentation if APIs or major features changed
- Add PHPDoc comments for new methods/classes
- Update CLAUDE.md if new patterns or conventions were introduced

## Important Reminders
- NEVER commit sensitive data or API keys
- Ensure all user inputs are validated
- Follow existing code patterns and conventions
- Use service classes for complex business logic
- Keep controllers thin