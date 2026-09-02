---
name: backend-extender
description: Step-by-step instructions for adding new models, controllers, middleware, and endpoints to the UEW Digital Library Express backend.
---

# Backend Extender Skill

## When to Use
Use this skill when adding new features, database collections, or routes to the backend system.

## Golden Workflow for New Endpoints

1. **Define Mongoose Model (`models/NewFeature.js`)**
2. **Define Validation Rules (`middleware/validation.js`)**
3. **Implement Controller (`controllers/newFeatureController.js`)**
4. **Define Route (`routes/newFeatures.js`)**
5. **Mount in `server.js`**
