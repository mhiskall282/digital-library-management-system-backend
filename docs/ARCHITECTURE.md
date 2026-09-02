# Architecture Overview — UEW School of Business Digital Library

The **UEW School of Business Digital Library Management System** is an enterprise-grade academic learning and resource distribution repository built for the University of Education, Winneba (UEW), Faculty of Business Education.

The system is built as a robust, resilient Laravel 11/12 application using **Tailwind CSS v4**, **Alpine.js v3**, **PostgreSQL 16**, and multi-stage containerized deployments on **Render**.

---

## 1. High-Level System Architecture

The application adopts a modular Model-View-Controller (MVC) pattern with dedicated service domains, high-performance binary streaming capabilities, automated IP compliance auditing, and role-based access control.

```mermaid
flowchart TB
    subgraph Clients["Clients & Edge Tier"]
        BrowserMobile["📱 Mobile Browsers\n(Responsive Drawer & PWA Ready)"]
        BrowserDesktop["💻 Desktop Browsers\n(Full Catalog & Admin Command Center)"]
        CDN["🌐 Cloudflare / Edge Network\n(SSL Termination & Static Assets)"]
    end

    subgraph Ingress["Web Server Tier (Docker on Render)"]
        Nginx["🛡️ Nginx Reverse Proxy\n(Gzip, FastCGI, Static Asset Cache)"]
        Supervisord["⚙️ Supervisor Process Manager\n(PHP-FPM & Queue Workers)"]
    end

    subgraph AppLayer["Application Tier (Laravel 11 / PHP 8.4)"]
        Router["HTTP Router & Middleware\n(Auth, RBAC, EnsureActive, RateLimiter)"]
        
        subgraph Subsystems["Core Modules & Subsystems"]
            CatalogSubsystem["📚 Catalog & Program Explorer\n(Hierarchy: Program → Level → Week)"]
            ContribSubsystem["📝 Student Contribution Desk\n(+50 Pts, Moderation Queue)"]
            IPAuditSubsystem["🔒 IP Audit & Download Approvals\n(Reason Justification, Client IP Tracking)"]
            MailStudio["✉️ Mail Studio & Outbound SMTP\n(Zoho / Gmail presets, In-App Simulator)"]
            AdminConsole["⚡ Executive Command Center\n(Metrics, User Ingestion, Audit Logs)"]
        end

        subgraph ServiceLayer["Domain Services"]
            RecService["🧠 RecommendationService\n(Smart program-tailored suggestions)"]
            MailService["📨 Dynamic Mail Binding\n(Zero-restart DB configuration)"]
            BlobEngine["⚡ Zero-Disk Streaming Engine\n(PostgreSQL \\x hex bytea & disk fallback)"]
        end
    end

    subgraph DataTier["Data & Storage Tier"]
        PostgresDB[("🐘 PostgreSQL 16 Managed DB\n(Relations, BLOBs, Audit Logs, Settings)")]
        DiskStorage[("💾 Persistent NVMe Storage\n(10GB Mounted at /storage/app/public)")]
        ExternalSMTP["🌐 External SMTP Providers\n(Zoho Mail, Google Workspace)"]
    end

    BrowserMobile --> CDN
    BrowserDesktop --> CDN
    CDN --> Nginx
    Nginx --> Supervisord
    Supervisord --> Router
    Router --> Subsystems
    Subsystems --> ServiceLayer
    ServiceLayer --> PostgresDB
    ServiceLayer --> DiskStorage
    ServiceLayer -.-> ExternalSMTP
```

---

## 2. Role-Based Access Control (RBAC) Architecture

The application enforces a strictly partitioned 5-tier access hierarchy:

