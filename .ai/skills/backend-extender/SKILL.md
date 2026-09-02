---
name: backend-extender
description: Step-by-step instructions for adding new models, controllers, middleware, and endpoints to the UEW Digital Library Express backend.
---

# Backend Extender Skill

## When to Use
Use this skill when adding new features, database collections, or routes to the backend system.

## Golden Workflow for New Endpoints

1. **Define Mongoose Model (`models/NewFeature.js`)**:
   - Explicitly define fields, types, required constraints, and enum choices.
   - Add `{ timestamps: true }`.
   - Add compound indexes for frequent query paths.

2. **Define Validation Rules (`middleware/validation.js`)**:
   - Create express-validator array matching expected body fields.
   - Always run `validate` middleware after rules.

3. **Implement Controller (`controllers/newFeatureController.js`)**:
   - Wrap entire handler in `try { ... } catch (error) { ... }`.
   - Return appropriate HTTP status codes (`201` for created, `200` for success, `400` for bad request, `403` for forbidden, `404` for not found, `500` for server error).
   - Sanitize responses (never return raw user passwords).

4. **Define Route (`routes/newFeatures.js`)**:
   - Mount middleware (`protect`, `admin`, `rateLimiter`, `validate`).
   - Export Express router.

5. **Mount in `server.js`**:
   - `app.use('/api/new-features', require('./routes/newFeatures'));`
   - Update welcome endpoint feature list in `server.js`.
