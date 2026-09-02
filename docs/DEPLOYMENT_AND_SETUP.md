# Deployment & Infrastructure Guide

## 1. One-Click Render Deployment

The application ships with a complete Infrastructure-as-Code blueprint in [`render.yaml`](../render.yaml). To deploy:

1. Push the repository to GitHub (already done — `mhiskall282/digital-library-management-system-backend`).
2. Visit [https://dashboard.render.com](https://dashboard.render.com) → **New** → **Blueprint** → Connect the GitHub repo.
3. Render auto-detects `render.yaml` and provisions:
   - **Web Service** (`uew-digital-library`): Docker container (PHP 8.4 + Nginx + Supervisor) on the `standard` plan.
   - **PostgreSQL Database** (`uew-library-db`): Managed PostgreSQL 16, `basic-1gb` paid plan with 10 GB disk.
   - **Persistent NVMe Disk** (`uew-library-storage`): 10 GB mounted at `/var/www/html/storage/app/public` so uploaded slide decks survive container restarts.
4. Set `MAIL_USERNAME` and `MAIL_PASSWORD` as **secret** environment variables inside the Render Dashboard (not committed to `render.yaml`).

> **Plan note**: Render's current valid PostgreSQL plan identifiers are `free`, `basic-256mb`, `basic-1gb`, `standard-4gb`, `pro-8gb`, etc. Storage is configured separately using `diskSizeGB`. The legacy `starter` plan is no longer supported for new databases.

---

## 2. Render Blueprint Summary (`render.yaml`)

```yaml
services:
  - type: web
    name: uew-digital-library
    runtime: docker
    plan: standard          # Paid plan — $25/month
    region: frankfurt
    healthCheckPath: /health
    disk:
      name: uew-library-storage
      mountPath: /var/www/html/storage/app/public
      sizeGB: 10

databases:
  - name: uew-library-db
    plan: basic-1gb         # Paid plan — $20/month, 1 GB RAM, 10 GB disk
    diskSizeGB: 10          # Separate storage field (Render flexible plan model)
    postgresMajorVersion: 16
```

> **Finding valid plan IDs**: Open the Render Dashboard → **New PostgreSQL** — the plan names shown in the UI are the exact identifiers to use in `render.yaml`.

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
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=5231570343@st.uew.edu.gh
MAIL_PASSWORD=your_16_char_google_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="5231570343@st.uew.edu.gh"
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

### Admin Email Studio & Simulator (`/admin/mail-studio`)
Visit `/admin/mail-studio` to:
- **Preview All 3 Branded HTML Templates**: Welcome & Activation, Security Alert, Departmental Broadcast.
- **Dual-Mode Delivery**:
  - **⚡ In-App Simulator**: Delivers immediately to the In-App Mailbox tab with zero external mail server dependencies.
  - **🌐 Live Zoho SMTP**: Dispatches via `smtppro.zoho.com:465`. If Zoho rejects due to account settings (`554 5.7.8`), the system automatically archives the full rendered email in the In-App Mailbox with diagnostic explanations.
- **Simulate Inbound Correspondence**: Test receiving student inquiries or verification replies without needing expensive IMAP/POP3 subscriptions.
- **Live HTML Inspector**: Click any logged message to inspect full headers, rendered HTML, and delivery status in a responsive modal.

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

## 6. SSL Reverse Proxy & Mixed Content Prevention

When deploying on Render, AWS, or Cloudflare, the edge load balancer terminates SSL and proxies unencrypted traffic to Nginx on port 80. To prevent browsers from blocking CSS/JS as mixed content:

1. **Trusted Proxies (`bootstrap/app.php`)**:
   `$middleware->trustProxies(at: '*');` ensures Laravel reads `X-Forwarded-Proto: https` from Render.
2. **Forced HTTPS Scheme (`app/Providers/AppServiceProvider.php`)**:
   `URL::forceScheme('https')` guarantees all `@vite` assets, preloads, route links, and form actions render as `https://`.
3. **Browser CSP Auto-Upgrade**:
   Every Blade layout includes:
   ```html
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   ```
4. **Nginx FastCGI Forwarding (`deploy/docker/nginx.conf`)**:
   Nginx passes `HTTP_X_FORWARDED_PROTO`, `HTTP_X_FORWARDED_PORT 443`, and `HTTPS on` directly to PHP-FPM.

---

## 7. Local Development

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
