---
name: api-client-generator
description: Generates TypeScript interfaces, Axios/Fetch service classes, and TanStack Query hooks that mirror the UEW Digital Library backend endpoints.
---

# API Client Generator Skill

## When to Use
Use this skill when scaffolding or expanding the frontend client integration layer to ensure 100% type-safety and alignment with backend routes, request bodies, and response payloads.

## Standard Architecture
The generated client should reside in `src/services/api/` (or `lib/api/`):
```
src/
├── types/
│   └── api.ts             # TypeScript interfaces for models & responses
└── services/
    ├── apiClient.ts       # Axios instance with auth interceptor
    ├── authService.ts     # Auth endpoints
    ├── resourceService.ts # Resource & category endpoints
    ├── reviewService.ts   # Review & rating endpoints
    ├── bookmarkService.ts # Bookmark endpoints
    ├── notificationService.ts # Notification endpoints
    └── analyticsService.ts    # Admin analytics endpoints
```
