# Backend Architecture & Stack Details

## 1. Architectural Pattern: Layered MVC

The backend follows a clean, modular MVC-inspired REST architecture:

```
Request ──> [server.js]
                 │
                 ├── Global Middleware (cors, express.json, urlencoded)
                 ├── Global Rate Limiter (/api/ -> apiLimiter)
                 │
                 └── [routes/*.js]
                         │
                         ├── Route-specific Middleware (protect, admin, upload, validation)
                         │
                         └── [controllers/*.js]
                                 │
                                 ├── [models/*.js] (Mongoose ODM) ──> MongoDB Atlas
                                 └── [utils/*.js] (emailService, notificationService)
```

---

## 2. Directory Anatomy

| Folder | Purpose | Core Responsibilities |
|---|---|---|
| `config/` | Environment & DB Connection | `db.js` initializes Mongoose connection with error handling and process exit on failure. |
| `controllers/` | Request Execution Logic | Encapsulates business logic, database queries, and status response formation. |
| `middleware/` | Pipeline Interceptors | `auth.js` (JWT & RBAC), `rateLimiter.js` (IP rate limiting), `upload.js` (Multer), `validation.js` (express-validator). |
| `models/` | Data Schemas & ODM | Mongoose schemas with hooks, validation rules, virtuals, and compound indexes. |
| `routes/` | Endpoint Routing | Maps HTTP verbs and paths to middleware chains and controller handlers. |
| `uploads/` | Physical Asset Storage | Local disk storage with partitioned subdirectories: `uploads/slides/` and `uploads/past-questions/`. |
| `utils/` | External Services & Helpers | `emailService.js` (Nodemailer HTML templates) and `notificationService.js` (Batch DB + email dispatcher). |

---

## 3. Middleware Execution Sequence

For every incoming HTTP request:
1. **CORS**: `cors()` enables cross-origin requests from frontend clients.
2. **Body Parsers**: `express.json()` and `express.urlencoded({ extended: true })` parse request payloads.
3. **Global Rate Limiter**: `apiLimiter` checks IP against the 15-minute window (default: 100 requests).
4. **Route Match**: Request is routed to `/api/{auth|resources|users|reviews|bookmarks|notifications|analytics}`.
5. **Authentication & Authorization**:
   - `protect`: Extracts `Authorization: Bearer <token>`, verifies JWT signature, verifies user active state in DB, attaches `req.user`.
   - `admin`: Checks if `req.user.role === 'admin'`. If false, aborts with `403 Forbidden`.
   - `checkLevel`: Optional level gating comparing `req.user.level` index with `resourceLevel`.
6. **Validation**: `express-validator` rules run and `validate` middleware checks for validation errors, responding with `400` if invalid.
7. **Controller Handler**: Business logic runs inside a `try...catch` block.
8. **Centralized Error Handler**: Catches unhandled errors or Multer errors (`LIMIT_FILE_SIZE`, `LIMIT_FILE_COUNT`), responding with `500` or `400`.

---

## 4. File Storage Strategy

- Handled by Multer (`middleware/upload.js`).
- Files are saved directly to local disk:
  - Slide files (`type === 'SLIDE'`) -> `uploads/slides/<timestamp>-<sanitized-filename>`
  - Past question files (`type === 'PAST_QUESTION'`) -> `uploads/past-questions/<timestamp>-<sanitized-filename>`
- Allowed MIME types: PDF, DOC, DOCX, PPT, PPTX.
- Maximum file size: 10MB per file.
- Single upload: field name `file`.
- Batch upload: field name `files` (up to 10 files).
- Automated Cleanup: If DB resource creation fails during upload, `fs.unlinkSync` automatically unlinks the freshly uploaded file.
