#!/bin/sh

# ChordHound Health Check Script
# This script performs comprehensive health checks for the containerized application

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
HEALTH_URL="${APP_URL:-http://localhost:8000}/health"
DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
TIMEOUT=10

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

check_web_server() {
    log "Checking web server health..."
    if curl -f -s --max-time $TIMEOUT "$HEALTH_URL" > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Web server is responding${NC}"
        return 0
    else
        echo -e "${RED}✗ Web server is not responding${NC}"
        return 1
    fi
}

check_database() {
    log "Checking database connectivity..."
    if [ -f "$DB_PATH" ] && [ -r "$DB_PATH" ]; then
        # Try to perform a simple query
        if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';" > /dev/null 2>&1; then
            echo -e "${GREEN}✓ Database is accessible${NC}"
            return 0
        else
            echo -e "${RED}✗ Database query failed${NC}"
            return 1
        fi
    else
        echo -e "${RED}✗ Database file not found or not readable${NC}"
        return 1
    fi
}

check_storage_permissions() {
    log "Checking storage permissions..."
    if [ -w "/var/www/html/storage" ] && [ -w "/var/www/html/bootstrap/cache" ]; then
        echo -e "${GREEN}✓ Storage directories are writable${NC}"
        return 0
    else
        echo -e "${RED}✗ Storage directories are not writable${NC}"
        return 1
    fi
}

check_queue_health() {
    log "Checking queue system..."
    # Check if queue worker is responding (simplified check)
    if pgrep -f "artisan queue:work" > /dev/null; then
        echo -e "${GREEN}✓ Queue worker is running${NC}"
        return 0
    else
        echo -e "${YELLOW}⚠ Queue worker not detected (may be normal in some deployments)${NC}"
        return 0  # Don't fail health check for this
    fi
}

check_disk_space() {
    log "Checking disk space..."
    DISK_USAGE=$(df /var/www/html | awk 'NR==2 {print $5}' | sed 's/%//')
    if [ "$DISK_USAGE" -lt 90 ]; then
        echo -e "${GREEN}✓ Disk space is adequate (${DISK_USAGE}% used)${NC}"
        return 0
    else
        echo -e "${YELLOW}⚠ Disk space is low (${DISK_USAGE}% used)${NC}"
        return 1
    fi
}

check_memory_usage() {
    log "Checking memory usage..."
    if command -v free > /dev/null; then
        MEMORY_USAGE=$(free | awk '/^Mem:/ {printf "%.0f", $3/$2 * 100}')
        if [ "$MEMORY_USAGE" -lt 90 ]; then
            echo -e "${GREEN}✓ Memory usage is normal (${MEMORY_USAGE}% used)${NC}"
            return 0
        else
            echo -e "${YELLOW}⚠ Memory usage is high (${MEMORY_USAGE}% used)${NC}"
            return 1
        fi
    else
        echo -e "${YELLOW}⚠ Cannot check memory usage${NC}"
        return 0
    fi
}

# Main health check function
main() {
    log "Starting ChordHound health check..."
    
    HEALTH_STATUS=0
    
    # Critical checks (must pass)
    check_web_server || HEALTH_STATUS=1
    check_database || HEALTH_STATUS=1
    check_storage_permissions || HEALTH_STATUS=1
    
    # Warning checks (logged but don't fail)
    check_disk_space || true
    check_memory_usage || true
    check_queue_health || true
    
    if [ $HEALTH_STATUS -eq 0 ]; then
        log "✅ All critical health checks passed"
        echo "healthy"
        exit 0
    else
        log "❌ One or more critical health checks failed"
        echo "unhealthy"
        exit 1
    fi
}

# Handle different check types
case "${1:-full}" in
    "quick")
        check_web_server
        ;;
    "db")
        check_database
        ;;
    "full"|*)
        main
        ;;
esac