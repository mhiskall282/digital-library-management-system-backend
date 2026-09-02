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

## Implementation Guidelines

### 1. Axios Instance with Interceptors
```typescript
import axios from 'axios';

export const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:5000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = typeof window !== 'undefined' ? localStorage.getItem('token') : null;
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && typeof window !== 'undefined') {
      localStorage.removeItem('token');
      // Redirect to login if appropriate
    }
    return Promise.reject(error);
  }
);
```

### 2. TanStack Query Hook Pattern
Always wrap query operations in hooks with sensible query keys:
```typescript
export const useResources = (params: SearchParams) => {
  return useQuery({
    queryKey: ['resources', params],
    queryFn: () => resourceService.advancedSearch(params),
  });
};
```
