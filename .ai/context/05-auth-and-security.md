# Authentication, Authorization & Security

## 1. Authentication Mechanism

- **Standard**: JSON Web Token (JWT) via `jsonwebtoken`.
- **Signing Algorithm**: HMAC-SHA256 with `process.env.JWT_SECRET`.
- **Payload**: `{ id: user._id }`.
- **Expiration**: `30d` (30 calendar days).
- **Transport**: Standard HTTP header:
  ```http
  Authorization: Bearer <jwt-token>
  ```

---

## 2. Password Security

- Passwords are encrypted before persisting to MongoDB using a Mongoose `pre('save')` hook.
- Library: `bcryptjs` with salt rounds = 10.
- Passwords are never returned in queries (`User.find().select('-password')`).
- Comparison is performed using the schema instance method:
  ```js
  userSchema.methods.comparePassword = async function(candidatePassword) {
    return await bcrypt.compare(candidatePassword, this.password);
  };
  ```

---

## 3. Role-Based Access Control (RBAC)

Two distinct roles are recognized:
1. `student` (Default): Can browse, download, bookmark, review, and manage personal profile/notifications.
2. `admin`: Has all student abilities plus uploading single/batch files, creating categories, deleting resources, managing users, and accessing analytics.

Middleware guards:
- `protect`: Verifies JWT validity and confirms that `req.user.isActive === true`.
- `admin`: Verifies `req.user.role === 'admin'`.

---

## 4. Academic Level Hierarchy (`checkLevel`)

The system establishes an educational level rank hierarchy:
`['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD']`

When enforced via `checkLevel`:
- Admins bypass level gating.
- A user can only access materials of their own level or lower:
  `userLevelIndex >= resourceLevelIndex`

---

## 5. Account Verification & Password Recovery Tokens

- **Email Verification**:
  - A cryptographically random 32-byte hex token is generated via `crypto.randomBytes(32).toString('hex')`.
  - Token expires after 24 hours (`emailVerificationExpires: Date.now() + 24 * 60 * 60 * 1000`).
  - Stored directly on the `User` document.
- **Password Reset**:
  - Independent `PasswordReset` model collection.
  - Generates 32-byte hex string.
  - Expires after 1 hour (`Date.now() + 3600000`).
  - Contains `used: Boolean` flag to prevent replay attacks.
- **Enumeration Protection**:
  - In `forgotPassword`, the endpoint returns `{ message: 'If that email exists, a password reset link has been sent' }` regardless of whether the user was found in the database.

---

## 6. Rate Limiting Rules

Implemented using `express-rate-limit`:
1. **Global API (`/api/`)**: 100 requests per 15 minutes per IP.
2. **Auth Endpoints (`/api/auth/login`)**: 5 requests per 15 minutes per IP.
3. **Email Sending Endpoints (`/api/auth/forgot-password`, `/api/auth/resend-verification`)**: 3 requests per hour per IP.