```mermaid
flowchart TD
    Guest["👤 Guest Scholar / Visitor\n(Public Access)"]
    Student["🎓 Enrolled Student (Tier 1)\n(Level-Gated Access)"]
    Lecturer["👨‍🏫 Lecturer / Academic Staff (Tier 2)\n(Direct Resource Publishing)"]
    Staff["🏛️ Library Curator / Staff (Tier 3)\n(Moderation & Material Requests)"]
    Admin["⚙️ System Administrator (Tier 4)\n(Users, Settings, SMTP, Broadcasts)"]
    SuperAdmin["👑 Super Administrator (Tier 5)\n(Full Institutional Governance)"]

    Guest -->|Self-Registration & Index No.| Student
    Student -->|Browse L100-PhD, Stream Slides, Request Missing Materials| Student
    Student -->|Submit Lecture Slides & Past Questions| ContribQueue["Moderation Desk"]
    
    Lecturer -->|Publish Course Material Direct (Bypass Queue)| Catalog["Approved Catalog"]
    
    Staff -->|Review Submissions, Approve/Reject, Handle Requests| ContribQueue
    ContribQueue -->|Approved (+50 Pts)| Catalog

    Admin -->|Manage All Resources, Audit Logs, Ingest CSV Users, Send Broadcasts| Platform["Platform Core"]
    SuperAdmin -->|Manage Roles, Reset SMTP, Clear Caches, Manage RBAC| Platform
```

### RBAC Capabilities Matrix

| Privilege / Capability | Guest | Student | Lecturer | Staff | Admin | SuperAdmin |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Browse Public Programs Directory (`/programs`) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Read Official Documentation (`/docs`) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Access Level-Gated Catalog Explorer (`/dashboard`) | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Download Material (Subject to Level & IP Audit) | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Submit Materials for Moderation Desk | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Direct Unmoderated Uploads | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ |
| Review & Moderate Student Submissions | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| Approve / Decline Download Requests | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| Send Departmental Broadcast Alerts & HTML Emails | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Configure Dynamic SMTP & Cache Operations | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Bulk CSV User Ingestion & Role Assignment | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |

---

## 3. Resource Ingestion & Binary BLOB Flow

To guarantee high availability on cloud container environments (such as Render) where ephemeral filesystems can reset, the application supports a dual-layer persistence model: physical storage disk and raw PostgreSQL binary `bytea` streaming.

```mermaid
sequenceDiagram
    autonumber
    actor Scholar as Student / Contributor
    participant Controller as ContributionController
    participant Model as Resource Model
    participant Postgres as PostgreSQL 16 (DB)
    participant Disk as Persistent NVMe Disk
    actor Curator as Library Curator / Admin

    Scholar->>Controller: POST /student/contribute (Title, Course, Level, Week, File)
    Controller->>Controller: Validate MIME, File Extension & Size (< 100MB)
    Controller->>Disk: Store copy on public storage disk (/storage/app/public/resources)
    alt File Size <= 8MB
        Controller->>Model: Resource::prepareBlobForStorage(rawBytes)
        Note over Model: Encodes as PostgreSQL \\x[hex] string to prevent UTF-8 errors
        Model-->>Controller: Formatted hex string
    else File Size > 8MB
        Note over Controller: Skip in-db blob to optimize RAM and DB packet size
    end
    Controller->>Postgres: INSERT into resources (status='PENDING_REVIEW', file_blob=...)
    Controller->>Postgres: INSERT into activity_logs ('STUDENT_SUBMISSION')
    Controller-->>Scholar: 200 OK Redirect (Awaiting Curatorial Review)

    Note over Curator: Later in Admin Desk (/admin/moderation)
    Curator->>Controller: POST /admin/moderation/{id}/approve
    Controller->>Postgres: UPDATE resources SET status='APPROVED'
    Controller->>Postgres: UPDATE users SET contributor_points += 50
    Controller->>Postgres: INSERT into notifications ('Submission Approved +50 Pts')
    Controller-->>Curator: 200 OK (Material is now live in Catalog)
```

---

## 4. Download Authorization & IP Compliance Audit Flow

When intellectual property safeguards (`require_download_approval`) are enabled, students must provide a legitimate study justification before downloading sensitive materials. Every download event logs the client's public IP address, user-agent, and academic affiliation.

