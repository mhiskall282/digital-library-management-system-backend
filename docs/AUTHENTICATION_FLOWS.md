# Authentication & Authorization Flows: UEW Digital Library

## 1. Authentication Overview

The system uses standard Laravel Session-based State-Cookie Authentication coupled with cryptographically secure CSRF protection, eliminating local storage token exposure.

### Primary Capabilities:
- **Dual Login Identifier**: Students can sign in using their **Student Index Number** (e.g., `5201040001`) or their institutional **Email Address** (`student@st.uew.edu.gh`).
- **Account Status Guard**: [`EnsureActive`](file:///c:/Users/user/Desktop/digital-library-management-system-backend/app/Http/Middleware/EnsureActive.php) immediately intercepts and revokes sessions if an administrator deactivates an account.
- **Smart Post-Login Redirection**:
  - Administrators & Superadmins &rarr; [`/admin`](file:///c:/Users/user/Desktop/digital-library-management-system-backend/routes/web.php) (Command Center).
  - Students &rarr; [`/student/hub`](file:///c:/Users/user/Desktop/digital-library-management-system-backend/routes/web.php) (Personalized Student Hub).

---

## 2. Authentication Sequence Flow

```mermaid
sequenceDiagram
    actor Student
    participant Browser
    participant LoginController
    participant AuthGuard
    participant ActivityLog
    participant Database

    Student->>Browser: Enters Index No/Email & Password
    Browser->>LoginController: POST /login (with _token)
    LoginController->>LoginController: Detect format (email vs student_id)
    LoginController->>Database: Query user record by identifier
    LoginController->>AuthGuard: Auth::attempt() with Bcrypt verify
    alt Credentials Invalid
        AuthGuard-->>Browser: Redirect back with error message
    else Account Inactive (is_active = false)
        LoginController->>AuthGuard: Auth::logout()
        LoginController-->>Browser: Redirect with deactivation notice
    else Authentication Succeeded
        AuthGuard->>Browser: Session regenerated & secure cookie set
        LoginController->>ActivityLog: Record 'LOGIN' event with IP
        alt User is Admin
            LoginController-->>Browser: Redirect to /admin (Command Center)
        else User is Student
            LoginController-->>Browser: Redirect to /student/hub (Student Hub)
        end
    end
```

---

## 3. Password Recovery Flow
- Accessible at `/forgot-password`.
- Uses signed tokens generated via Laravel's password broker and stored in `password_reset_tokens`.
- Delivers password reset notifications using the configured mail driver (Mailpit in development, SMTP in production).
