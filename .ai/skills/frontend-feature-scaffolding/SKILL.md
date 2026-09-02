---
name: frontend-feature-scaffolding
description: Instructions and design patterns for scaffolding modern, responsive frontend pages and components for the UEW Digital Library.
---

# Frontend Feature Scaffolding Skill

## When to Use
Use this skill when developing the user interface for students, lecturers, and administrators connecting to the UEW Digital Library backend.

## Design Aesthetic Mandates
- Modern, clean academic portal interface.
- Typography: Inter or Outfit via Google Fonts.
- Color Palette: University deep blue/navy (`#0f172a`, `#1e3a8a`), crisp slate backgrounds (`#f8fafc`), vibrant gold/amber accent for ratings & highlights, emerald green for verified statuses.
- Micro-interactions: Smooth hover states, transition badges, skeleton loading cards while fetching data.

## Key Screens to Scaffold

1. **Authentication Hub**:
   - Tabbed Login / Student Registration.
   - Email verification notification banner.
   - Forgot password modal / dedicated recovery page.

2. **Resource Discovery Hub (Main Student Dashboard)**:
   - Sticky header with global search bar, level selector, and notification bell with unread badge.
   - Filter sidebar: Course category, Level (`L100` - `PHD`), Semester (`FIRST` / `SECOND`), Type (`SLIDE` vs `PAST_QUESTION`), Minimum Rating.
   - Resource Card Grid: Display document icon (PDF, PPT, DOC), title, course code, average star rating, total reviews, download count, and quick bookmark toggle.

3. **Resource Detail Page**:
   - Document overview, file size, download button (triggers file download stream).
   - Interactive 5-star review submission form with live validation.
   - List of student reviews with "Helpful" upvoting.
   - Bookmark button with note editing drawer.

4. **Student Personal Hub**:
   - Saved Bookmarks with editable personal notes.
   - My Reviews history with edit/delete options.
   - Account Preferences (toggle email notifications & new material alerts).

5. **Admin Portal**:
   - Dashboard with stat cards: Total Resources, Total Users, Total Downloads, Categories.
   - Breakdown charts (Downloads by Level, Resources by Type).
   - Single & Multi-File Drag-and-Drop Uploader with progress bars.
   - Course Category Manager (Add Course Code, Name, Semester, Level).
   - User Management Table with role switches and activation toggles.
