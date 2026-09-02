# AI Assistant Workspace Index: UEW Digital Library

Welcome to the AI context repository for the **UEW School of Business Digital Library Management System**.

This `.ai/` directory provides complete architectural context, domain knowledge, operational skills, and coding rules for AI coding assistants and developers collaborating on this project.

---

## 📁 Directory Structure

```
.ai/
├── INDEX.md                            # Master entry point and system directory
├── context/
│   ├── 01-system-overview.md           # Project mission, domain, and user personas
│   ├── 02-backend-architecture.md      # Express 5, Mongoose, pipeline, and middleware
│   ├── 03-data-models-and-relations.md # Comprehensive 7-model database specification
│   ├── 04-api-endpoint-directory.md    # Complete catalog of all endpoints and contracts
│   ├── 05-auth-and-security.md         # JWT, RBAC, level gating, and reset protocols
│   └── 06-frontend-integration-contract.md # Frontend API consumption & state blueprints
├── skills/
│   ├── api-client-generator/SKILL.md   # Generating typed API clients & Query hooks
│   ├── frontend-feature-scaffolding/SKILL.md # Scaffolding React/Next.js library pages
│   └── backend-extender/SKILL.md       # Extending backend routes, controllers, and models
└── rules/
    ├── backend-rules.md                # Server-side coding standards and patterns
    ├── frontend-rules.md               # Visual design, UX, accessibility, and UI rules
    ├── security-rules.md               # Secret hygiene, authentication, and RBAC rules
    └── code-quality.md                 # Conventions, naming, and git commit guidelines
```

---

## 🧠 Core System Quick Facts

| Dimension | Specification |
|---|---|
| **Domain** | University of Education, Winneba (UEW) - School of Business |
| **Backend Runtime** | Node.js (>=18.0.0) CommonJS |
| **Framework** | Express.js 5.2.1 |
| **Database** | MongoDB Atlas via Mongoose 9.1.5 |
| **Authentication** | Bearer JWT (HMAC-SHA256, 30-day expiry) |
| **Roles** | `student` (default), `admin` |
| **Academic Levels** | `L100`, `L200`, `L300`, `L400`, `MASTERS`, `PHD` |
| **Resource Types** | `SLIDE`, `PAST_QUESTION` |
| **Semesters** | `FIRST`, `SECOND` |
| **File Storage** | Multer disk storage (`uploads/slides`, `uploads/past-questions`) max 10MB |
| **Rate Limiting** | Express-rate-limit (API: 100 req/15min, Auth: 5 req/15min, Email: 3 req/1h) |
| **Mail Service** | Nodemailer via Gmail SMTP (STARTTLS 587) |

---

## 🎯 How to Use this Context

1. **Before writing backend code**: Read [backend-rules.md](file:///c:/Users/user/Desktop/digital-library-management-system-backend/.ai/rules/backend-rules.md) and [02-backend-architecture.md](file:///c:/Users/user/Desktop/digital-library-management-system-backend/.ai/context/02-backend-architecture.md).
2. **Before scaffolding frontend features**: Read [06-frontend-integration-contract.md](file:///c:/Users/user/Desktop/digital-library-management-system-backend/.ai/context/06-frontend-integration-contract.md) and [frontend-rules.md](file:///c:/Users/user/Desktop/digital-library-management-system-backend/.ai/rules/frontend-rules.md).
3. **When implementing endpoints**: Use [04-api-endpoint-directory.md](file:///c:/Users/user/Desktop/digital-library-management-system-backend/.ai/context/04-api-endpoint-directory.md) for exact request/response schemas.
