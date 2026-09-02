# Backend Rules & Guidelines

1. **CommonJS Consistency**:
   - The backend runs on CommonJS (`require` and `module.exports`). Do NOT introduce ES Module syntax (`import`/`export`) in backend files unless the entire project `package.json` is migrated.

2. **Always Handle Errors Gracefully**:
   - Every asynchronous controller function must be wrapped in `try...catch`.
   - Never leave unhandled promise rejections.
   - If an error occurs during file upload processing, always unlink the temporary file from the disk before responding.

3. **Mongoose Model Best Practices**:
   - Always specify `{ timestamps: true }`.
   - Use `.select('-password')` whenever fetching user objects.
   - Use compound indexes for frequently searched multi-field criteria (e.g., `{ user: 1, resource: 1 }`).

4. **Middleware Separation**:
   - Business logic belongs in `controllers/`.
   - Route declarations and middleware attachments belong in `routes/`.
   - Data schemas belong in `models/`.
   - Do not write inline handler logic inside route files.

5. **Resource Level Gating**:
   - Remember the level order: `['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD']`.
   - Always allow `admin` users to bypass level gating.
