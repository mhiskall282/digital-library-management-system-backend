# Architecture Overview — UEW School of Business Digital Library Management System

## 1. High-Level System Architecture

```
                        ┌─────────────────────────────────────────────────┐
                        │         UEW Business Digital Library             │
                        │         (Laravel 11 Monolith / MVC)             │
                        └─────────────────────────────────────────────────┘
                                              │
           ┌──────────────────────────────────┼──────────────────────────────────┐
           │                                  │                                  │
   ┌───────▼──────┐                  ┌────────▼───────┐                 ┌───────▼───────┐
   │  Public Web  │                  │  Auth Portal   │                 │  Admin Panel  │
   │   /welcome   │                  │  /login        │                 │  /admin/*     │
   │   /programs  │                  │  /register     │                 │               │
   │   /docs      │                  │  /onboarding   │                 │  Dashboard    │
   │   /health    │                  │                │                 │  Users & RBAC │
   └──────────────┘                  └────────────────┘                 │  Moderation   │
                                                                        │  Reports      │
                                                                        │  Mail Studio  │
                                                                        └───────────────┘
                                                                                │
           ┌───────────────────────────────────────────────────────────────────┘
           │
   ┌───────▼───────────────────────────────────────────────────────────────────────────┐
   │                         Authenticated Student & Faculty Portal                     │
   │                                                                                   │
   │  /dashboard           Catalog Explorer — search, filter by program/level/week    │
   │  /student/hub         Personalized dashboard with recommendations                │
   │  /student/contribute  Upload slides & past exams (BLOB storage, week tagging)    │
   │  /requests            Material Request Desk — request missing resources           │
   │  /bookmarks           Personal saved resource bookmarks with private notes        │
   │  /notifications       In-app notification feed                                   │
   │  /profile/edit        Profile editor — program, level, phone, bio                │
   └───────────────────────────────────────────────────────────────────────────────────┘
           │
   ┌───────▼──────────────────────────────────────────────────────────────────┐
   │                       Data & Infrastructure Layer                         │
   │                                                                           │
   │  Database: SQLite (dev) / PostgreSQL 16 (production on Render)           │
   │  File Storage: Binary BLOB columns in DB (zero-disk streaming)           │
   │  Cache/Session: Database driver (production) / File (local dev)          │
   │  Mail: Zoho SMTP (smtppro.zoho.com:465 SSL)                             │
   │  Queue: Sync (can upgrade to database/redis for async jobs)              │
   │  Activity Logging: ActivityLog model (polymorphic, client IP, UA)        │
   └───────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Role-Based Access Control (RBAC) — 5 Tiers

| Role | Level | Capabilities |
|------|-------|-------------|
| `student` | 1 | Browse catalog, download (with IP audit), bookmark, contribute materials, earn points, view leaderboard |
| `lecturer` | 2 | All student capabilities + upload official course materials directly without moderation queue |
| `staff` | 3 | All lecturer capabilities + approve/reject student submissions, manage material requests |
| `admin` | 4 | All staff capabilities + user management, system settings, broadcast announcements, audit reports |
| `superadmin` | 5 | All admin capabilities + role assignment, system configuration, category management |

---

## 3. Key Domain Models

| Model | Table | Key Relationships |
|-------|-------|------------------|
| `User` | `users` | hasMany Reviews, Bookmarks, Resources (uploaded), ActivityLogs, Notifications |
| `Resource` | `resources` | belongsTo User (uploader), Category; hasMany Reviews, Bookmarks, DownloadRequests |
| `Category` | `categories` | hasMany Resources |
| `Review` | `reviews` | belongsTo User, Resource |
| `Bookmark` | `bookmarks` | belongsTo User, Resource |
| `ActivityLog` | `activity_logs` | polymorphic `subject` (logs actions against any model) |
| `DownloadRequest` | `download_requests` | belongsTo User, Resource |
| `MaterialRequest` | `material_requests` | belongsTo User |
| `Notification` | `notifications` | belongsTo User |
| `Setting` | `settings` | key-value system configuration store |

---

## 4. Resource Upload Flow (Binary BLOB Architecture)

```
Student/Admin submits file
        │
        ▼
ContributionController / Admin\ResourceController
        │
        ├─ Validate: max 50MB, mimes: pdf,doc,docx,ppt,pptx,xls,xlsx,zip
        ├─ Capture: file_get_contents($file->getRealPath()) → stored as BLOB
        ├─ Tag: week (1–15), course_code, program, level, academic_year
        ├─ Set status: 'pending' (student) | 'approved' (admin direct upload)
        └─ Award +50 contributor points on admin approval
        │
        ▼
