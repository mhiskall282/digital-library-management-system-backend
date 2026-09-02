# Code Quality & Standards

1. **Naming Conventions**:
   - Models: PascalCase (`User.js`, `Resource.js`, `PasswordReset.js`).
   - Controllers: camelCase with suffix (`authController.js`, `resourceController.js`).
   - Routes: plural camelCase (`users.js`, `resources.js`, `reviews.js`).
   - Variables & Functions: camelCase (`uploadResource`, `generateToken`).
   - Constants & Enums: UPPERCASE_SNAKE_CASE (`SLIDE`, `PAST_QUESTION`, `L100`).

2. **Clean Async/Await**:
   - Prefer modern `async/await` syntax over nested `.then().catch()` chains for readability.

3. **HTTP Status Codes**:
   - `200 OK`: Successful fetch or update.
   - `201 Created`: Successful creation of resources, reviews, or bookmarks.
   - `400 Bad Request`: Validation failure or bad input.
   - `401 Unauthorized`: Missing or invalid JWT token.
   - `403 Forbidden`: Insufficient role or level permissions.
   - `404 Not Found`: Resource or user does not exist.
   - `500 Internal Server Error`: Unhandled database or filesystem failures.

4. **Git Commit Format**:
   - Use conventional commit prefixes: `feat:`, `fix:`, `refactor:`, `docs:`, `chore:`.
