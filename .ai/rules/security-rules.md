# Security Rules & Policies

1. **Secret Management**:
   - Never hardcode JWT secrets, MongoDB connection strings, or SMTP credentials in code.
   - Always load secrets via `process.env` backed by a local `.env` (ignored by git).
   - Commit an updated `.env.example` when adding new environment variables.

2. **Input Sanitization & Validation**:
   - Validate all user-submitted parameters with `express-validator` or `joi` before invoking controller logic.
   - Sanitize string inputs using `.trim()` and lowercase emails with `.normalizeEmail()`.

3. **Rate Limiting Protection**:
   - Auth endpoints (login, forgot-password, resend-verification) MUST have strict rate limiters to prevent brute-force attacks.
   - Keep rate limit windows active in all deployment environments.

4. **File Upload Security**:
   - Strictly validate file MIME type and extension (`pdf|doc|docx|ppt|pptx`).
   - Enforce 10MB maximum file size.
   - Do not trust user-provided filenames directly on the filesystem; prefix with `Date.now()` and sanitize whitespace.

5. **Authorization Guards**:
   - Never rely on client-side role checks alone. Ensure every administrative endpoint is guarded by `protect` AND `admin` middleware.
