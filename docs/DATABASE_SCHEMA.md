# Database Schema & Entity Relationship Model — UEW Digital Library

The **UEW School of Business Digital Library Management System** utilizes a high-integrity relational database schema designed for multi-tenant academic level segregation, high-throughput binary content streaming, and strict intellectual property audit logging.

The database runs on **PostgreSQL 16** in production (Render) and supports **SQLite 3** for local unit and feature test execution.

---

## 1. Relational Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ REVIEWS : "authors"
    USERS ||--o{ BOOKMARKS : "creates"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ ACTIVITY_LOGS : "generates"
    USERS ||--o{ RESOURCES : "uploads"
    USERS ||--o{ DOWNLOAD_REQUESTS : "petitions"
    USERS ||--o{ MATERIAL_REQUESTS : "submits"
    
    CATEGORIES ||--o{ RESOURCES : "groups"
    
    RESOURCES ||--o{ REVIEWS : "receives"
    RESOURCES ||--o{ BOOKMARKS : "bookmarked_in"
    RESOURCES ||--o{ DOWNLOAD_REQUESTS : "authorized_for"

    USERS {
        bigint id PK
        string student_id "Indexed, Unique, Nullable"
        string first_name
        string last_name
        string email "Indexed, Unique"
        string password "Hashed bcrypt"
        enum level "L100, L200, L300, L400, MASTERS, PHD"
        string program "Indexed"
        string department "Nullable"
        enum role "student, lecturer, staff, admin, superadmin"
        boolean is_active "Default true"
        boolean is_onboarded "Default false"
        integer contributor_points "Default 0"
        string contributor_rank "Default 'Novice Contributor'"
        text bio "Nullable"
        string phone "Nullable"
        string avatar_path "Nullable"
        boolean email_notifications "Default true"
        boolean new_resource_alerts "Default true"
        timestamp email_verified_at "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string course_code "Indexed, Unique (e.g. BNF 211)"
        string course_name
        string program "e.g. Banking and Finance"
        enum level "L100, L200, L300, L400, MASTERS, PHD"
        enum semester "FIRST, SECOND"
        text description "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    RESOURCES {
        bigint id PK
        string title
        text description "Nullable"
        enum type "SLIDE, PAST_QUESTION"
        enum status "PENDING_REVIEW, APPROVED, REJECTED"
        text rejection_reason "Nullable"
        bigint reviewed_by "FK -> users.id, Nullable"
        timestamp reviewed_at "Nullable"
        bigint category_id "FK -> categories.id"
        enum level "L100, L200, L300, L400, MASTERS, PHD"
        integer week "Nullable, 1-15 module"
        string academic_year "e.g. 2023/2024"
        string file_name
        string file_path "Nullable (disk storage path)"
        bytea file_blob "Binary data for zero-disk streaming"
        bigint file_size "Bytes"
        string mime_type "application/pdf, etc."
        integer downloads "Default 0"
        decimal average_rating "Precision 3,2, Default 0.00"
        integer total_reviews "Default 0"
        bigint uploaded_by "FK -> users.id"
        json tags "Nullable string array"
        timestamp created_at
        timestamp updated_at
    }

    REVIEWS {
        bigint id PK
        bigint user_id "FK -> users.id"
        bigint resource_id "FK -> resources.id"
        smallint rating "1 to 5"
        text comment "Nullable"
        integer helpful_votes "Default 0"
        json helpful_voters "Array of user IDs"
        timestamp created_at
        timestamp updated_at
    }

    BOOKMARKS {
        bigint id PK
        bigint user_id "FK -> users.id"
        bigint resource_id "FK -> resources.id"
        text notes "Private student revision notes"
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id "FK -> users.id"
        string type "SYSTEM, NEW_RESOURCE, SUBMISSION_APPROVED, etc."
        string title
        text message
        string link "Nullable"
        bigint resource_id "FK -> resources.id, Nullable"
        boolean is_read "Default false"
        timestamp created_at
        timestamp updated_at
    }

    ACTIVITY_LOGS {
        bigint id PK
        string action "DOWNLOAD, SUBMISSION, LOGIN, etc."
        bigint user_id "FK -> users.id, Nullable"
        string subject_type "Polymorphic class"
        bigint subject_id "Polymorphic ID"
        json metadata "IP, user-agent, file details"
        timestamp created_at
        timestamp updated_at
    }

    SETTINGS {
        bigint id PK
        string key "Unique index"
        text value "Nullable"
        string description "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    DOWNLOAD_REQUESTS {
        bigint id PK
        bigint user_id "FK -> users.id"
        bigint resource_id "FK -> resources.id"
        text reason "Academic justification"
        enum status "PENDING, APPROVED, REJECTED"
        text rejection_reason "Nullable"
        bigint reviewed_by "FK -> users.id, Nullable"
        timestamp reviewed_at "Nullable"
        string ip_address "Nullable"
        string user_agent "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    MATERIAL_REQUESTS {
        bigint id PK
        bigint user_id "FK -> users.id"
        string title
        string course_code
        enum level "L100-PHD"
        text description
        enum status "OPEN, IN_PROGRESS, FULFILLED, CLOSED"
        text admin_response "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    EMAIL_LOGS {
        bigint id PK
        string sender "e.g. noreply@uew.edu.gh"
        string recipient "Student / Staff email"
        string subject
        text body_html
        string template "welcome, security, broadcast, etc."
        enum status "SENT, SIMULATED, FAILED, RECEIVED"
        text error "Nullable"
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Table Specifications & Indexes

### `users`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `email` (UNIQUE)
  - `student_id` (UNIQUE, NULLABLE)
  - `level`, `program`, `role` (B-Tree indexes for fast student filtering)
- **Casts**: `is_active` (boolean), `is_onboarded` (boolean), `contributor_points` (integer), `email_verified_at` (datetime).

### `categories`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `course_code` (UNIQUE, e.g. `BBA 111`, `BNF 211`, `BIS 311`)
  - `level`, `semester`, `program` (B-Tree indexes for curriculum drill-down)

### `resources`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `category_id` (FK to `categories.id`)
  - `uploaded_by` (FK to `users.id`)
  - `status`, `level`, `type`, `week` (Composite & standalone indexes for high-speed faceted search)
- **Binary Data Handling (`file_blob`)**:
  - PostgreSQL uses the native `bytea` binary column type.
  - Raw binary strings are hex-encoded as `\x[hex]` upon insertion via `Resource::prepareBlobForStorage()`.
  - Streamed out directly via `Resource::getRawBlob()`.
  - `file_blob` is included in the model's `$hidden` attribute to ensure it is never serialized into JSON responses, preventing PHP memory exhaustion and `JsonException`.

### `download_requests`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `user_id` + `resource_id` (Composite index for status lookups)
  - `status` (B-Tree index for the Admin Approvals Desk)

### `material_requests`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `user_id` (FK to `users.id`)
  - `status` (B-Tree index for ticket workflow management)

### `email_logs`
- **Primary Key**: `id` (`BIGINT UNSIGNED AUTO_INCREMENT`)
- **Indexes**:
  - `recipient`, `status`, `created_at` (B-Tree indexes for Mail Studio filtering and diagnostics)

---

&copy; University of Education, Winneba &mdash; School of Business.
