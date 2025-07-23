#!/bin/sh

# ChordHound Docker Entrypoint Script
# Handles initialization and startup tasks

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log() {
    echo -e "${BLUE}[ChordHound Init]${NC} $1"
}

error() {
    echo -e "${RED}[ChordHound Error]${NC} $1" >&2
}

warn() {
    echo -e "${YELLOW}[ChordHound Warning]${NC} $1"
}

success() {
    echo -e "${GREEN}[ChordHound Success]${NC} $1"
}

# Wait for dependencies
wait_for_database() {
    log "Checking database availability..."
    
    if [ "$DB_CONNECTION" = "sqlite" ]; then
        # Ensure SQLite database file exists
        DB_DIR=$(dirname "$DB_DATABASE")
        if [ ! -d "$DB_DIR" ]; then
            log "Creating database directory: $DB_DIR"
            mkdir -p "$DB_DIR"
        fi
        
        if [ ! -f "$DB_DATABASE" ]; then
            log "Creating SQLite database file: $DB_DATABASE"
            touch "$DB_DATABASE"
            chmod 664 "$DB_DATABASE"
            chown www-data:www-data "$DB_DATABASE" 2>/dev/null || true
        fi
        
        success "SQLite database is ready"
    else
        # For other database types, we would implement connection testing here
        log "Database connection type: $DB_CONNECTION"
    fi
}

# Initialize Laravel application
initialize_laravel() {
    log "Initializing Laravel application..."
    
    # Generate app key if not set
    if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:REPLACE_WITH_GENERATED_KEY" ]; then
        warn "APP_KEY not set, generating new key..."
        php artisan key:generate --force
    fi
    
    # Clear and cache config
    log "Optimizing Laravel configuration..."
    php artisan config:clear
    php artisan config:cache
    
    # Run migrations
    log "Running database migrations..."
    php artisan migrate --force
    
    # Create storage link
    log "Creating storage link..."
    php artisan storage:link || warn "Storage link already exists or failed to create"
    
    # Clear and cache routes
    log "Optimizing routes..."
    php artisan route:clear
    php artisan route:cache
    
    # Clear and cache views
    log "Optimizing views..."
    php artisan view:clear
    php artisan view:cache
    
    # Clear caches
    log "Clearing application caches..."
    php artisan cache:clear
    
    success "Laravel initialization completed"
}

# Set proper permissions
set_permissions() {
    log "Setting proper file permissions..."
    
    # Ensure www-data owns the application
    chown -R www-data:www-data /var/www/html || warn "Could not change ownership to www-data"
    
    # Set directory permissions
    find /var/www/html -type d -exec chmod 755 {} \; || warn "Could not set directory permissions"
    
    # Set file permissions
    find /var/www/html -type f -exec chmod 644 {} \; || warn "Could not set file permissions"
    
    # Ensure storage and cache directories are writable
    chmod -R 775 /var/www/html/storage || warn "Could not set storage permissions"
    chmod -R 775 /var/www/html/bootstrap/cache || warn "Could not set cache permissions"
    
    # Ensure database file is writable (if SQLite)
    if [ "$DB_CONNECTION" = "sqlite" ] && [ -f "$DB_DATABASE" ]; then
        chmod 664 "$DB_DATABASE" || warn "Could not set database file permissions"
        chown www-data:www-data "$DB_DATABASE" || warn "Could not change database file ownership"
    fi
    
    success "File permissions set"
}

# Handle different initialization modes
handle_init_mode() {
    case "${INIT_MODE:-normal}" in
        "setup")
            log "Running in setup mode - performing full initialization"
            wait_for_database
            initialize_laravel
            set_permissions
            ;;
        "migrate")
            log "Running in migration mode - database migrations only"
            wait_for_database
            php artisan migrate --force
            ;;
        "cache")
            log "Running in cache mode - rebuilding caches only"
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            ;;
        "normal"|*)
            log "Running in normal mode - standard initialization"
            wait_for_database
            set_permissions
            
            # Only run Laravel initialization if this is the first run
            if [ ! -f "/var/www/html/.docker-initialized" ]; then
                initialize_laravel
                touch /var/www/html/.docker-initialized
            else
                log "Application already initialized, skipping Laravel setup"
            fi
            ;;
    esac
}

# Main initialization
main() {
    log "Starting ChordHound container initialization..."
    log "Environment: ${APP_ENV:-unknown}"
    log "Debug mode: ${APP_DEBUG:-false}"
    
    # Handle initialization based on mode
    handle_init_mode
    
    success "Container initialization completed successfully"
    
    # Execute the main command
    log "Starting main process: $*"
    exec "$@"
}

# Trap signals for graceful shutdown
trap 'log "Received termination signal, shutting down gracefully..."; exit 0' TERM INT

# Run main function with all arguments
main "$@"