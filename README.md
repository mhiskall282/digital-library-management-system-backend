# UEW School of Business Digital Library Management System

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Render Deploy](https://img.shields.io/badge/Deploy-Render-46E3B7?style=for-the-badge&logo=render&logoColor=white)](https://render.com/)

A modern, enterprise-grade academic repository and learning management system designed for the **University of Education, Winneba (UEW) — School of Business**. Built using **Laravel 11**, **Tailwind CSS v4**, **Alpine.js**, and **PostgreSQL 16**, optimized for seamless deployment to **Render**.

---

## 🏛️ Comprehensive Architecture & Features

### 1. 🌐 Public Brand Landing Page (`/`)
- Pre-login brand showcase presenting the official repository to prospective and enrolled scholars.
- Interactive academic program directory cards with quick stream exploration.
- Real-time repository metrics (Total Study Materials, Verified Downloads, Active Scholars, Courses Offered).
- Public **Top Contributors Leaderboard** celebrating scholar engagement.
- Search jump bar with instant query dispatch to the academic catalog.

### 2. 🎓 Academic Programs & Level Directory (`/programs`)
- Curriculum navigation grouped by degree programs:
  - **BSc. Business Information Systems (BIS)**
  - **BSc. Banking and Finance**
  - **BSc. Accounting**
  - **BBA. Marketing**
  - **BBA. Human Resource Management**
  - **BSc. Procurement & Supply Chain Management**
  - **Postgraduate Studies (MBA, MPhil, PhD)**
- Multi-level tab navigation (`L100`, `L200`, `L300`, `L400`, `MASTERS`, `PHD`) revealing all courses and attached lecture slides.

### 3. 📅 Syllabus Week Grouping (Weeks 1 to 15)
- Every lecture slide and course resource can be tagged with its corresponding syllabus module (`Week 1` through `Week 15`), or marked as `General / Exam Paper`.
- Catalog search and filter sidebar includes a dedicated **Syllabus Week** filter dropdown.
- Resource cards prominently display color-coded week badges (`Week 4 Module`, etc.) for fast curriculum comprehension.

### 4. ⚡ Binary BLOB & Zero-Disk High-Performance Streaming
- Documents can be stored directly as binary blobs (`file_blob`) in the database as well as on disk.
- When downloaded or previewed, the application streams directly from the binary blob with optimal HTTP caching headers (`Content-Disposition`, `Content-Length`, `Cache-Control: public`), avoiding disk bottlenecking and memory bloat.

### 5. 📝 Student Contributions & Moderation Desk
- Students can submit verified lecture slides and past examination question papers via `/student/contribute`.
- Uploads enter `PENDING_REVIEW` status and are queued in the **Admin Moderation Desk** (`/admin/moderation`).
- Librarians and staff review the material with an in-browser preview, and either approve it (which publishes it and awards **+50 Contributor Points**) or reject it with constructive feedback.

### 6. 🏆 Gamification, Points & Leaderboard
- Students earn contributor points:
  - Profile Onboarding: `+25 points`
  - Approved Document Upload: `+50 points`
  - Helpful Review: `+10 points`
- Automated Scholar Ranks:
  - `0 - 49 pts`: Novice Contributor
  - `50 - 149 pts`: Scholar Contributor
  - `150 - 299 pts`: Top Contributor
  - `300+ pts`: Master Scholar
- Highlighting top scholars on the public homepage and student dashboard.

### 7. 🔒 Intellectual Property (IP) Protection & Download Authorizations
- Configurable via `Admin System Settings`: `require_download_approval`.
- When enabled, students submit a formal study purpose/justification before downloading material.
- Dedicated **Download Approvals Desk** (`/admin/downloads`) where staff approve/decline requests.
- **Client IP Tracking**: Every download logs client IP address, browser user-agent, and timestamps in `activity_logs` for copyright compliance.

### 8. 💬 Material & Support Requests Desk (`/requests`)
- Dedicated communication channel where scholars submit requests for missing lecture slides or past exam papers.
- Tracked through `OPEN`, `IN_PROGRESS`, `FULFILLED`, and `CLOSED` statuses with staff responses.

### 9. 📢 Broadcast Messaging & Modern HTML Emails
- Administrators can compose targeted announcements to **All Students**, by **Academic Level** (`L300`), or by **Degree Program** (`BIS`).
- Transmitted as instant in-app dashboard alerts and styled HTML emails (`AdminBroadcastMail`).
- Comprehensive branded HTML email templates for:
  - Welcome & Activation with initial credentials (`WelcomeActivationMail`)
  - Security Alerts for password/authentication events (`SecurityAlertMail`)
  - Broadcast Announcements (`AdminBroadcastMail`)

### 10. ✉️ In-App Email Studio & Simulator (`/admin/mail-studio`)
- **Visual Template Inspector**: Real-time rendering and responsive preview for all 3 institutional email templates.
- **Dual Delivery Modes**:
  - **⚡ In-App Simulator**: Instant zero-dependency delivery directly into the In-App Mailbox for testing workflows without an active mail server.
  - **🌐 Live SMTP**: Direct dispatch via Google Workspace / Gmail or Zoho. If server policies restrict delivery, the system automatically falls back to archiving the full email in the In-App Mailbox.
- **Inbound Message Simulation**: Simulate receiving student and staff replies to test end-to-end communication loops without paying for third-party IMAP/POP3 subscriptions.
- **HTML Telemetry & Payload Inspector**: Click any message in the mailbox to view rendered HTML, transmission headers, and delivery status.

### 11. ⚙️ Dynamic SMTP Server Management (`/admin/settings`)
- Configure and update outbound mail server credentials directly from the Admin Dashboard.
- **One-Click Presets**: Pre-configures Google Gmail / Workspace (Port 587 TLS), Zoho Mail (Port 465 SSL), or Log Driver.
- **Secure Key Storage**: Passwords are securely stored in the database, masked in the UI, and never overwritten unless a new key is typed.
- **Live Connection Ping**: Test connectivity to any recipient with a single click.
- **Zero-Redeployment Real-Time Binding**: All application emails automatically use database settings without restarting containers or editing `.env`.

### 12. 📑 Bulk Student Ingestion (CSV / Excel)
- Batch upload class rosters via `/admin/users/import`.
- Automated account creation with cryptographically secure temporary passwords and un-onboarded status.
- Automated email dispatch of activation links and sign-in credentials.
- Downloadable official CSV template (`/admin/users/import/sample`).

### 13. 📊 System Audit Logs & Compliance Reports (`/admin/reports`)
- Comprehensive audit trail of all platform events (Downloads, Uploads, Approvals, Broadcasts, Logins).
- Filter by action event, date range (Today, Week, Month, All Time), or search term.
- **Export CSV Report**: One-click download of audit logs for the Dean and University Academic Board.

### 14. 🛡️ Security Hardening & Zero Credential Exposure
- Public login screen (`/login`) is strictly hardened: all hardcoded demo credential quick-fill cards have been removed.
- Full rate-limiting on sensitive endpoints.
- Protection against Path Traversal, MIME sniffing, and SQL injection.
- Complete documentation handbook: [`docs/SECURITY_AUDIT_AND_QA.md`](docs/SECURITY_AUDIT_AND_QA.md).

---

## 🎨 Design System & UEW Aesthetics

| Color Token | Hex Code | Usage |
|---|---|---|
| **UEW Scarlet** | `#C41E3A` | Primary brand accent, primary CTA buttons, active states, brand headers |
| **UEW Ultramarine Navy** | `#1E3A8A` | Academic header stripes, course code badges, secondary actions |
| **UEW Deep Slate** | `#0F172A` | Admin sidebar background, card elevations, high-contrast dark elements |
| **Academic Amber** | `#F59E0B` | 5-star ratings, bookmark highlights, warning badges |
| **Emerald Green** | `#10B981` | Verified status badges, success indicators |
| **Surface Slate** | `#F8FAFC` | Page background providing clean academic contrast |

**Typography**: Plus Jakarta Sans & Inter via Google Fonts.

---

## 🚀 Quick Start (Local Development)

### Prerequisites
- PHP >= 8.2 with `pdo_sqlite`, `fileinfo`, `mbstring` extensions
- Composer >= 2.0
- Node.js >= 20.0 and npm

### Setup
```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate and seed database
php artisan migrate:fresh --seed

# 4. Build assets
npm run build

# 5. Start dev server
php artisan serve
```

---

## 🧪 Automated Testing
Run the complete PHPUnit test suite:
```bash
php artisan test
```
All **19 tests** pass with **51 assertions** covering health checks, landing page, programs directory, student contribution, admin moderation desk, points awarding, download approvals, and compliance reporting.

---

## ☁️ Deployment on Render

This repository includes a turnkey **Render Blueprint** ([`render.yaml`](render.yaml)):
- **Web Service**: Multi-stage Docker container (`deploy/Dockerfile`) with PHP-FPM, Nginx, and Supervisor.
- **Persistent Disk**: 10GB NVMe storage mounted at `/var/www/html/storage/app/public` ensuring uploaded slides survive redeployments.
- **Managed Database**: PostgreSQL 16 managed database with automated migration on deploy.
- **Health Check**: Configured at `/health`.

---

## 📚 Technical Documentation Index

- [Architecture & Domain Model](docs/ARCHITECTURE.md)
- [Database Schema & ERD](docs/DATABASE_SCHEMA.md)
- [Authentication & RBAC Flows](docs/AUTHENTICATION_FLOWS.md)
- [Web & API Route Directory](docs/API_DOCUMENTATION.md)
- [Deployment & Setup Guide](docs/DEPLOYMENT_AND_SETUP.md)
- [Frontend Design System & Tokens](docs/FRONTEND_INTEGRATION_GUIDE.md)
- [Security Audit & Enterprise Architecture Q&A](docs/SECURITY_AUDIT_AND_QA.md)

---
&copy; University of Education, Winneba &mdash; School of Business. All rights reserved.
