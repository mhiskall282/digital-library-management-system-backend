# UEW Digital Library Backend API

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![Node](https://img.shields.io/badge/node-%3E%3D18.0.0-green.svg)
![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)

A comprehensive RESTful API for managing digital library resources at the University of Education, Winneba (UEW) School of Business. This backend system provides authentication, resource management, reviews, bookmarks, notifications, and analytics features.

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation & Setup](#installation--setup)
- [Environment Variables](#environment-variables)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Testing Guide](#testing-guide)
- [Deployment](#deployment)
- [Common Issues](#common-issues)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### 🔐 Authentication & Authorization
- User registration with email verification
- JWT-based authentication
- Password reset functionality
- Role-based access control (Student/Admin)
- Session management

### 📚 Resource Management
- Upload slides and past questions (PDF, DOC, DOCX, PPT, PPTX)
- Single and multiple file uploads
- Categorize by course, level, and semester
- Download tracking
- Advanced search with filters

### ⭐ Reviews & Ratings
- Rate resources (1-5 stars)
- Write and edit reviews
- Mark reviews as helpful
- Automatic average rating calculation

### 🔖 Bookmarks
- Save favorite resources
- Add personal notes to bookmarks
- Filter bookmarks by type and level

### 🔔 Notifications
- Real-time notifications for new resources
- Email notifications (configurable)
- Mark as read/unread
- Clear read notifications

### 📊 Admin Analytics
- Dashboard statistics
- User activity tracking
- Resource performance metrics
- Download and rating analytics
- Upload trends

### 🔍 Advanced Search
- Full-text search
- Filter by type, level, category, rating
- Pagination support
- Sort by multiple criteria

---

## 🛠 Tech Stack

- **Runtime:** Node.js (v18+)
- **Framework:** Express.js
- **Database:** MongoDB with Mongoose ODM
- **Authentication:** JWT (JSON Web Tokens)
- **File Upload:** Multer
- **Email:** Nodemailer
- **Validation:** Express-validator, Joi
- **Security:** Bcrypt.js, Rate limiting, CORS

---

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **Node.js** (v18.0.0 or higher) - [Download](https://nodejs.org/)
- **npm** (comes with Node.js) or **yarn**
- **MongoDB Atlas Account** (free) - [Sign Up](https://www.mongodb.com/cloud/atlas/register)
- **Git** - [Download](https://git-scm.com/)
- **Postman** (for testing) - [Download](https://www.postman.com/downloads/)

---

## 🚀 Installation & Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/uew-digital-library-backend.git
cd uew-digital-library-backend
```

### Step 2: Install Dependencies

```bash
npm install
```

This will install all required packages:
- express
- mongoose
- bcryptjs
- jsonwebtoken
- dotenv
- cors
- multer
- nodemailer
- joi
- express-validator
- express-rate-limit
- nodemon (dev dependency)

### Step 3: Set Up MongoDB Atlas

1. **Create Account:**
   - Go to [MongoDB Atlas](https://www.mongodb.com/cloud/atlas/register)
   - Sign up for a free account

2. **Create Cluster:**
   - Click "Build a Database"
   - Choose **M0 FREE** tier
   - Select a cloud provider and region closest to you
   - Click "Create Deployment"

3. **Create Database User:**
   - Username: `uewadmin` (or your choice)
   - Password: Create a strong password
   - Click "Create Database User"

4. **Configure Network Access:**
   - Click "Network Access" in left sidebar
   - Click "Add IP Address"
   - Click "Allow Access from Anywhere" (0.0.0.0/0)
   - Click "Confirm"

5. **Get Connection String:**
   - Click "Database" in left sidebar
   - Click "Connect" on your cluster
   - Choose "Drivers"
   - Copy the connection string
   - Replace `<password>` with your actual password
   - Add database name: `/uew-library` before the `?`

   Example:
   ```
   mongodb+srv://uewadmin:YourPassword@cluster0.xxxxx.mongodb.net/uew-library?retryWrites=true&w=majority
   ```

### Step 4: Configure Environment Variables

Create a `.env` file in the root directory:

```bash
touch .env
```

Add the following variables (see [Environment Variables](#environment-variables) section for details):

```env
# Server Configuration
PORT=5000
NODE_ENV=development

# Database
MONGO_URI=mongodb+srv://username:password@cluster0.xxxxx.mongodb.net/uew-library?retryWrites=true&w=majority

# JWT Secret (change this to a random string)
JWT_SECRET=your_super_secret_jwt_key_change_this_to_something_random

# Email Configuration (Gmail)
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USER=your_email@gmail.com
EMAIL_PASS=your_gmail_app_password
EMAIL_FROM=UEW Digital Library <noreply@uew.edu.gh>

# Frontend URL (update when you deploy frontend)
FRONTEND_URL=http://localhost:3000

# Rate Limiting
RATE_LIMIT_WINDOW_MS=900000
RATE_LIMIT_MAX_REQUESTS=100
```

### Step 5: Set Up Gmail for Email Features

1. **Enable 2-Step Verification:**
   - Go to [Google Account Security](https://myaccount.google.com/security)
   - Enable "2-Step Verification"

2. **Generate App Password:**
   - Go to [App Passwords](https://myaccount.google.com/apppasswords)
   - Select app: **Mail**
   - Select device: **Other** (name it "UEW Library")
   - Click **Generate**
   - Copy the 16-character password
   - Paste it in your `.env` file as `EMAIL_PASS`

### Step 6: Create Upload Directories

```bash
mkdir -p uploads/slides
mkdir -p uploads/past-questions
```

### Step 7: Start the Server

**Development mode (with auto-restart):**
```bash
npm run dev
```

**Production mode:**
```bash
npm start
```

You should see:
```
╔════════════════════════════════════════════════════════════╗
║                                                            ║
║   UEW Digital Library Backend API                         ║
║   Version: 2.0.0                                          ║
║                                                            ║
║   Server running in development mode                      ║
║   Port: 5000                                              ║
║                                                            ║
║   Features:                                               ║
║   ✓ Email Verification                                    ║
║   ✓ Password Reset                                        ║
║   ✓ Reviews & Ratings                                     ║
║   ✓ Bookmarks                                             ║
║   ✓ Notifications                                         ║
║   ✓ Advanced Search                                       ║
║   ✓ Multiple File Upload                                  ║
║   ✓ Admin Analytics                                       ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝

MongoDB Connected Successfully
```

---

## 🔧 Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `PORT` | Server port number | `5000` |
| `NODE_ENV` | Environment (development/production) | `development` |
| `MONGO_URI` | MongoDB connection string | `mongodb+srv://...` |
| `JWT_SECRET` | Secret key for JWT tokens | `your_random_secret` |
| `EMAIL_HOST` | SMTP host for emails | `smtp.gmail.com` |
| `EMAIL_PORT` | SMTP port | `587` |
| `EMAIL_USER` | Email account username | `your@gmail.com` |
| `EMAIL_PASS` | Email account password (app password) | `abcd efgh ijkl mnop` |
| `EMAIL_FROM` | From address for emails | `UEW Library <noreply@uew.edu.gh>` |
| `FRONTEND_URL` | Frontend application URL | `http://localhost:3000` |
| `RATE_LIMIT_WINDOW_MS` | Rate limit time window (ms) | `900000` (15 min) |
| `RATE_LIMIT_MAX_REQUESTS` | Max requests per window | `100` |

---

## 📁 Project Structure

```
uew-digital-library-backend/
├── config/
│   └── db.js                      # Database connection configuration
├── controllers/
│   ├── authController.js          # Authentication logic
│   ├── resourceController.js      # Resource management logic
│   ├── reviewController.js        # Review management logic
│   ├── bookmarkController.js      # Bookmark management logic
│   ├── notificationController.js  # Notification management logic
│   └── analyticsController.js     # Analytics logic
├── middleware/
│   ├── auth.js                    # Authentication middleware
│   ├── upload.js                  # File upload middleware
│   ├── validation.js              # Input validation middleware
│   └── rateLimiter.js             # Rate limiting middleware
├── models/
│   ├── User.js                    # User schema
│   ├── Resource.js                # Resource schema
│   ├── Category.js                # Category schema
│   ├── Review.js                  # Review schema
│   ├── Bookmark.js                # Bookmark schema
│   ├── Notification.js            # Notification schema
│   └── PasswordReset.js           # Password reset schema
├── routes/
│   ├── auth.js                    # Authentication routes
│   ├── resources.js               # Resource routes
│   ├── users.js                   # User management routes
│   ├── reviews.js                 # Review routes
│   ├── bookmarks.js               # Bookmark routes
│   ├── notifications.js           # Notification routes
│   └── analytics.js               # Analytics routes
├── utils/
│   ├── emailService.js            # Email sending utilities
│   └── notificationService.js     # Notification utilities
├── uploads/
│   ├── slides/                    # Uploaded slide files
│   └── past-questions/            # Uploaded past question files
├── .env                           # Environment variables (DO NOT COMMIT)
├── .gitignore                     # Git ignore file
├── package.json                   # Dependencies and scripts
├── server.js                      # Application entry point
└── README.md                      # This file
```

---

## 📖 API Documentation

### Base URL
- **Local:** `http://localhost:5000`
- **Production:** `https://your-deployed-app.onrender.com`

### Authentication
Most endpoints require a JWT token in the Authorization header:
```
Authorization: Bearer YOUR_JWT_TOKEN
```

---

### 🔐 Authentication Endpoints

#### Register User
```http
POST /api/auth/register
Content-Type: application/json

{
  "studentId": "UEB0012345",
  "email": "student@uew.edu.gh",
  "password": "Student123!",
  "firstName": "John",
  "lastName": "Mensah",
  "level": "L200",
  "program": "Accounting"
}
```

**Response:**
```json
{
  "_id": "...",
  "studentId": "UEB0012345",
  "email": "student@uew.edu.gh",
  "firstName": "John",
  "lastName": "Mensah",
  "level": "L200",
  "program": "Accounting",
  "role": "student",
  "isEmailVerified": false,
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "message": "Registration successful. Please check your email to verify your account."
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "student@uew.edu.gh",
  "password": "Student123!"
}
```

**Response:**
```json
{
  "_id": "...",
  "email": "student@uew.edu.gh",
  "role": "student",
  "token": "eyJhbGciOiJIUzI1NiIs..."
}
```

#### Get Current User Profile
```http
GET /api/auth/me
Authorization: Bearer YOUR_TOKEN
```

#### Verify Email
```http
GET /api/auth/verify-email/:token
```

#### Forgot Password
```http
POST /api/auth/forgot-password
Content-Type: application/json

{
  "email": "student@uew.edu.gh"
}
```

#### Reset Password
```http
POST /api/auth/reset-password/:token
Content-Type: application/json

{
  "password": "NewPassword123!"
}
```

#### Update Preferences
```http
PUT /api/auth/preferences
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "emailNotifications": true,
  "newResourceAlerts": true
}
```

---

### 📚 Resource Endpoints

#### Create Category (Admin Only)
```http
POST /api/resources/categories
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "name": "Financial Accounting",
  "level": "L100",
  "courseCode": "ACC101",
  "courseName": "Introduction to Financial Accounting",
  "semester": "FIRST"
}
```

#### Get All Categories
```http
GET /api/resources/categories
Authorization: Bearer YOUR_TOKEN

# Filter by level
GET /api/resources/categories?level=L100
```

#### Upload Single Resource (Admin Only)
```http
POST /api/resources/upload
Authorization: Bearer ADMIN_TOKEN
Content-Type: multipart/form-data

file: [PDF/DOC/DOCX/PPT/PPTX file]
title: "Week 1 - Introduction to Accounting"
description: "Basic accounting principles"
type: "SLIDE" or "PAST_QUESTION"
category: "category_id_here"
level: "L100"
academicYear: "2024/2025"
```

#### Upload Multiple Resources (Admin Only)
```http
POST /api/resources/upload-multiple
Authorization: Bearer ADMIN_TOKEN
Content-Type: multipart/form-data

files: [Multiple PDF files]
type: "SLIDE"
category: "category_id_here"
level: "L200"
academicYear: "2024/2025"
```

#### Get All Resources
```http
GET /api/resources
Authorization: Bearer YOUR_TOKEN

# With filters
GET /api/resources?type=SLIDE&level=L100&category=category_id
```

#### Get Single Resource
```http
GET /api/resources/:id
Authorization: Bearer YOUR_TOKEN
```

#### Download Resource
```http
GET /api/resources/:id/download
Authorization: Bearer YOUR_TOKEN
```

#### Advanced Search
```http
GET /api/resources/search/advanced
Authorization: Bearer YOUR_TOKEN

# Parameters:
# - query: search term
# - type: SLIDE or PAST_QUESTION
# - level: L100, L200, L300, L400, MASTERS, PHD
# - category: category_id
# - minRating: minimum rating (1-5)
# - academicYear: e.g., "2024/2025"
# - sortBy: createdAt, averageRating, downloads
# - order: asc or desc
# - page: page number
# - limit: results per page

Example:
GET /api/resources/search/advanced?query=accounting&type=SLIDE&level=L100&minRating=4&sortBy=averageRating&order=desc&page=1&limit=10
```

#### Delete Resource (Admin Only)
```http
DELETE /api/resources/:id
Authorization: Bearer ADMIN_TOKEN
```

---

### ⭐ Review Endpoints

#### Create Review
```http
POST /api/reviews
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "resource": "resource_id_here",
  "rating": 5,
  "comment": "Excellent slides! Very helpful."
}
```

#### Get Resource Reviews
```http
GET /api/reviews/resource/:resourceId
Authorization: Bearer YOUR_TOKEN
```

#### Get My Reviews
```http
GET /api/reviews/my-reviews
Authorization: Bearer YOUR_TOKEN
```

#### Update Review
```http
PUT /api/reviews/:id
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "rating": 4,
  "comment": "Updated review text"
}
```

#### Delete Review
```http
DELETE /api/reviews/:id
Authorization: Bearer YOUR_TOKEN
```

#### Mark Review as Helpful
```http
PUT /api/reviews/:id/helpful
Authorization: Bearer YOUR_TOKEN
```

---

### 🔖 Bookmark Endpoints

#### Add Bookmark
```http
POST /api/bookmarks
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "resource": "resource_id_here",
  "notes": "Important for exam"
}
```

#### Get My Bookmarks
```http
GET /api/bookmarks
Authorization: Bearer YOUR_TOKEN

# With filters
GET /api/bookmarks?type=SLIDE&level=L200
```

#### Check if Resource is Bookmarked
```http
GET /api/bookmarks/check/:resourceId
Authorization: Bearer YOUR_TOKEN
```

#### Update Bookmark
```http
PUT /api/bookmarks/:id
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "notes": "Updated notes"
}
```

#### Delete Bookmark
```http
DELETE /api/bookmarks/:id
Authorization: Bearer YOUR_TOKEN
```

---

### 🔔 Notification Endpoints

#### Get My Notifications
```http
GET /api/notifications
Authorization: Bearer YOUR_TOKEN

# With filters
GET /api/notifications?isRead=false&limit=20
```

#### Get Unread Count
```http
GET /api/notifications/unread-count
Authorization: Bearer YOUR_TOKEN
```

#### Mark Notification as Read
```http
PUT /api/notifications/:id/read
Authorization: Bearer YOUR_TOKEN
```

#### Mark All as Read
```http
PUT /api/notifications/mark-all-read
Authorization: Bearer YOUR_TOKEN
```

#### Delete Notification
```http
DELETE /api/notifications/:id
Authorization: Bearer YOUR_TOKEN
```

#### Clear Read Notifications
```http
DELETE /api/notifications/clear-read
Authorization: Bearer YOUR_TOKEN
```

---

### 📊 Analytics Endpoints (Admin Only)

#### Get Dashboard Statistics
```http
GET /api/analytics/dashboard
Authorization: Bearer ADMIN_TOKEN
```

**Response:**
```json
{
  "overview": {
    "totalUsers": 150,
    "totalResources": 245,
    "totalReviews": 89,
    "totalBookmarks": 312,
    "newUsers": 12,
    "recentUploads": 8,
    "activityScore": 45
  },
  "distributions": {
    "usersByLevel": [...],
    "resourcesByType": [...],
    "resourcesByLevel": [...]
  },
  "topContent": {
    "mostDownloaded": [...],
    "topRated": [...]
  },
  "trends": {
    "uploadTrends": [...]
  }
}
```

#### Get Resource Statistics
```http
GET /api/analytics/resources/:id
Authorization: Bearer ADMIN_TOKEN
```

#### Get User Statistics
```http
GET /api/analytics/users/:id
Authorization: Bearer ADMIN_TOKEN
```

---

### 👥 User Management Endpoints (Admin Only)

#### Get All Users
```http
GET /api/users
Authorization: Bearer ADMIN_TOKEN
```

#### Get User by ID
```http
GET /api/users/:id
Authorization: Bearer ADMIN_TOKEN
```

#### Update User
```http
PUT /api/users/:id
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "role": "admin",
  "isActive": true,
  "level": "L300"
}
```

#### Delete User
```http
DELETE /api/users/:id
Authorization: Bearer ADMIN_TOKEN
```

---

## 🧪 Testing Guide

### Setting Up Postman

1. **Import Environment:**
   - Create new environment: "UEW Library - Local"
   - Add variables:
     - `baseUrl`: `http://localhost:5000`
     - `adminToken`: (will be filled after login)
     - `studentToken`: (will be filled after login)

2. **Create Collection:**
   - Name: "UEW Digital Library API"
   - Add all endpoints from API documentation

### Test Sequence

#### Step 1: Create Admin User

```http
POST {{baseUrl}}/api/auth/register
{
  "studentId": "ADMIN001",
  "email": "admin@uew.edu.gh",
  "password": "Admin123!",
  "firstName": "System",
  "lastName": "Administrator",
  "level": "PHD",
  "program": "Business Administration"
}
```

**Then manually update role to "admin" in MongoDB Atlas:**
1. Go to MongoDB Atlas → Browse Collections
2. Find `users` collection
3. Edit the admin user
4. Change `role` from `"student"` to `"admin"`

#### Step 2: Login as Admin

```http
POST {{baseUrl}}/api/auth/login
{
  "email": "admin@uew.edu.gh",
  "password": "Admin123!"
}
```

**Save the token in Postman environment as `adminToken`**

#### Step 3: Create Categories

```http
POST {{baseUrl}}/api/resources/categories
Authorization: Bearer {{adminToken}}

{
  "name": "Financial Accounting",
  "level": "L100",
  "courseCode": "ACC101",
  "courseName": "Introduction to Financial Accounting",
  "semester": "FIRST"
}
```

Create 2-3 different categories for testing.

#### Step 4: Upload Resources

```http
POST {{baseUrl}}/api/resources/upload
Authorization: Bearer {{adminToken}}
Content-Type: multipart/form-data

file: [Select a PDF file]
title: "Week 1 - Accounting Basics"
description: "Introduction to accounting principles"
type: "SLIDE"
category: [paste category ID from step 3]
level: "L100"
academicYear: "2024/2025"
```

#### Step 5: Register Student

```http
POST {{baseUrl}}/api/auth/register
{
  "studentId": "UEB0012345",
  "email": "student@uew.edu.gh",
  "password": "Student123!",
  "firstName": "John",
  "lastName": "Mensah",
  "level": "L200",
  "program": "Accounting"
}
```

**Save the token as `studentToken`**

#### Step 6: Test Student Features

**Create Review:**
```http
POST {{baseUrl}}/api/reviews
Authorization: Bearer {{studentToken}}

{
  "resource": "[resource_id from step 4]",
  "rating": 5,
  "comment": "Excellent material!"
}
```

**Add Bookmark:**
```http
POST {{baseUrl}}/api/bookmarks
Authorization: Bearer {{studentToken}}

{
  "resource": "[resource_id]",
  "notes": "Important for exam"
}
```

**Get Resources:**
```http
GET {{baseUrl}}/api/resources
Authorization: Bearer {{studentToken}}
```

**Advanced Search:**
```http
GET {{baseUrl}}/api/resources/search/advanced?query=accounting&type=SLIDE&level=L100
Authorization: Bearer {{studentToken}}
```

#### Step 7: Test Notifications

Upload a new resource as admin (step 4) - this will create notifications for students of that level.

**Get Notifications:**
```http
GET {{baseUrl}}/api/notifications
Authorization: Bearer {{studentToken}}
```

#### Step 8: Test Admin Analytics

```http
GET {{baseUrl}}/api/analytics/dashboard
Authorization: Bearer {{adminToken}}
```

### Testing Checklist

- [ ] User registration works
- [ ] User login returns valid token
- [ ] Email verification token is generated
- [ ] Password reset works
- [ ] Admin can create categories
- [ ] Admin can upload single file
- [ ] Admin can upload multiple files
- [ ] Students can view resources
- [ ] Students can download resources
- [ ] Students can create reviews
- [ ] Students can bookmark resources
- [ ] Notifications are created on upload
- [ ] Advanced search returns correct results
- [ ] Analytics dashboard shows data
- [ ] Rate limiting works (try 6+ login attempts)
- [ ] Invalid tokens are rejected

---

## 🚀 Deployment

### Deploy to Render (Free)

#### Step 1: Prepare for Deployment

1. **Ensure .gitignore is correct:**
```
node_modules/
.env
uploads/
*.log
.DS_Store
```

2. **Verify package.json has start script:**
```json
{
  "scripts": {
    "start": "node server.js",
    "dev": "nodemon server.js"
  },
  "engines": {
    "node": ">=18.0.0"
  }
}
```

#### Step 2: Push to GitHub

```bash
# Initialize git (if not done)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - UEW Digital Library Backend"

# Create GitHub repo and push
git remote add origin https://github.com/YOUR_USERNAME/uew-digital-library-backend.git
git branch -M main
git push -u origin main
```

#### Step 3: Deploy to Render

1. **Create Render Account:**
   - Go to [Render.com](https://render.com)
   - Sign up with GitHub

2. **Create New Web Service:**
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select `uew-digital-library-backend`

3. **Configure Service:**
   - **Name:** `uew-library-backend`
   - **Region:** Choose closest region
   - **Branch:** `main`
   - **Runtime:** `Node`
   - **Build Command:** `npm install`
   - **Start Command:** `npm start`
   - **Instance Type:** **Free**

4. **Add Environment Variables:**

   Click "Advanced" → "Add Environment Variable"

   Add all variables from your `.env` file:
   - `NODE_ENV` = `production`
   - `PORT` = `5000`
   - `MONGO_URI` = `your_mongodb_atlas_url`
   - `JWT_SECRET` = `your_secret`
   - `EMAIL_HOST` = `smtp.gmail.com`
   - `EMAIL_PORT` = `587`
   - `EMAIL_USER` = `your_email@gmail.com`
   - `EMAIL_PASS` = `your_app_password`
   - `EMAIL_FROM` = `UEW Digital Library <noreply@uew.edu.gh>`
   - `FRONTEND_URL` = `https://your-frontend-url.com`
   - `RATE_LIMIT_WINDOW_MS` = `900000`
   - `RATE_LIMIT_MAX_REQUESTS` = `100`

5. **Click "Create Web Service"**

   Wait 2-5 minutes for deployment.

6. **Get Your URL:**

   After deployment, you'll get a URL like:
   ```
   https://uew-library-backend.onrender.com
   ```

#### Step 4: Test Deployed API

Update Postman base URL to your Render URL:
```
https://uew-library-backend.onrender.com
```

Test the welcome endpoint:
```http
GET https://uew-library-backend.onrender.com/
```

Should return:
```json
{
  "message": "Welcome to UEW School of Business Digital Library API",
  "version": "2.0.0",
  ...
}
```

#### Step 5: Update MongoDB Network Access

1. Go to MongoDB Atlas
2. Click "Network Access"
3. Click "Add IP Address"
4. Select "Allow Access from Anywhere" (0.0.0.0/0)
5. Click "Confirm"

#### Step 6: Auto-Deployment

Render automatically redeploys when you push to GitHub:

```bash
# Make changes
git add .
git commit -m "Updated feature"
git push origin main
```

Render will detect the push and redeploy automatically!

### Deploy to Railway (Alternative)

1. Go to [Railway.app](https://railway.app)
2. Sign up with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your repository
5. Add environment variables
6. Deploy!

### Deploy to Heroku (Alternative)

1. Create Heroku account
2. Install Heroku CLI
3. Run:
```bash
heroku login
heroku create uew-library-backend
git push heroku main
heroku config:set NODE_ENV=production
# Add other environment variables
```

---

## 🐛 Common Issues & Solutions

### Issue 1: MongoDB Connection Failed

**Error:** `MongoDB Connection Error: connect ECONNREFUSED`

**Solution:**
- Verify MongoDB Atlas connection string in `.env`
- Check network access settings in MongoDB Atlas
- Ensure IP whitelist includes your IP or 0.0.0.0/0
- Verify username and password are correct

### Issue 2: "User not found" Error

**Error:** `401 Unauthorized - User not found`

**Solution:**
- Login again to get a fresh token
- Verify Authorization header format: `Bearer YOUR_TOKEN`
- Check if user exists in database
- Verify token hasn't expired (30 days by default)

### Issue 3: File Upload Fails

**Error:** `MulterError: File too large`

**Solution:**
- File must be under 10MB
- Only PDF, DOC, DOCX, PPT, PPTX allowed
- Ensure `uploads/slides/` and `uploads/past-questions/` directories exist

### Issue 4: Email Not Sending

**Error:** Email verification/password reset emails not received

**Solution:**
- Verify Gmail app password is correct (not regular password)
- Check 2-Step Verification is enabled
- Verify `EMAIL_USER` and `EMAIL_PASS` in `.env`
- Check server logs for email errors
- Test with a different email address

### Issue 5: Rate Limit Exceeded

**Error:** `429 Too Many Requests`

**Solution:**
- Wait 15 minutes for rate limit to reset
- Reduce request frequency
- Adjust `RATE_LIMIT_MAX_REQUESTS` if needed (development only)

### Issue 6: JWT Token Expired

**Error:** `401 Unauthorized - Token expired`

**Solution:**
- Login again to get new token
- Tokens expire after 30 days
- Save new token in Postman environment

### Issue 7: Deployment Failed on Render

**Error:** Build fails or app crashes

**Solution:**
- Check Render logs for specific error
- Verify all environment variables are set
- Ensure Node version is compatible (18+)
- Check `package.json` has correct start script
- Verify MongoDB Atlas allows connections from 0.0.0.0/0

### Issue 8: CORS Error

**Error:** `Access-Control-Allow-Origin` error

**Solution:**
- CORS is enabled for all origins by default
- If using specific frontend URL, update CORS settings in `server.js`
- Verify request includes proper headers

---

## 📝 API Response Format

### Success Response
```json
{
  "_id": "...",
  "field1": "value1",
  "field2": "value2",
  ...
}
```

### Error Response
```json
{
  "message": "Error description",
  "error": "Detailed error (development only)"
}
```

### Validation Error Response
```json
{
  "message": "Validation failed",
  "errors": [
    {
      "msg": "Email is required",
      "param": "email",
      "location": "body"
    }
  ]
}
```

---

## 🔒 Security Features

- **Password Hashing:** Bcrypt with salt rounds
- **JWT Authentication:** Secure token-based auth
- **Rate Limiting:** Prevents brute force attacks
- **Input Validation:** Express-validator for all inputs
- **CORS Protection:** Configurable CORS policy
- **File Type Validation:** Only allowed file types
- **File Size Limits:** Maximum 10MB per file
- **Email Verification:** Prevents fake accounts
- **Role-Based Access:** Admin/Student permissions

---

## 📊 Database Schema

### User Schema
```javascript
{
  studentId: String (unique, required),
  email: String (unique, required),
  password: String (hashed, required),
  firstName: String (required),
  lastName: String (required),
  level: String (enum: L100-PHD, required),
  program: String (required),
  role: String (enum: student/admin, default: student),
  isActive: Boolean (default: true),
  isEmailVerified: Boolean (default: false),
  emailVerificationToken: String,
  emailVerificationExpires: Date,
  preferences: {
    emailNotifications: Boolean,
    newResourceAlerts: Boolean
  },
  createdAt: Date,
  updatedAt: Date
}
```

### Resource Schema
```javascript
{
  title: String (required),
  description: String,
  type: String (enum: SLIDE/PAST_QUESTION, required),
  category: ObjectId (ref: Category, required),
  level: String (enum: L100-PHD, required),
  fileName: String (required),
  filePath: String (required),
  fileSize: Number (required),
  uploadedBy: ObjectId (ref: User, required),
  downloads: Number (default: 0),
  academicYear: String,
  averageRating: Number (0-5, default: 0),
  totalReviews: Number (default: 0),
  tags: [String],
  createdAt: Date,
  updatedAt: Date
}
```

### Review Schema
```javascript
{
  resource: ObjectId (ref: Resource, required),
  user: ObjectId (ref: User, required),
  rating: Number (1-5, required),
  comment: String (max: 500),
  helpful: Number (default: 0),
  createdAt: Date,
  updatedAt: Date
}
```

### Category Schema
```javascript
{
  name: String (required),
  level: String (enum: L100-PHD, required),
  courseCode: String (required),
  courseName: String (required),
  semester: String (enum: FIRST/SECOND, required),
  createdAt: Date,
  updatedAt: Date
}
```

---

## 🚦 Rate Limits

| Endpoint Type | Window | Max Requests |
|--------------|--------|--------------|
| General API | 15 min | 100 requests |
| Auth (Login) | 15 min | 5 requests |
| Email | 1 hour | 3 requests |

---

## 📈 Performance Tips

1. **Use Pagination:** Always use `page` and `limit` parameters for large datasets
2. **Filter Results:** Use query parameters to reduce response size
3. **Cache Tokens:** Store JWT tokens securely on client side
4. **Optimize Images:** Compress PDFs before upload
5. **Index Database:** MongoDB indexes are set on frequently queried fields

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/AmazingFeature`
3. Commit changes: `git commit -m 'Add AmazingFeature'`
4. Push to branch: `git push origin feature/AmazingFeature`
5. Open a Pull Request

### Code Style
- Use ES6+ syntax
- Follow existing code structure
- Add comments for complex logic
- Update README if adding new features

---

## 📜 License

This project is licensed under the MIT License.

---

## 👥 Authors

- **Your Name** - Initial work - [@mhiskall282](https://github.com/mhiskall282)

---

## 🙏 Acknowledgments

- University of Education, Winneba - School of Business
- MongoDB Atlas for database hosting
- Render.com for free hosting
- All contributors and testers

---

## 📞 Support

For issues, questions, or contributions:

- **Email:** 0xmhiskall@gmail.com
- **GitHub Issues:** [Create an issue](https://github.com/mhiskall282/digital-library-management-system-backend/issues)
- **Documentation:** This README

---

## 🗺️ Roadmap

### Future Features
- [ ] Real-time chat support
- [ ] Mobile app API support
- [ ] Resource version control
- [ ] Collaborative annotations
- [ ] AI-powered search
- [ ] Integration with learning management systems
- [ ] Video content support
- [ ] Offline mode support
- [ ] Multi-language support
- [ ] Advanced analytics dashboard

---

## 📅 Version History

### v2.0.0 (Current)
- ✅ Email verification
- ✅ Password reset
- ✅ Reviews and ratings
- ✅ Bookmarks with notes
- ✅ Notifications system
- ✅ Advanced search
- ✅ Multiple file upload
- ✅ Admin analytics dashboard

### v1.0.0
- ✅ User authentication
- ✅ Resource management
- ✅ Category system
- ✅ Basic search
- ✅ Download tracking

---

**Built with ❤️ for UEW School of Business**

---

*Last Updated: January 2026*