```mermaid
sequenceDiagram
    autonumber
    actor Student as Authenticated Student
    participant Controller as ResourceController
    participant Postgres as PostgreSQL 16
    participant StreamEngine as Binary Stream Engine
    actor Staff as Library Curator

    Student->>Controller: GET /resources/{id}/download
    alt Level Mismatch (Student Level < Material Level)
        Controller-->>Student: 403 Forbidden ("Level Access Restricted")
    else Download Approval Required (Setting Enabled)
        Controller->>Postgres: Check download_requests for approved record
        alt No Approved Record
            Controller-->>Student: 302 Redirect with request modal
            Student->>Controller: POST /resources/{id}/request-download (Justification)
            Controller->>Postgres: INSERT into download_requests (status='PENDING')
            Controller-->>Student: 200 OK ("Request Submitted for Curatorial Review")
            Staff->>Controller: POST /admin/downloads/{id}/approve
            Controller->>Postgres: UPDATE download_requests SET status='APPROVED'
        end
    end

    Note over Controller,StreamEngine: Download Authorized
    Controller->>Postgres: UPDATE resources SET downloads = downloads + 1
    Controller->>Postgres: INSERT into activity_logs ('DOWNLOAD', client_ip, user_agent)
    
    alt Binary BLOB Available
        Controller->>Postgres: SELECT file_blob FROM resources WHERE id = ?
        Controller->>StreamEngine: Decode hex \\x stream to raw bytes
        StreamEngine-->>Student: 200 OK (application/pdf, Content-Disposition: attachment)
    else Disk Fallback
        Controller->>StreamEngine: Storage::disk('public')->download()
        StreamEngine-->>Student: 200 OK (Binary stream from persistent NVMe)
    end
```

---

## 5. Material Request Lifecycle

Students can petition the library faculty for missing lecture slides, syllabus modules, or exam archives through the Support & Request Desk (`/requests`).

```mermaid
stateDiagram-v2
    [*] --> OPEN: Student submits missing material request
    OPEN --> IN_PROGRESS: Library curator acknowledges ticket & contacts department
    IN_PROGRESS --> FULFILLED: Material sourced & uploaded to catalog (Student alerted via notification)
    IN_PROGRESS --> CLOSED: Request duplicate, out-of-scope, or archived
    OPEN --> CLOSED: Rejected with administrative reason
    FULFILLED --> [*]
    CLOSED --> [*]
```

---

## 6. Real-Time Dynamic SMTP & Email Studio Architecture

The application avoids brittle `.env` restarts for institutional communication by supporting real-time database-driven mail transport reconfiguration:

```mermaid
flowchart LR
    subgraph AdminActions["Administrative Control"]
        SettingsUI["⚙️ System Settings\n(/admin/settings)"]
        PresetSelect["Preset Quick-Switch\n(Zoho / Gmail / Log)"]
        TestPing["📡 Ping SMTP Diagnostic"]
    end

    subgraph DBConfig["Database Key-Value Store"]
        SettingsTable[("`settings` table\n(mail_mailer, mail_host,\nmail_port, mail_username,\nmail_password, mail_encryption)")]
    end

    subgraph ServiceEngine["Mail Execution Engine"]
        BootHook["AppServiceProvider::boot()\nConfig::set('mail.mailers.smtp')"]
        EmailStudio["✉️ Mail Studio & Simulator\n(/admin/mail-studio)"]
        FallbackArchive["📥 In-App Mailbox Archive\n(Local simulation & fallback)"]
    end

    subgraph ExternalDispatches["External Transmission"]
        ZohoServer["Zoho SMTP\n(smtppro.zoho.com:465 SSL)"]
        GoogleServer["Google Workspace\n(smtp.gmail.com:587 TLS)"]
    end

    SettingsUI -->|Save Host & Masked Keys| DBConfig
    PresetSelect -->|1-Click Configuration| DBConfig
    TestPing --> ServiceEngine
    DBConfig --> BootHook
    BootHook --> ServiceEngine
    EmailStudio -->|Simulate Inbound/Outbound| FallbackArchive
    ServiceEngine -->|Direct Dispatch| ZohoServer
    ServiceEngine -->|Direct Dispatch| GoogleServer
    ServiceEngine -.->|On Socket Timeout / Block| FallbackArchive
```

---

## 7. Containerization & Deployment Topology on Render

