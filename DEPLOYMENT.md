# ChordHound Deployment Guide

This guide covers deploying ChordHound to Control Plane (cpln).

## Prerequisites

1. **Control Plane Account**
   - Organization: `seanodea-5f382d`
   - GVC: `chordhound`
   - Workload: `app`

2. **GitHub Secrets**
   Set the following secret in your GitHub repository settings:
   - `CPLN_TOKEN` - Your Control Plane API token

## Deployment Process

### Automatic Deployment (GitHub Actions)

Deployments are triggered automatically when you push to the `main` branch:

```bash
git push origin main
```

The GitHub Action will:
1. Build a Docker image
2. Push it to Control Plane registry
3. Deploy to the `app` workload in the `chordhound` GVC

### Manual Deployment

If you need to deploy manually:

1. **Install Control Plane CLI**
   ```bash
   npm install -g @controlplane/cli
   ```

2. **Login to Control Plane**
   ```bash
   cpln login
   cpln profile create default --org seanodea-5f382d --gvc chordhound
   ```

3. **Build and Push Image**
   ```bash
   docker build -t seanodea-5f382d.registry.cpln.io/chordhound:latest .
   cpln image docker-login
   docker push seanodea-5f382d.registry.cpln.io/chordhound:latest
   ```

4. **Deploy to Workload**
   ```bash
   cpln workload update app \
     --set spec.containers.app.image=seanodea-5f382d.registry.cpln.io/chordhound:latest \
     --gvc chordhound
   ```

## Workload Configuration

The workload configuration is defined in `cpln/chordhound/app.yaml`:

- **Type**: Serverless
- **CPU**: 500m
- **Memory**: 512Mi
- **Port**: 8000
- **Autoscaling**: 1-5 replicas based on concurrency

## Monitoring

### Check Deployment Status

```bash
cpln workload get app --gvc chordhound
```

### View Logs

```bash
cpln workload logs app --gvc chordhound --follow
```

### Health Checks

The application includes health checks at:
- Liveness: `GET /` (port 8000)
- Readiness: `GET /` (port 8000)

## Troubleshooting

### Image Build Failures

1. Check Dockerfile syntax
2. Ensure all dependencies are in package.json and composer.json
3. Verify npm/composer install steps complete

### Deployment Failures

1. Check workload logs: `cpln workload logs app --gvc chordhound`
2. Verify environment variables are set correctly
3. Check resource limits (CPU/Memory)

### Application Issues

1. SSH into the container (if enabled)
2. Check Laravel logs: `/var/www/html/storage/logs/laravel.log`
3. Verify database migrations ran: `php artisan migrate:status`

## Rolling Back

To rollback to a previous image:

```bash
# List available images
cpln image ls --org seanodea-5f382d

# Update workload with previous image
cpln workload update app \
  --set spec.containers.app.image=seanodea-5f382d.registry.cpln.io/chordhound:PREVIOUS_TAG \
  --gvc chordhound
```

## Support

For Control Plane specific issues, refer to:
- [Control Plane Documentation](https://docs.controlplane.com/)
- [Control Plane CLI Reference](https://docs.controlplane.com/reference/cli)
