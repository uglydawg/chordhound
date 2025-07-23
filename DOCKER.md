# ChordHound Docker Deployment Guide

This guide covers containerizing and deploying ChordHound using Docker for both development and production environments.

## Quick Start

### Development Environment

1. **Clone and setup:**
   ```bash
   git clone <repository-url>
   cd chordhound
   cp .env.docker .env
   # Edit .env with your configuration
   ```

2. **Start development environment:**
   ```bash
   docker-compose up -d
   ```

3. **Access the application:**
   - Application: http://localhost:8000
   - WebSocket server: http://localhost:8080

### Production Environment

1. **Prepare environment:**
   ```bash
   cp .env.docker .env
   # Configure production values in .env
   ```

2. **Deploy production stack:**
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

3. **Access the application:**
   - Application: http://localhost (port 80/443)
   - WebSocket server: http://localhost:8080

## Container Architecture

### Services Overview

#### Development (`docker-compose.yml`)
- **app**: Laravel application server (port 8000)
- **queue**: Background job processor
- **scheduler**: Laravel task scheduler
- **reverb**: WebSocket server (port 8080)
- **tools**: Development utilities (optional)

#### Production (`docker-compose.prod.yml`)
- **app**: Nginx + PHP-FPM (ports 80/443)
- **queue**: Optimized job processor with resource limits
- **scheduler**: Task scheduler with resource limits
- **reverb**: WebSocket server with SSL support
- **backup**: Automated SQLite backup service
- **monitoring**: Node exporter for metrics (optional)

## Configuration

### Environment Variables

Copy `.env.docker` to `.env` and configure:

#### Required Variables
```bash
APP_KEY=                    # Generate with: php artisan key:generate
APP_URL=https://your-domain.com
MAIL_HOST=smtp.example.com
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
```

#### OAuth Configuration
```bash
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

#### Stripe Configuration
```bash
STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret
```

#### WebSocket Configuration
```bash
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
```

### SSL/TLS Setup

For production HTTPS deployment:

1. **Obtain SSL certificates** (Let's Encrypt, Cloudflare, etc.)

2. **Mount certificates in docker-compose.prod.yml:**
   ```yaml
   volumes:
     - /path/to/ssl/certs:/etc/nginx/ssl:ro
   ```

3. **Update nginx configuration** to handle SSL

## Deployment Commands

### Initial Deployment

```bash
# Build and start containers
docker-compose -f docker-compose.prod.yml up -d --build

# Check container status
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f app
```

### Updates and Maintenance

```bash
# Update application code
git pull
docker-compose -f docker-compose.prod.yml build --no-cache app
docker-compose -f docker-compose.prod.yml up -d app

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear caches
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### Database Management

```bash
# Backup database
docker-compose -f docker-compose.prod.yml exec app cp database/database.sqlite /tmp/backup.sqlite
docker cp chordhound_app_prod:/tmp/backup.sqlite ./backup-$(date +%Y%m%d).sqlite

# Restore database
docker cp ./backup.sqlite chordhound_app_prod:/tmp/restore.sqlite
docker-compose -f docker-compose.prod.yml exec app cp /tmp/restore.sqlite database/database.sqlite
```

## Monitoring and Health Checks

### Health Check Endpoints

- **Application Health**: `GET /health`
- **Custom Health Script**: `docker/healthcheck.sh`

### Container Health Checks

All containers include health checks:
```bash
# Check container health
docker ps --format "table {{.Names}}\t{{.Status}}"

# View health check logs
docker inspect chordhound_app_prod | grep -A 10 "Health"
```

### Monitoring with Profiles

Enable monitoring stack:
```bash
docker-compose -f docker-compose.prod.yml --profile monitoring up -d
```

Access metrics at: http://localhost:9100/metrics

## Scaling and Performance

### Resource Limits

Production containers include resource limits:
- **App**: 512MB memory limit, 256MB reservation
- **Queue**: 256MB memory limit, 128MB reservation
- **Scheduler**: 128MB memory limit, 64MB reservation

### Horizontal Scaling

Scale queue workers:
```bash
docker-compose -f docker-compose.prod.yml up -d --scale queue=3
```

### Performance Tuning

1. **PHP-FPM Configuration**: Edit `docker/php-fpm.conf`
2. **Nginx Configuration**: Edit `docker/nginx.conf`
3. **Laravel Optimization**: 
   ```bash
   docker-compose exec app php artisan optimize
   ```

## Backup and Recovery

### Automated Backups

The backup service runs daily and retains backups based on `BACKUP_RETENTION_DAYS`.

### Manual Backup

```bash
# Create backup
docker-compose -f docker-compose.prod.yml exec backup sh -c "
  cp /var/www/html/database/database.sqlite /var/backups/manual_backup_$(date +%Y%m%d_%H%M%S).sqlite
"

# List backups
docker-compose -f docker-compose.prod.yml exec backup ls -la /var/backups/
```

## Troubleshooting

### Common Issues

1. **Permission Errors**:
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/storage
   ```

2. **Database Connection Issues**:
   ```bash
   docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo();"
   ```

3. **Cache Issues**:
   ```bash
   docker-compose exec app php artisan cache:clear
   docker-compose exec app php artisan config:clear
   ```

### Debug Mode

Enable debug mode for troubleshooting:
```bash
# Set in .env
APP_DEBUG=true
APP_ENV=local

# Restart containers
docker-compose -f docker-compose.prod.yml restart
```

### Container Logs

```bash
# View all logs
docker-compose -f docker-compose.prod.yml logs -f

# View specific service logs
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f queue
```

## Security Considerations

### Container Security
- Containers run as non-root user (`www-data`)
- Sensitive files excluded via `.dockerignore`
- Security headers configured in Nginx
- Resource limits prevent resource exhaustion

### Application Security
- Environment variables for sensitive data
- SSL/TLS encryption for production
- Regular security updates via base image updates

### Network Security
- Internal network isolation
- Exposed ports limited to necessary services
- Health check endpoints secured

## CI/CD Integration

### Docker Build in CI

```yaml
# Example GitHub Actions workflow
name: Build and Deploy
on:
  push:
    branches: [main]

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Build Docker image
        run: docker build -t chordhound:latest .
      - name: Deploy to production
        run: |
          docker-compose -f docker-compose.prod.yml pull
          docker-compose -f docker-compose.prod.yml up -d --build
```

### Automated Testing

```bash
# Run tests in container
docker-compose exec app php artisan test

# Run specific test suite
docker-compose exec app php artisan test --testsuite=Feature
```

## File Structure

```
chordhound/
├── Dockerfile              # Multi-stage build configuration
├── docker-compose.yml      # Development environment
├── docker-compose.prod.yml # Production environment
├── .dockerignore           # Docker build context exclusions
├── .env.docker            # Environment template
├── docker/
│   ├── nginx.conf         # Nginx server configuration
│   ├── php-fpm.conf      # PHP-FPM pool configuration
│   ├── healthcheck.sh    # Health check script
│   └── entrypoint.sh     # Container initialization
└── DOCKER.md             # This documentation
```

## Support

For deployment issues:
1. Check container logs first
2. Verify environment configuration
3. Test health check endpoints
4. Review resource usage and limits

For application-specific issues, refer to the main project documentation.