# ChordHound

A Laravel-based piano chord application that helps musicians visualize and play chord progressions with an intuitive interface.

## Features

- **Visual Piano Display**: Interactive piano keyboard showing chord notes
- **Chord Progression Builder**: Create and save custom chord progressions
- **Dog-Themed Branding**: Custom ChordHound branding with orange color scheme
- **Multiple Authentication Methods**: Email/Password, Google OAuth, Magic Links, and Auth Codes
- **Save/Load Functionality**: Authenticated users can save and name their chord progressions
- **Voice Leading Optimization**: Automatic chord inversion calculations for smooth transitions
- **Print Support**: Generate printable chord sheets
- **Real-time Updates**: WebSocket support via Laravel Reverb
- **Performance Optimized**: Powered by Laravel Octane with FrankenPHP

## Tech Stack

### Backend
- **Laravel 12.x** - PHP framework
- **Laravel Octane** - High-performance application serving with FrankenPHP
- **Laravel Reverb** - WebSocket broadcasting server
- **Laravel Socialite** - OAuth authentication (Google)
- **Laravel Telescope** - Debug assistant (local only)
- **Laravel Pulse** - Application performance monitoring
- **Laravel Cashier** - Subscription billing with Stripe
- **SQLite** - Database

### Frontend
- **Livewire + Volt** - Reactive components
- **Flux UI** - Livewire's UI component library
- **Tailwind CSS v4** - Styling
- **Alpine.js** - Client-side interactions (via Livewire)
- **Laravel Echo** - WebSocket client

### Development Tools
- **Laravel Pint** - Code formatting (configured as pre-commit hook)
- **Pest** - Testing framework
- **Vite** - Asset bundling

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd piano-chords
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node dependencies:
```bash
npm install
```

4. Copy environment file and generate app key:
```bash
cp .env.example .env
php artisan key:generate
```

5. Run database migrations:
```bash
php artisan migrate
```

6. Start the development server:
```bash
composer dev
```

This will start:
- Laravel development server
- Queue worker
- Log viewer
- Vite dev server

## Development Commands

### Start Development Environment
```bash
composer dev  # Runs Laravel server, queue, logs, and Vite concurrently
```

### Run Tests
```bash
php artisan test  # Run all tests
php artisan test --filter TestName  # Run specific test
```

### Code Formatting
```bash
./vendor/bin/pint  # Format PHP code
```

### Database
```bash
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Reset and re-run migrations
```

### Build Assets
```bash
npm run dev    # Start Vite dev server
npm run build  # Build for production
```

## Laravel Package Features

### Octane (High Performance)
Laravel Octane is configured with FrankenPHP for improved application performance. The configuration is in `config/octane.php`.

### Reverb (WebSockets)
Laravel Reverb provides WebSocket broadcasting capabilities. Configure in `.env`:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
```

### Telescope (Debugging)
Access the Telescope dashboard at `/telescope` (local environment only).

### Pulse (Monitoring)
Access the Pulse dashboard at `/pulse` for application performance monitoring.

### Cashier (Billing)
Laravel Cashier is configured for Stripe. Add your Stripe keys to `.env`:
```env
STRIPE_KEY=your-stripe-key
STRIPE_SECRET=your-stripe-secret
```

## Project Structure

```
├── app/
│   ├── Http/Controllers/    # Request controllers
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Providers/           # Service providers
├── resources/
│   ├── views/               # Blade templates
│   │   ├── livewire/        # Livewire component views
│   │   └── components/      # Blade components
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── routes/
│   └── web.php              # Web routes
├── database/
│   └── migrations/          # Database migrations
└── tests/                   # Test files
```

## Key Components

### ChordGrid
Main component for chord progression editing (`app/Livewire/ChordGrid.php`)

### ChordDisplay
2x2 grid display of chord details (`app/Livewire/ChordDisplay.php`)

### ChordService
Core business logic for chord calculations (`app/Services/ChordService.php`)

## Authentication

The application supports multiple authentication methods:
- **Email/Password**: Traditional registration and login
- **Google OAuth**: Sign in with Google account
- **Magic Links**: Passwordless email authentication
- **Auth Codes**: PIN-based authentication

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (Laravel Pint will run automatically)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Code Style

This project uses Laravel Pint for code formatting. A pre-commit hook is configured to automatically format PHP files before each commit.

## Testing

Tests are written using Pest. Follow the existing patterns:
- Use `it()` functions, not `test()`
- Use database transactions for isolation
- Follow Arrange-Act-Assert pattern

## License

[Add your license information here]