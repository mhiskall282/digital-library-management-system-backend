# Frontend Integration Contract

This document provides the exact contract, data structures, and client workflows for building the frontend.

---

## 1. Authentication State Contract

### Storage
- Token: Store in `localStorage` or `HttpOnly` cookie as `token` or `uew_token`.
- User Profile: Store user object in React Context, Zustand, or Redux.

### Request Headers
Every authenticated request must send:
```http
Authorization: Bearer <token>
```

### Auth Response Shape (`/api/auth/login` and `/api/auth/register`)
```json
{
  "_id": "65b871c...",
  "studentId": "10023456",
  "email": "student@uew.edu.gh",
  "firstName": "Kwame",
  "lastName": "Mensah",
  "level": "L200",
  "program": "BSc Accounting",
  "role": "student",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

## 2. File Upload Contract (FormData)

When uploading files, the client must submit `multipart/form-data`.

### Single Resource Upload: `POST /api/resources/upload`
```ts
const formData = new FormData();
formData.append('title', 'Introduction to Business Management Lecture 1');
formData.append('description', 'Week 1 slide deck covering principles and theories');
formData.append('type', 'SLIDE'); // 'SLIDE' | 'PAST_QUESTION'
formData.append('category', categoryId); // MongoDB ObjectId string
formData.append('level', 'L100'); // 'L100' | 'L200' | 'L300' | 'L400' | 'MASTERS' | 'PHD'
formData.append('academicYear', '2024/2025');
formData.append('file', fileObject); // File instance from <input type="file" />
```

### Multiple Resource Upload: `POST /api/resources/upload-multiple`
```ts
const formData = new FormData();
formData.append('category', categoryId);
formData.append('level', 'L200');
formData.append('type', 'PAST_QUESTION');
formData.append('academicYear', '2023/2024');

// Append each file under 'files'
Array.from(fileList).forEach(file => {
  formData.append('files', file);
});
```

---

## 3. Downloading Resources

The endpoint `/api/resources/:id/download` requires authentication and sends a file attachment.

### Client-Side Download Handler (Axios / Fetch Blob):
```ts
async function downloadResource(resourceId: string, fileName: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(`/api/resources/${resourceId}/download`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  
  if (!response.ok) throw new Error('Download failed');
  
  const blob = await response.blob();
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = fileName;
  document.body.appendChild(a);
  a.click();
  a.remove();
  window.URL.revokeObjectURL(url);
}
```

---

## 4. Search & Pagination Payload Contract

Endpoint: `GET /api/resources/search/advanced`
Query Parameters:
- `query`: Keyword text for title, description, or tags
- `type`: `SLIDE` or `PAST_QUESTION`
- `level`: `L100` | `L200` | `L300` | `L400` | `MASTERS` | `PHD`
- `category`: Category ObjectId
- `minRating`: Number (1 to 5)
- `academicYear`: String (e.g., "2024/2025")
- `sortBy`: `createdAt` | `downloads` | `averageRating` | `title`
- `order`: `asc` | `desc`
- `page`: Integer (1-indexed)
- `limit`: Integer (default: 20)

### Response Shape:
```json
{
  "page": 1,
  "limit": 20,
  "total": 54,
  "pages": 3,
  "resources": [
    {
      "_id": "65ba...",
      "title": "Macroeconomics Past Questions 2023",
      "type": "PAST_QUESTION",
      "level": "L200",
      "category": {
        "_id": "65b9...",
        "name": "Macroeconomics",
        "courseCode": "ECN212",
        "courseName": "Macroeconomics II"
      },
      "uploadedBy": {
        "_id": "65b8...",
        "firstName": "Library",
        "lastName": "Admin"
      },
      "downloads": 142,
      "averageRating": 4.8,
      "totalReviews": 12,
      "academicYear": "2023/2024",
      "fileSize": 209986,
      "createdAt": "2026-01-29T12:00:00.000Z"
    }
  ]
}
```

---

## 5. Notification Polling Contract

- **Badge Count**: `GET /api/notifications/unread-count` returns `{ "unreadCount": 3 }`. Fetch on app mount or interval (every 60s).
- **List Notifications**: `GET /api/notifications?limit=10`.
- **Mark As Read**: `PUT /api/notifications/:id/read`.
- **Mark All Read**: `PUT /api/notifications/mark-all-read`.
