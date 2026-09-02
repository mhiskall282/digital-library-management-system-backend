# Database Schema: UEW Digital Library Management System

## 1. Relational Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ REVIEWS : "writes"
    USERS ||--o{ BOOKMARKS : "saves"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ ACTIVITY_LOGS : "triggers"
    USERS ||--o{ RESOURCES : "uploads"
    CATEGORIES ||--o{ RESOURCES : "contains"
    RESOURCES ||--o{ REVIEWS : "has"
    RESOURCES ||--o{ BOOKMARKS : "referenced_by"

    USERS {
        bigint id PK
        string student_id "Indexed, Unique, Nullable"
        string first_name
        string last_name
        string email "Indexed, Unique"
        string password "Bcrypt hashed"
        enum level "L100, L200, L300, L400, MASTERS, PHD"
        string program
        string department "Nullable"
        enum role "student, lecturer, admin, superadmin"
        boolean is_active "Default true"
        string avatar_path "Nullable"
        boolean email_notifications "Default true"
        boolean new_resource_alerts "Default true"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string course_code "Indexed, Unique (e.g. BBA 111)"
        string course_name
        enum level "L100-PHD"
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
        bigint category_id FK
        enum level "L100-PHD"
        string academic_year "e.g. 2023/2024"
        string file_path
        string file_name
        bigint file_size "Bytes"
        string mime_type
        json tags "Nullable"
        bigint uploaded_by FK
        integer downloads "Default 0"
        decimal average_rating "Precision 3, Scale 2, Default 0.00"
        integer total_reviews "Default 0"
        timestamp created_at
        timestamp updated_at
    }

    REVIEWS {
        bigint id PK
        bigint resource_id FK
        bigint user_id FK
        smallint rating "1 to 5"
        text comment "Nullable"
        integer helpful_count "Default 0"
        timestamp created_at
        timestamp updated_at
    }

    BOOKMARKS {
        bigint id PK
        bigint user_id FK
        bigint resource_id FK
        text notes "Personal student revision notes"
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type "NEW_RESOURCE, SYSTEM, GENERAL"
        string title
        text message
        bigint resource_id "Nullable"
        string link "Nullable"
        boolean is_read "Default false"
        timestamp created_at
        timestamp updated_at
    }

    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK "Nullable"
        string action "LOGIN, UPLOAD, DOWNLOAD, etc."
        string subject_type "Nullable"
        bigint subject_id "Nullable"
        json metadata "Nullable"
        string ip_address "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    SETTINGS {
        bigint id PK
        string key "Unique"
        text value "Nullable"
        string type "string, boolean, integer, json"
        string group "academic, storage, security, notifications"
        string description "Nullable"
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Table Specifications & Indexes

### `users`
- **Unique Indexes**: `users_email_unique`, `users_student_id_unique`.
- **Role Hierarchy**: `superadmin` > `admin` > `lecturer` > `student`.

### `categories`
- **Unique Indexes**: `categories_course_code_unique`.
- **Foreign Constraints**: Referenced by `resources.category_id` (`ON DELETE RESTRICT`).

### `resources`
- **Rating Synchronization**: Automatically updated by Eloquent `Review::saved` and `Review::deleted` events, maintaining cached `average_rating` and `total_reviews` columns to eliminate costly runtime SQL aggregation on catalog listings.

### `settings`
- Caches all operational values in memory via `Cache::rememberForever()`, invalidated on any `Setting::set()` call.
