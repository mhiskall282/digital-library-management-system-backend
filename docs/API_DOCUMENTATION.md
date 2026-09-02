# Web & API Route Reference — UEW Digital Library

## 1. System Health & Monitoring

### `GET /health`
- **Purpose**: Healthcheck endpoint for Render zero-downtime deployment, Docker container liveness probes, and uptime monitors.
- **Access**: Public (Unauthenticated)
- **Response**:
  ```json
  {
    "status": "healthy",
    "app": "UEW Digital Library Management System",
    "timestamp": "2026-09-02T04:00:00+00:00"
  }
  ```

---

## 2. Public Pages

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/` | `home` | Public landing page — hero banner, programs showcase, contributor leaderboard |
| `GET` | `/programs` | `programs.index` | Degree program directory grouped by stream and level |
| `GET` | `/docs` | `docs.index` | Non-technical student & staff user guide (A-to-Z walkthrough) |
| `GET` | `/doc` | — | Alias redirect → `/docs` |

---

## 3. Authentication Endpoints

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/login` | `login` | Sleek student/faculty sign-in form with switcher tabs |
| `POST` | `/login` | `login` | Authenticates using Student Index Number or institutional email |
| `POST` | `/logout` | `logout` | Invalidates session and CSRF cookie |
| `GET` | `/register` | `register` | Student self-registration & onboarding form |
| `POST` | `/register` | `register` | Validates and creates student account |
| `GET` | `/onboarding` | `onboarding` | First-time profile setup (program, level, phone) — awards +25 pts |
| `POST` | `/onboarding` | `onboarding.save` | Saves onboarding profile and credits bonus points |
| `GET` | `/forgot-password` | `password.request` | Password recovery form |
| `POST` | `/forgot-password` | `password.email` | Dispatches Zoho SMTP reset email |
| `GET` | `/reset-password/{token}` | `password.reset` | Password update form |
| `POST` | `/reset-password` | `password.update` | Saves new password |

---

## 4. Student & Faculty Portal

Protected by `auth` middleware:

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/student/hub` | `student.hub` | Personalized study hub with enrolled program resources and recommendations |
| `GET` | `/student/contribute` | `student.contribute` | Upload form — week selector (1–15), program, level, course code, file |
| `POST` | `/student/contribute` | `student.contribute.store` | Validates BLOB upload, queues for admin moderation |
| `GET` | `/dashboard` | `dashboard` | Catalog explorer with search, program/level/week/type filters |
| `GET` | `/resources/{id}` | `resources.show` | Resource detail — preview, reviews, download, bookmark |
| `GET` | `/resources/{id}/download` | `resources.download` | BLOB stream download with IP audit log |
| `GET` | `/resources/{id}/preview` | `resources.preview` | Inline PDF/PPTX streaming preview |
| `POST` | `/resources/{id}/reviews` | `resources.reviews.store` | Submit 1–5 star rating & written feedback |
| `POST` | `/reviews/{id}/helpful` | `reviews.helpful` | Vote review as helpful |
| `DELETE` | `/reviews/{id}` | `reviews.destroy` | Delete own review |
| `GET` | `/bookmarks` | `bookmarks.index` | Personal saved materials library |
| `POST` | `/resources/{id}/bookmark` | `resources.bookmark.toggle` | Toggle bookmark on/off |
| `PUT` | `/bookmarks/{id}` | `bookmarks.update` | Save private study notes on bookmark |
| `DELETE` | `/bookmarks/{id}` | `bookmarks.destroy` | Remove bookmark |
| `GET` | `/notifications` | `notifications.index` | In-app alert centre |
| `POST` | `/notifications/{id}/read` | `notifications.read` | Mark single notification read |
| `POST` | `/notifications/mark-all-read` | `notifications.mark-all-read` | Mark all read |
| `POST` | `/notifications/clear-read` | `notifications.clear-read` | Delete read notifications |
| `GET` | `/requests` | `requests.index` | Material request desk |
| `POST` | `/requests` | `requests.store` | Submit request for missing material |
| `GET` | `/profile` | `profile.edit` | Account settings, stream preferences, notification toggles |
| `PUT` | `/profile` | `profile.update` | Update name, level, program |
| `PUT` | `/profile/password` | `profile.password` | Change account password |

---

## 5. Admin Command Centre

Protected by `auth` + admin role middleware:

### Dashboard & Analytics
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin` | `admin.dashboard` | Executive overview — system KPIs, recent activity |
| `GET` | `/admin/analytics` | `admin.analytics` | KPI charts, level distribution, audit telemetry |