The system is encapsulated in a production-hardened multi-stage Docker container deployed to Render alongside a managed PostgreSQL 16 cluster and persistent NVMe volume:

```mermaid
flowchart TB
    subgraph GitHub["GitHub Repository & CI/CD"]
        GitPush["git push origin main"]
        ActionsCI["GitHub Actions Workflow\n(PHP 8.4, Node 22, PHPUnit 41 Tests, npm ci --legacy-peer-deps)"]
        DeployHook["Render Deploy Webhook\n(curl -f -X POST)"]
    end

    subgraph RenderPlatform["Render Managed Cloud Platform"]
        subgraph WebContainer["Docker Web Service (php:8.4-fpm-alpine)"]
            NginxServer["Nginx 1.26\n(Port 80 / 443 SSL)"]
            SupervisordDaemon["Supervisord Process Daemon"]
            PHPFPM["PHP-FPM 8.4 Worker Pool"]
            LaravelRuntime["Laravel 11 App Core\n(Optimized Config & Route Cache)"]
        end

        subgraph StorageCluster["Storage & Persistence Infrastructure"]
            ManagedPostgres[("Managed PostgreSQL 16 DB\n(Automated Backups & SSL Connection)")]
            NVMeVolume[("10GB Persistent NVMe Disk\n(/var/www/html/storage/app/public)")]
        end
    end

    GitPush --> ActionsCI
    ActionsCI -->|On Success| DeployHook
    DeployHook --> RenderPlatform
    RenderPlatform --> WebContainer
    NginxServer --> PHPFPM
    SupervisordDaemon --> NginxServer
    SupervisordDaemon --> PHPFPM
    PHPFPM --> LaravelRuntime
    LaravelRuntime --> ManagedPostgres
    LaravelRuntime --> NVMeVolume
```

---

## 8. Directory & Codebase Architecture

```
digital-library-management-system-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Administrative Controllers (11 desks)
│   │   │   │   ├── BroadcastController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DownloadApprovalController.php
│   │   │   │   ├── MailTestController.php
│   │   │   │   ├── ModerationController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── ResourceController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── UserImportController.php
│   │   │   ├── Auth/               # Authentication & Onboarding
│   │   │   ├── Student/            # Study Hub & Material Contribution
│   │   │   ├── BookmarkController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   ├── MaterialRequestController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ResourceController.php
│   │   │   └── ReviewController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php # RBAC: Staff, Admin, SuperAdmin guard
│   │       └── EnsureActive.php    # Session kill-switch for deactivated users
│   ├── Mail/                       # Institutional Mailable Classes
│   │   ├── AdminBroadcastMail.php
│   │   ├── SecurityAlertMail.php
│   │   └── WelcomeActivationMail.php
│   ├── Models/                     # Eloquent Entities (11 Models)
│   │   ├── ActivityLog.php
│   │   ├── Bookmark.php
│   │   ├── Category.php
│   │   ├── DownloadRequest.php
│   │   ├── EmailLog.php
│   │   ├── MaterialRequest.php
│   │   ├── Notification.php
│   │   ├── Resource.php
│   │   ├── Review.php
│   │   ├── Setting.php
│   │   └── User.php
│   └── Services/                   # Domain Logic Services
│       └── RecommendationService.php
├── database/
│   ├── migrations/                 # 14 Schema Migrations
│   └── seeders/                    # Realistic Institutional Seed Data
├── deploy/
│   ├── docker/                     # Nginx, Supervisor & PHP configs
│   └── Dockerfile                  # Multi-stage production container
├── docs/                           # Exhaustive Architectural Documentation
├── resources/
│   ├── css/app.css                 # Tailwind CSS v4 Theme & Tokens
│   ├── js/app.js                   # Alpine.js v3 Initialization
│   └── views/                      # Modern Blade Templates (5 Layouts + Subsystems)
├── routes/
│   ├── web.php                     # Complete Route Definitions & Middleware Gates
│   └── console.php
└── tests/                          # 41 Automated Feature & Unit Test Suites
```

---

&copy; University of Education, Winneba &mdash; School of Business. All rights reserved.
