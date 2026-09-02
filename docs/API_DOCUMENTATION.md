# Web & API Route Reference: UEW Digital Library

## 1. System Health & Monitoring

### `GET /health`
- **Purpose**: Healthcheck endpoint for Render zero-downtime deployment, Docker container liveness, and uptime monitors.
- **Access**: Public (Unauthenticated).
- **Response Format**:
  ```json
  {
    "status": "healthy",
    "app": "UEW Digital Library Management System",
    "timestamp": "2026-09-02T03:10:00+00:00"
  }
  ```

---

## 2. Authentication Endpoints

| Method | URI | Name | Description |
|---|---|---|---|
| `GET` | `/login` | `login` | Displays student & faculty sign-in form |
| `POST` | `/login` | `login` | Authenticates using student ID or email |
| `POST` | `/logout` | `logout` | Invalidates session and CSRF cookie |
| `GET` | `/register` | `register` | Student self-registration form |
| `POST` | `/register` | `register` | Validates and creates student account |
| `GET` | `/forgot-password` | `password.request` | Password recovery form |
| `POST` | `/forgot-password` | `password.email` | Dispatches reset token email |
| `GET` | `/reset-password/{token}` | `password.reset` | Password update form |
| `POST` | `/reset-password` | `password.update` | Saves new password |

---

## 3. Student & Public Portal Endpoints

| Method | URI | Name | Description |
|---|---|---|---|
| `GET` | `/student/hub` | `student.hub` | Personalized Student Study Hub with enrolled stream courses |
| `GET` | `/dashboard` | `dashboard` | Multi-filter course catalog explorer |
| `GET` | `/resources/{id}` | `resources.show` | Detailed resource view, preview, and review feed |
| `GET` | `/resources/{id}/download` | `resources.download` | File download stream & counter increment |
| `GET` | `/resources/{id}/preview` | `resources.preview` | In-tab PDF document streaming |
| `POST` | `/resources/{id}/reviews` | `resources.reviews.store` | Submits/updates 1–5 star rating & qualitative feedback |
| `POST` | `/reviews/{id}/helpful` | `reviews.helpful` | Votes review as helpful |
| `DELETE` | `/reviews/{id}` | `reviews.destroy` | Deletes user's own review |
| `GET` | `/bookmarks` | `bookmarks.index` | Student saved study materials library |
| `POST` | `/resources/{id}/bookmark` | `resources.bookmark.toggle` | Toggles bookmark state |
| `PUT` | `/bookmarks/{id}` | `bookmarks.update` | Saves personal study notes on bookmarked item |
| `DELETE` | `/bookmarks/{id}` | `bookmarks.destroy` | Removes bookmarked material |
| `GET` | `/notifications` | `notifications.index` | Alert center listing |
| `POST` | `/notifications/{id}/read` | `notifications.read` | Marks single notification as read |
| `POST` | `/notifications/mark-all-read` | `notifications.mark-all-read` | Batch marks all notifications as read |
| `POST` | `/notifications/clear-read` | `notifications.clear-read` | Deletes read notifications |
| `GET` | `/profile` | `profile.edit` | Account settings, study stream, alert preferences |
| `PUT` | `/profile` | `profile.update` | Updates name, level, program, and notification toggles |
| `PUT` | `/profile/password` | `profile.password` | Updates account password |

---

## 4. Administrative & Command Center Endpoints

Protected by `auth` and `admin` middleware:

| Method | URI | Name | Description |
|---|---|---|---|
| `GET` | `/admin` | `admin.dashboard` | Executive Command Center overview & system health |
| `GET` | `/admin/analytics` | `admin.analytics` | Detailed KPI metrics, level distributions, audit trail |
| `GET` | `/admin/settings` | `admin.settings` | System configuration policies (Academic, Storage, Gating) |
| `PUT` | `/admin/settings` | `admin.settings.update` | Updates library operational parameters |
| `POST` | `/admin/settings/cache-clear` | `admin.settings.cache-clear` | Flushes application, route, and view caches |
| `GET` | `/admin/resources` | `admin.resources.index` | Materials inventory directory with search & filters |
| `GET` | `/admin/resources/create` | `admin.resources.create` | Upload interface with drag-and-drop support |
| `POST` | `/admin/resources` | `admin.resources.store` | Validates file, stores to disk, alerts students |
| `GET` | `/admin/resources/{id}/edit` | `admin.resources.edit` | Edits document metadata |
| `PUT` | `/admin/resources/{id}` | `admin.resources.update` | Updates resource metadata |
| `DELETE` | `/admin/resources/{id}` | `admin.resources.destroy` | Deletes resource and removes file from disk |
| `GET` | `/admin/categories` | `admin.categories.index` | Course syllabus category directory |
| `POST` | `/admin/categories` | `admin.categories.store` | Adds new course category |
| `PUT` | `/admin/categories/{id}` | `admin.categories.update` | Updates course code, title, level, or semester |
| `DELETE` | `/admin/categories/{id}` | `admin.categories.destroy` | Deletes category (when not in use) |
| `GET` | `/admin/users` | `admin.users.index` | Student directory & administrative users |
| `POST` | `/admin/users/{id}/toggle-active` | `admin.users.toggle-active` | Activates / deactivates user accounts |
| `POST` | `/admin/users/{id}/role` | `admin.users.role` | Updates user role (`student` vs `admin`) |