### Moderation
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/moderation` | `admin.moderation.index` | Student submission review queue |
| `POST` | `/admin/moderation/{id}/approve` | `admin.moderation.approve` | Approve submission → +50 pts credited to student |
| `POST` | `/admin/moderation/{id}/reject` | `admin.moderation.reject` | Reject submission with feedback |

### Download Approvals
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/downloads` | `admin.downloads.index` | IP-restricted download request queue |
| `POST` | `/admin/downloads/{id}/approve` | `admin.downloads.approve` | Grant download access |
| `POST` | `/admin/downloads/{id}/reject` | `admin.downloads.reject` | Deny download access |

### Material Requests
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/material-requests` | `admin.requests.index` | Missing-material request desk |
| `PUT` | `/admin/material-requests/{id}` | `admin.requests.update` | Update request status (in-progress, fulfilled, rejected) |

### Resource Management
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/resources` | `admin.resources.index` | Full materials inventory |
| `GET` | `/admin/resources/create` | `admin.resources.create` | Admin upload form (week-tagged BLOB) |
| `POST` | `/admin/resources` | `admin.resources.store` | Store and publish resource directly |
| `GET` | `/admin/resources/{id}/edit` | `admin.resources.edit` | Edit metadata |
| `PUT` | `/admin/resources/{id}` | `admin.resources.update` | Update resource |
| `DELETE` | `/admin/resources/{id}` | `admin.resources.destroy` | Permanently delete resource |

### User Management
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/users` | `admin.users.index` | Student directory & staff roster |
| `POST` | `/admin/users/{id}/toggle-active` | `admin.users.toggle-active` | Activate / deactivate account |
| `POST` | `/admin/users/{id}/role` | `admin.users.role` | Assign role (student / lecturer / staff / admin / superadmin) |
| `GET` | `/admin/users/import` | `admin.users.import` | Bulk CSV import interface |
| `POST` | `/admin/users/import` | `admin.users.import.store` | Process batch import |

### Categories
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/categories` | `admin.categories.index` | Course category directory |
| `POST` | `/admin/categories` | `admin.categories.store` | Create new category |
| `PUT` | `/admin/categories/{id}` | `admin.categories.update` | Update category |
| `DELETE` | `/admin/categories/{id}` | `admin.categories.destroy` | Delete category |

### Communications
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/broadcasts` | `admin.broadcasts.create` | Compose broadcast announcement |
| `POST` | `/admin/broadcasts` | `admin.broadcasts.store` | Send broadcast to all users via Zoho SMTP |
| `GET` | `/admin/mail-studio` | `admin.mail.index` | Email template preview, in-app mailbox, & dispatch studio |
| `POST` | `/admin/mail-studio/send` | `admin.mail.send` | Trigger test email dispatch (live SMTP or simulated) |
| `POST` | `/admin/mail-studio/simulate-incoming` | `admin.mail.simulate-incoming` | Simulate receiving an inbound student/staff message |
| `GET` | `/admin/mail-studio/logs/{id}` | `admin.mail.show-log` | Fetch detailed email log with full HTML payload |
| `DELETE` | `/admin/mail-studio/logs` | `admin.mail.clear-logs` | Clear email simulation logs |

### Reporting & Audit
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/reports` | `admin.reports.index` | Institutional audit log with date, type, and keyword filters |
| `GET` | `/admin/reports/export` | `admin.reports.export` | Stream CSV audit report download |

### Settings
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| `GET` | `/admin/settings` | `admin.settings` | System configuration — academic settings, storage, gating |
| `PUT` | `/admin/settings` | `admin.settings.update` | Update configuration values |
| `POST` | `/admin/settings/cache-clear` | `admin.settings.cache-clear` | Flush application, route, and view caches |

---

## 6. Response Conventions

All form submissions use standard Laravel redirects with flash messages:

| Flash Key | Meaning |
|-----------|---------|
| `session('success')` | Action completed successfully |
| `session('error')` | Action failed with explanation |
| `session('status')` | Informational status (e.g. reset link sent) |

Validation errors are returned via `$errors` Blade variable.

---

## 7. Security Headers

All authenticated routes enforce:
- CSRF token verification (`@csrf` in all forms)
- Session-based authentication (no JWT/API tokens)
- Activity logging (IP address + user-agent captured on sensitive actions)
- Role gate middleware (`App\Http\Middleware\AdminMiddleware`)