ResourceController::download / preview
        │
        ├─ Check: if (!empty($resource->file_blob)) → stream directly from BLOB
        ├─ Headers: Content-Type, Content-Disposition, Content-Length, Cache-Control
        └─ Fallback: Storage::path($resource->file_path) if no BLOB
```

**Zero-disk advantage**: Files are stored in the database as `BYTEA` (PostgreSQL) / `BLOB` (SQLite) — no separate file system mount required for basic deployments.

---

## 5. Gamification Engine

```
Action                      →  Points Awarded
─────────────────────────────────────────────
First-time profile setup    →  +25 Points
Submission approved         →  +50 Points
Course review submitted     →  +10 Points

Points Total   →  Contributor Rank
──────────────────────────────────
0 – 49         →  🥉 Novice Contributor
50 – 149       →  🥈 Scholar Contributor
150 – 299      →  🥇 Top Contributor
300+           →  👑 Master Scholar
```

Points are stored in `users.points` (integer). Leaderboard is a live `ORDER BY points DESC` query on the `users` table.

---

## 6. Syllabus Week Grouping

All resources are tagged with a `week` column (tinyint, 1–15, nullable):

- **Week 1–6**: Early semester foundational modules
- **Week 7**: Mid-semester revision materials
- **Week 8–14**: Advanced & applied content
- **Week 15**: Finals revision, exam prep, past papers
- **NULL / 0**: General resources, supplementary reading, cross-semester exams

Students filter the Catalog Explorer by week using the `scopeFilterByWeek` scope on the `Resource` model.

---

## 7. Dual-Mode Email & In-App Simulation Architecture

Three branded institutional email templates with dual delivery pathways:

| Mailable | Template | Trigger |
|----------|----------|---------|
| `WelcomeActivationMail` | `emails/auth/welcome-activation` | New student account created |
| `SecurityAlertMail` | `emails/security/alert` | Password reset, suspicious login |
| `AdminBroadcastMail` | `emails/admin/broadcast` | Departmental announcement to all users |

### Dual Delivery Modes & Dynamic UI Configuration:
1. **Dynamic Database-Driven SMTP Engine (`/admin/settings` -> SMTP & Email Logins)**:
   - Administrators can update the SMTP mailer, host (`smtp.gmail.com`, `smtppro.zoho.com`), port (`587`, `465`), encryption (`tls`, `ssl`), username, password, and sender identities directly through the web UI.
   - `AppServiceProvider` dynamically binds database settings to Laravel's runtime mail configuration without container redeployment.
   - Includes instant one-click connection ping and quick-presets for Google Workspace and Zoho.
2. **In-App Simulator & Telemetry Mailbox (`/admin/mail-studio`)**:
   - Dispatches are automatically recorded to the `email_logs` table.
   - If external SMTP fails (e.g. server-side policy block), the system gracefully archives the full HTML email to the In-App Mailbox with diagnostic explanations.
   - Admins can inspect full rendered HTML, headers, and status in `/admin/mail-studio`.
   - Inbound simulation allows testing student replies and inquiries without requiring premium mail server packages.

**Admin Test Studio**: `/admin/mail-studio`  
**Admin SMTP Settings**: `/admin/settings` (Tab: ✉️ SMTP & Email Logins)  
**CLI Test**: `php artisan mail:test-templates [--smtp]`

---

## 8. Activity Audit Logging

All security-relevant actions are logged to the `activity_logs` table:

| Field | Description |
|-------|-------------|
| `event` | Action name: `login`, `logout`, `upload`, `download`, `approve`, `reject` |
| `user_id` | Actor's user ID |
| `subject_id` | Polymorphic target model ID |
| `subject_type` | Target model class |
| `ip_address` | Client IP (for download IP protection) |
| `user_agent` | Client browser/device |
| `metadata` | JSON context (e.g. resource title, program, file size) |

**Admin Audit Reports**: `/admin/reports`
**CSV Export**: `/admin/reports/export`

---

## 9. Deployment Topology

| Environment | Platform | Database | File Storage |
|------------|----------|----------|-------------|
| Local Dev | Native PHP / Docker Compose | SQLite | Disk / BLOB |
| Staging | Render | PostgreSQL 16 `basic-256mb` | BLOB + 10GB NVMe Disk |
| Production | Render | PostgreSQL 16 `basic-1gb` (`diskSizeGB: 10`) | BLOB + 10GB NVMe Disk |
| Alternative | Vercel (serverless) | External PostgreSQL | BLOB only (no disk) |
