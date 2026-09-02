# Security Audit & QA Report — UEW Digital Library

## 1. Security Architecture Overview

The UEW Digital Library implements a layered defence-in-depth strategy:

```
Browser Request
    │
    ├── HTTPS (TLS 1.2+) enforced by Nginx/Render
    │
    ├── CSRF Token Validation (every state-changing form)
    │
    ├── Laravel Session Driver (database, signed cookie on Vercel)
    │
    ├── Authentication Middleware (auth)
    │
    ├── Role Gate Middleware (AdminMiddleware — 5-tier RBAC)
    │
    ├── Eloquent ORM (parameterized queries — no raw SQL injection risk)
    │
    └── Response (Blade auto-escapes all output — XSS safe)
```

---

## 2. Authentication Security

| Control | Implementation | Status |
|---------|---------------|--------|
| Password Hashing | `bcrypt` via Laravel `Hash::make()` — 12 rounds | ✅ |
| Session Regeneration | `session()->regenerate()` after login | ✅ |
| CSRF Protection | `VerifyCsrfToken` middleware on all POST/PUT/DELETE | ✅ |
| Rate Limiting | Laravel `throttle:login` on POST `/login` (5 attempts / 1 min) | ✅ |
| Brute-Force Lockout | Redirects with generic error (no credential enumeration) | ✅ |
| Remember Me | Signed, long-lived cookie (Laravel standard) | ✅ |
| Logout | Full session invalidation + CSRF token regeneration | ✅ |

---

## 3. Role-Based Access Control (RBAC)

The `AdminMiddleware` (`app/Http/Middleware/AdminMiddleware.php`) enforces role hierarchy:

```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->canModerate()) {
        abort(403, 'Insufficient permissions.');
    }
    return $next($request);
}
```

`canModerate()` returns `true` for roles: `lecturer`, `staff`, `admin`, `superadmin`.

All `/admin/*` routes are wrapped in this middleware. Students receive a `403 Forbidden` response if they attempt to access admin endpoints directly.

---

## 4. File Upload Security

| Control | Implementation |
|---------|---------------|
| MIME Type Validation | `mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip` in form request |
| File Size Limit | `max:51200` (50 MB) |
| Storage Strategy | Binary BLOB in database (no executable file path exposure) |
| Content-Disposition | `attachment` enforced — files cannot be executed inline maliciously |
| No Direct URL | Files served only via authenticated controller stream — no public URL |

---

## 5. Download IP Protection & Audit Logging

Every file download triggers:

1. An `ActivityLog` entry with:
   - `user_id`, `event: 'download'`, `ip_address`, `user_agent`, `subject_id`, `metadata`
2. An increment of `resources.download_count`.
3. Optionally a `DownloadRequest` approval gate (configurable via Admin Settings).

The Admin Audit Logs page (`/admin/reports`) allows filtering by event type, date range, and keyword search. Exportable as CSV.

---

## 6. Email Security

| Control | Implementation |
|---------|---------------|
| Transport Encryption | Zoho SMTP over SSL (port 465) |
| Credential Storage | `MAIL_USERNAME` and `MAIL_PASSWORD` in `.env` (never committed) |
| Template Output | All user-generated content escaped via Blade `{{ }}` syntax |
| Password Reset Tokens | Laravel `password_reset_tokens` table, single-use, 60-min expiry |
| Security Alert Emails | Dispatched on: password reset initiated, new login from new device |

---

## 7. Transport Layer Security & Mixed Content Prevention

| Control | Implementation |
|---------|---------------|
| Reverse Proxy Trust | `bootstrap/app.php` sets `$middleware->trustProxies(at: '*')` to honor `X-Forwarded-Proto` |
| Force HTTPS Scheme | `AppServiceProvider` executes `URL::forceScheme('https')` in production |
| Content Security Policy | `<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">` in all layouts |
| Nginx Reverse Proxy Headers | Nginx passes `HTTP_X_FORWARDED_PROTO`, `HTTP_X_FORWARDED_PORT 443`, and `HTTPS on` |
| Session Cookie Security | `Secure` and `HttpOnly` flags active in production session configurations |

---

## 7. SQL Injection Prevention

All database interactions use Eloquent ORM and the Query Builder with parameterized bindings. No raw SQL strings with user input. Example:

```php
// SAFE — parameterized via Eloquent
Resource::where('course_code', $request->course_code)
        ->filterByWeek($request->week)
        ->get();

// SAFE — Query Builder bindings
DB::table('users')->where('email', '=', $request->email)->first();
```

---

## 8. Cross-Site Scripting (XSS) Prevention

All output in Blade templates uses `{{ }}` which applies `htmlspecialchars()` automatically:

```blade
{{ $resource->title }}           {{-- ✅ auto-escaped --}}
{!! $resource->html_content !!}} {{-- ⚠️ raw — only used for trusted admin content --}}
```

Raw HTML output (`{!! !!}`) is only used for:
- Email template preview in Admin Mail Studio (admin-only route)
- System-generated HTML (trusted source, not user content)

---

## 9. Test Suite

Run the full PHPUnit suite:

```bash
php artisan test
```

Current results: **23 tests, 62 assertions, 0 failures**

### Test Coverage

| Test | What It Verifies |
|------|-----------------|
| `test_health_check_endpoint` | `/health` returns 200 + JSON status |
| `test_public_landing_page_loads_for_guests` | Landing page renders without auth |
| `test_programs_directory_loads` | Programs listing accessible |
| `test_login_screen_has_no_exposed_credentials` | Login page renders without credential leakage |
| `test_student_login_flow` | Student can authenticate with Index Number |
| `test_student_can_view_student_hub` | Student hub accessible post-login |
| `test_student_can_view_catalog` | Catalog Explorer renders with resources |
| `test_student_can_contribute_material_for_moderation` | File upload queues correctly |
| `test_admin_can_approve_submission_and_points_are_awarded` | Approval awards +50 pts |
| `test_download_approval_request_flow` | Download request/approve cycle |
| `test_material_request_desk_flow` | Material request creation & admin view |
| `test_admin_can_broadcast_announcement` | Broadcast dispatch (log driver) |
| `test_admin_bulk_import_sample_csv_download` | CSV sample download streams |
| `test_admin_can_access_admin_portal` | Admin dashboard accessible to admin role |
| `test_student_cannot_access_admin_portal` | Admin portal blocked for student role (403) |
| `test_resources_filtered_by_week` | Week filter returns correct resource subset |
| `test_binary_blob_streaming_download` | BLOB stored in DB streams correctly |
| `test_admin_can_access_audit_reports_and_export_csv` | Reports page + CSV export |
| `test_docs_page_loads_for_guests_and_students` | `/docs` page loads without auth |
| `test_doc_alias_redirects_to_docs` | `/doc` redirects to `/docs` |
| `test_login_page_renders_with_student_and_staff_switchers` | Login redesign renders |
| `test_mail_studio_loads_for_admin` | Mail Studio accessible to admin |

---

## 10. Known Limitations & Remediation Roadmap

| Item | Status | Remediation |
|------|--------|------------|
| Zoho SMTP `554 5.7.8` | ⚠️ Account-side | Enable SMTP in Zoho Admin Console or generate App Password |
| Queue driver: sync | ⚠️ Dev mode | Upgrade to `database` or `redis` queue for async email on Render |
| File virus scanning | 🔲 Not implemented | Add ClamAV scan on file upload in production |
| 2FA for admin logins | 🔲 Not implemented | Add TOTP (Google Authenticator) via `pragmarx/google2fa-laravel` |
| API rate limiting | ✅ Login throttled | Extend to resource download (5/min) if needed |
