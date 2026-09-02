# Deployment & Infrastructure Guide

## 1. One-Click Render Deployment

The application ships with a complete Infrastructure-as-Code blueprint in [`render.yaml`](../render.yaml). To deploy:

1. Push the repository to GitHub (already done — `mhiskall282/digital-library-management-system-backend`).
2. Visit [https://dashboard.render.com](https://dashboard.render.com) → **New** → **Blueprint** → Connect the GitHub repo.
3. Render auto-detects `render.yaml` and provisions:
   - **Web Service** (`uew-digital-library`): Docker container (PHP 8.4 + Nginx + Supervisor).
   - **PostgreSQL Database** (`uew-library-db`): Managed PostgreSQL 16, `basic-256mb` plan.
   - **Persistent NVMe Disk** (`uew-library-storage`): 10 GB mounted at `/var/www/html/storage/app/public` so uploaded slide decks survive container restarts.
4. Set `MAIL_USERNAME` and `MAIL_PASSWORD` as **secret** environment variables inside the Render Dashboard (not committed to `render.yaml`).

> **Plan note**: The legacy `starter` Postgres plan has been removed by Render. The blueprint uses `basic-256mb` — Render's current entry-level plan. Upgrade to `pro-1gb` for production workloads.

---

## 2. Render Blueprint Summary (`render.yaml`)

```yaml
services:
  - type: web
    name: uew-digital-library
    runtime: docker
    plan: free              # upgrade to 'standard' for production
    region: frankfurt
    healthCheckPath: /health
    disk:
      name: uew-library-storage
      mountPath: /var/www/html/storage/app/public
      sizeGB: 10

databases:
  - name: uew-library-db
    plan: basic-256mb       # Render current supported plan
    postgresMajorVersion: 16
```

---

## 3. Vercel Serverless Deployment

The application is wired for Vercel via [`vercel.json`](../vercel.json) and [`api/index.php`](../api/index.php):

```bash
# Install Vercel CLI globally
npm i -g vercel

# Deploy from repository root
vercel --prod
```

Set these environment variables in the Vercel Dashboard:
- `APP_KEY` — run `php artisan key:generate --show` locally to get the value.
- `DATABASE_URL` — your external PostgreSQL connection string.
- `MAIL_USERNAME`, `MAIL_PASSWORD` — Zoho credentials.

---

## 4. Zoho Mail SMTP Setup

The app is configured to send transactional email via Zoho Mail.

### `.env` Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtppro.zoho.com
MAIL_PORT=465
MAIL_USERNAME=test@johnokyere.xyz
MAIL_PASSWORD=Mhiskall9090@
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="test@johnokyere.xyz"
MAIL_FROM_NAME="UEW School of Business Digital Library"
```

### Fix: Zoho SMTP `554 5.7.8 Access Restricted`
If authentication fails with error `554 5.7.8`, take **one** of these steps inside your Zoho account:

**Option A — Enable SMTP Access:**
1. Log in to [mailadmin.zoho.com](https://mailadmin.zoho.com).
2. Go to **Users** → select `test@johnokyere.xyz` → **Mail Settings** → **Email Accounts**.
3. Scroll to **IMAP/POP & SMTP** → toggle **SMTP Access** to **ON**.

**Option B — App-Specific Password (if 2FA is enabled):**
1. Log in to [accounts.zoho.com](https://accounts.zoho.com).
2. Go to **Security** → **App Passwords** → **Generate App Password**.
3. Name it `UEW Library SMTP` and paste the generated password into `MAIL_PASSWORD` in `.env`.

### Testing Email Templates
```bash
# Test all 3 templates via log mailer (safe, no real send)
php artisan mail:test-templates

# Test via live Zoho SMTP
php artisan mail:test-templates --smtp

# Run the advanced SMTP diagnostic (tries all host/port combos)
php tests/test_zoho_smtp.php
```

### Admin Email Studio
Visit `/admin/mail-studio` to preview all 3 branded templates in-browser and trigger live SMTP test dispatches to any recipient address.

---

## 5. Multi-Stage Docker Container

The `deploy/Dockerfile` uses a multi-stage build:

| Stage | Base Image | Purpose |
|-------|-----------|---------|
| **builder** | `node:22-alpine` | Compiles Vite + Tailwind CSS frontend assets |
| **production** | `php:8.4-fpm-alpine` | Serves the Laravel app via PHP-FPM + Nginx |

The entrypoint script `deploy/docker/start.sh` runs automatically on every container start:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

---

## 6. Local Development

### Option A — Native PHP (Recommended for Development)
```bash
git clone https://github.com/mhiskall282/digital-library-management-system-backend
cd digital-library-management-system-backend
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev &
php artisan serve
```
Open [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Option B — Docker Compose
```bash
docker compose up -d --build
```
- Web Application: [http://localhost:8080](http://localhost:8080)
- Mailpit (Email Preview): [http://localhost:8025](http://localhost:8025)

---

## 7. Seeded Test Credentials

| Role | Email | Student ID | Password |
|------|-------|-----------|----------|
| Super Admin | `superadmin@uew.edu.gh` | — | `password` |
| Admin | `admin@uew.edu.gh` | — | `password` |
| Librarian | `librarian@uew.edu.gh` | — | `password` |
| Lecturer | `lecturer@uew.edu.gh` | — | `password` |
| Student | `student@uew.edu.gh` | `5201040001` | `password` |
