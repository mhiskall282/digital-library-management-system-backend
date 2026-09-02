# API Endpoint Directory

Base URL: `http://localhost:5000/api`

---

## 1. Authentication (`/api/auth`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `POST` | `/api/auth/register` | Public | `{ studentId, email, password, firstName, lastName, level, program }` | Registers student, issues JWT, and sends verification email. |
| `POST` | `/api/auth/login` | Public | `{ email, password }` | Authenticates credentials and returns JWT token. |
| `GET` | `/api/auth/verify-email/:token` | Public | None | Verifies account email token. |
| `POST` | `/api/auth/forgot-password` | Public | `{ email }` | Generates reset token and emails recovery link. |
| `POST` | `/api/auth/reset-password/:token` | Public | `{ password }` | Resets password with valid, unused token. |
| `GET` | `/api/auth/me` | Private | None | Returns profile of currently authenticated user. |
| `PUT` | `/api/auth/profile` | Private | `{ firstName, lastName, email, program, password? }` | Updates profile details and regenerates JWT token. |
| `POST` | `/api/auth/resend-verification` | Private | None | Sends a fresh verification email. |
| `PUT` | `/api/auth/preferences` | Private | `{ emailNotifications?, newResourceAlerts? }` | Toggles notification preferences. |

---

## 2. Resources & Categories (`/api/resources`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `POST` | `/api/resources/categories` | Admin | `{ name, level, courseCode, courseName, semester }` | Creates a new curriculum category. |
| `GET` | `/api/resources/categories` | Private | Query: `?level=L100` | Lists all categories (filterable by level). |
| `GET` | `/api/resources/search/advanced` | Private | Query: `?query=...&type=...&level=...&category=...&minRating=...&academicYear=...&sortBy=...&order=...&page=1&limit=20` | Full-featured pagination and search. |
| `POST` | `/api/resources/upload` | Admin | `multipart/form-data`: `file`, `title`, `description`, `type`, `category`, `level`, `academicYear` | Uploads single resource and notifies level students. |
| `POST` | `/api/resources/upload-multiple` | Admin | `multipart/form-data`: `files` (up to 10), `category`, `level`, `type`, `academicYear` | Batch resource upload. |
| `GET` | `/api/resources` | Private | Query: `?type=...&level=...&category=...&search=...` | Lists resources with optional basic filters. |
| `GET` | `/api/resources/:id` | Private | None | Fetches single resource metadata and author. |
| `GET` | `/api/resources/:id/download` | Private | None | Increments download count and streams file attachment. |
| `DELETE` | `/api/resources/:id` | Admin | None | Removes resource from DB and deletes file on disk. |

---

## 3. Reviews & Ratings (`/api/reviews`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `POST` | `/api/reviews` | Private | `{ resource, rating, comment? }` | Adds a rating (1-5) and comment; recalculates average rating. |
| `GET` | `/api/reviews/resource/:resourceId` | Private | None | Returns all reviews for a specific resource. |
| `GET` | `/api/reviews/my-reviews` | Private | None | Returns all reviews written by current user. |
| `PUT` | `/api/reviews/:id` | Private | `{ rating?, comment? }` | Updates owner's review; recalculates average rating. |
| `DELETE` | `/api/reviews/:id` | Private | None | Deletes review (owner or admin); recalculates rating. |
| `PUT` | `/api/reviews/:id/helpful` | Private | None | Increments helpfulness score. |

---

## 4. Bookmarks (`/api/bookmarks`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `POST` | `/api/bookmarks` | Private | `{ resource, notes? }` | Bookmarks a resource with optional private notes. |
| `GET` | `/api/bookmarks` | Private | Query: `?type=...&level=...` | Returns user's bookmarks with populated resource data. |
| `GET` | `/api/bookmarks/check/:resourceId` | Private | None | Quick lookup to check if current user bookmarked resource. |
| `PUT` | `/api/bookmarks/:id` | Private | `{ notes }` | Updates study notes on an existing bookmark. |
| `DELETE` | `/api/bookmarks/:id` | Private | None | Deletes bookmark. |

---

## 5. Notifications (`/api/notifications`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `GET` | `/api/notifications` | Private | Query: `?isRead=true/false&limit=20` | Returns notifications and unread badge count. |
| `GET` | `/api/notifications/unread-count` | Private | None | Returns `{ unreadCount: n }`. |
| `PUT` | `/api/notifications/:id/read` | Private | None | Marks single notification as read. |
| `PUT` | `/api/notifications/mark-all-read` | Private | None | Marks all user notifications as read. |
| `DELETE` | `/api/notifications/:id` | Private | None | Deletes single notification. |
| `DELETE` | `/api/notifications/clear-read` | Private | None | Clears all read notifications. |

---

## 6. Admin Analytics (`/api/analytics`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `GET` | `/api/analytics/dashboard` | Admin | None | Aggregate counts (users, resources, downloads, breakdown by level & type). |
| `GET` | `/api/analytics/resources/:id` | Admin | None | Performance stats for single resource. |
| `GET` | `/api/analytics/users/:id` | Admin | None | User activity stats and upload metrics. |

---

## 7. User Administration (`/api/users`)

| Method | Endpoint | Access | Body / Query | Description |
|---|---|---|---|---|
| `GET` | `/api/users` | Admin | None | Lists all registered users (excluding password). |
| `GET` | `/api/users/:id` | Admin | None | Fetches single user profile. |
| `PUT` | `/api/users/:id` | Admin | `{ firstName, lastName, email, level, program, role, isActive }` | Updates user details, role, or active status. |
| `DELETE` | `/api/users/:id` | Admin | None | Deletes user account. |
