# ChordHound Suggested Commands

## Development Environment

### Start Development (Recommended)
```bash
composer dev  # Runs Laravel server, queue, logs, and Vite concurrently
```
This single command starts everything needed for development.

### Individual Services
```bash
php artisan serve      # Start Laravel development server
php artisan queue:work # Start queue worker
php artisan pail       # View application logs
npm run dev           # Start Vite dev server
```

## Testing

### PHP Tests
```bash
php artisan test                    # Run all tests
php artisan test --filter TestName  # Run specific test
composer test                       # Alternative test command
./vendor/bin/pest                   # Run Pest directly
```

### Browser Tests (Dusk)
```bash
php artisan dusk                                            # Run all browser tests
php artisan dusk tests/Browser/DuskEnvironmentTest.php      # Run specific test
php artisan dusk:chrome-driver --detect                     # Update Chrome driver
./run-chord-tests.sh                                        # Run comprehensive chord tests
./run-minor-chord-tests.sh                                  # Run minor chord tests only
```

## Code Quality

### Formatting & Linting
```bash
./vendor/bin/pint          # Format PHP code (Laravel Pint)
./vendor/bin/pint --test   # Check code style without changing
```

### Static Analysis
```bash
./vendor/bin/phpstan analyse  # Run PHPStan (if configured)
```

## Database

### Migrations
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh        # Reset and re-run all migrations
php artisan migrate:rollback     # Rollback last migration batch
php artisan migrate:status       # Check migration status
```

### Seeding
```bash
php artisan db:seed                          # Run all seeders
php artisan db:seed --class=UserSeeder      # Run specific seeder
```

## Asset Management

### Building Assets
```bash
npm run dev    # Start Vite development server with HMR
npm run build  # Build assets for production
```

## Artisan Commands

### Make Commands
```bash
php artisan make:livewire ComponentName      # Create Livewire component
php artisan make:controller ControllerName   # Create controller
php artisan make:model ModelName -m          # Create model with migration
php artisan make:service ServiceName          # Create service class
php artisan make:test TestName               # Create test file
```

### Cache Management
```bash
php artisan config:cache    # Cache configuration
php artisan config:clear    # Clear config cache
php artisan route:cache     # Cache routes
php artisan route:clear     # Clear route cache
php artisan view:cache      # Cache views
php artisan view:clear      # Clear view cache
php artisan cache:clear     # Clear application cache
```

## Package-Specific Commands

### Laravel Telescope (Debug)
```bash
php artisan telescope:prune  # Prune old entries
php artisan telescope:clear  # Clear all entries
```

### Laravel Pulse (Monitoring)
```bash
php artisan pulse:check  # Check pulse configuration
```

### Laravel Octane
```bash
php artisan octane:start  # Start Octane server
php artisan octane:stop   # Stop Octane server
```

## Git Commands (Linux)
```bash
git status                    # Check repository status
git add .                     # Stage all changes
git commit -m "message"       # Commit changes
git push                      # Push to remote
git pull                      # Pull from remote
git checkout -b branch-name   # Create new branch
```

## System Utilities (Linux)
```bash
ls -la              # List files with details
cd directory        # Change directory
pwd                 # Print working directory
grep -r "pattern"   # Search for pattern recursively
find . -name "*.php" # Find PHP files
tail -f storage/logs/laravel.log  # Follow Laravel logs
```

## Production Commands
```bash
php artisan down                        # Put application in maintenance mode
php artisan up                          # Bring application out of maintenance
php artisan optimize                    # Optimize for production
php artisan queue:restart               # Restart queue workers
composer install --no-dev --optimize-autoloader  # Production install
```

## When Task is Completed
After completing a coding task, always run:
1. `./vendor/bin/pint` - Format code
2. `php artisan test` - Run tests
3. `npm run build` - Build assets if frontend changes were made