# Deployment & Infrastructure Guide: Render & Docker

## 1. Architecture on Render

The system is declared via Infrastructure as Code using [`render.yaml`](file:///c:/Users/user/Desktop/digital-library-management-system-backend/render.yaml):

```yaml
services:
  - type: web
    name: uew-digital-library
    runtime: docker
    dockerfilePath: ./deploy/Dockerfile
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: DB_CONNECTION
        value: pgsql
      - key: DATABASE_URL
        fromDatabase:
          name: uew-library-db
          property: connectionString
      - key: CACHE_STORE
        value: database
      - key: SESSION_DRIVER
        value: database

databases:
  - name: uew-library-db
    databaseName: uew_library
    user: uew_admin
    plan: starter
```

---

## 2. Multi-Stage Docker Container (`deploy/Dockerfile`)

1. **Stage 1: Frontend Asset Builder**:
   - Node.js 22 container.
   - Installs packages and runs `npm run build` using Vite 6 & Tailwind CSS v4.
2. **Stage 2: Production PHP Runtime**:
   - PHP 8.3 Alpine with `pdo_pgsql`, `pdo_sqlite`, `fileinfo`, `mbstring`, `zip`, `gd`.
   - Copies pre-compiled frontend assets from Stage 1 into `public/build`.
   - Embeds Nginx reverse proxy configured for 100MB uploads and gzip compression.
   - Embeds Supervisor running both Nginx and PHP-FPM under a unified process tree.
   - Entrypoint [`deploy/docker/start.sh`](file:///c:/Users/user/Desktop/digital-library-management-system-backend/deploy/docker/start.sh) auto-runs:
     ```bash
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     php artisan migrate --force
     exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
     ```

---

## 3. Local Development with Docker Compose

Run the complete multi-container stack locally (PostgreSQL + Web App + Mailpit):
```bash
docker compose up -d --build
```
- Web Application: [http://localhost:8080](http://localhost:8080)
- Mailpit Web UI: [http://localhost:8025](http://localhost:8025)
