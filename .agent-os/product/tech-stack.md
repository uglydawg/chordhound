# Technical Stack

> Last Updated: 2025-07-23
> Version: 1.0.0

## Backend Framework

**Laravel 12.x** with PHP 8.2+
- Modern PHP framework with strict typing enforcement
- Laravel Octane for high-performance serving with FrankenPHP
- Rich ecosystem of first-party packages

## Database System

**SQLite**
- Lightweight, serverless database perfect for the application scale
- Supports complex relational queries for chord set management
- Easy backup and deployment

## Frontend Framework

**Livewire + Volt**
- Server-side rendering with reactive components
- Real-time updates without page reloads
- Simplified development with PHP-based components

## JavaScript Framework

**Alpine.js** (integrated with Livewire)
- Lightweight client-side reactivity
- Seamless integration with Livewire components
- Minimal JavaScript footprint

## Import Strategy

**Vite**
- Fast asset bundling and hot module replacement
- ES modules support
- Development server with instant updates

## CSS Framework

**Tailwind CSS v4**
- Utility-first CSS framework
- Modern design system with custom color schemes
- Responsive design capabilities

## UI Component Library

**Flux UI**
- Livewire's official UI component library
- Pre-built components with consistent design
- Seamless Livewire integration

## Fonts Provider

**System Fonts**
- Modern font stack using system fonts
- Fast loading with no external dependencies
- Consistent cross-platform appearance

## Icon Library

**Heroicons** (via Flux UI)
- SVG-based icon system
- Consistent design language
- Built into Flux components

## Application Hosting

**Production: VPS/Cloud Server**
- Flexible deployment options
- Laravel Octane optimization
- Scalable infrastructure

## Database Hosting

**Co-located with Application**
- SQLite database stored with application
- Simplified deployment and backup
- No external database dependencies

## Asset Hosting

**Application Server**
- Static assets served directly
- Vite-optimized asset pipeline
- CDN capability for future scaling

## Deployment Solution

**Git-based Deployment**
- Version-controlled deployments
- Automated testing pipeline
- Environment-specific configurations

## Laravel Packages

- **Laravel Reverb:** WebSocket broadcasting for real-time features
- **Laravel Echo:** Frontend WebSocket integration  
- **Laravel Socialite:** OAuth authentication (Google integration)
- **Laravel Telescope:** Debug assistant for development
- **Laravel Pulse:** Application performance monitoring
- **Laravel Cashier:** Subscription billing with Stripe
- **Laravel Pint:** Code formatting and linting

## Development Tools

- **Pest:** Modern PHP testing framework with elegant syntax
- **Composer:** PHP dependency management
- **NPM:** JavaScript package management
- **Vite:** Asset bundling and development server

## Audio Processing

**Tone.js**
- Web Audio API abstraction for chord playback
- Piano sound synthesis
- Audio scheduling and timing

## Code Quality

- **Laravel Pint:** Automated code formatting
- **Strict PHP typing:** `declare(strict_types=1)` enforcement
- **Pest testing:** Comprehensive test coverage
- **Database transactions:** Test isolation strategy

## Code Repository URL

*To be configured during deployment*