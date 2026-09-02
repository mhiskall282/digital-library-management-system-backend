# Frontend Integration Guide

## 1. Visual Design Philosophy & UEW Brand Palette

The user interface adheres to the official institutional color palette of the University of Education, Winneba. All colors are defined as Tailwind CSS v4 custom tokens in `resources/css/app.css`:

```css
@theme {
    --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;

    --color-uew-scarlet: #C41E3A;        /* Primary Academic Scarlet */
    --color-uew-scarlet-hover: #A0182E;
    --color-uew-scarlet-light: #FDF2F4;
    --color-uew-navy: #1E3A8A;           /* Ultramarine Navy */
    --color-uew-navy-hover: #172554;
    --color-uew-navy-dark: #0F172A;      /* Deep Slate Surface */
    --color-uew-amber: #F59E0B;          /* 5-Star Ratings & Bookmarks */
    --color-uew-emerald: #10B981;        /* Active Status & Verification */
}
```

---

## 2. Typography

The application uses **Plus Jakarta Sans** loaded from Google Fonts via the layouts. All layouts declare:

```html
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
```

Typography scale classes used throughout:
- `font-black` (900) — Hero headings, chapter titles
- `font-bold` (700) — Labels, badges, navigation
- `font-semibold` (600) — Card titles, sub-headings
- `font-medium` (500) — Body emphasis

---

## 3. Application Layouts

The application ships 3 primary Blade layouts:

| Layout | Path | Used For |
|--------|------|----------|
| **Public/Guest** | `resources/views/layouts/guest.blade.php` | Login, Register, Password Reset — split-card responsive with hero image panel |
| **Authenticated App** | `resources/views/layouts/app.blade.php` | Student Hub, Catalog Explorer, Bookmarks, Notifications, Profile |
| **Admin Panel** | `resources/views/layouts/admin.blade.php` | Admin Dashboard, User Management, Moderation, Reports, Settings |

---

## 4. Core Component Patterns

### Interactive 5-Star Rating Widget (Alpine.js)

```blade
<div x-data="{ rating: {{ $userReview ? $userReview->rating : 5 }}, hoverRating: 0 }" class="flex items-center space-x-1">
    <template x-for="star in [1, 2, 3, 4, 5]">
        <button type="button"
                @click="rating = star"
                @mouseenter="hoverRating = star"
                @mouseleave="hoverRating = 0"
                class="text-2xl transition-transform hover:scale-110 focus:outline-none"
                :class="(hoverRating || rating) >= star ? 'text-amber-400' : 'text-slate-300'">
            ★
        </button>
    </template>
    <input type="hidden" name="rating" :value="rating">
</div>
```

### Syllabus Week Selector Dropdown

```blade
<select name="week" id="week" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
    <option value="">All Weeks</option>
    @for ($w = 1; $w <= 15; $w++)
        <option value="{{ $w }}" {{ request('week') == $w ? 'selected' : '' }}>
            Week {{ $w }}{{ $w == 7 ? ' (Mid-Semester)' : ($w == 15 ? ' (Finals)' : '') }}
        </option>
    @endfor
    <option value="0" {{ request('week') === '0' ? 'selected' : '' }}>General / Exam</option>
</select>
```

### Resource Card with Week Badge

```blade
<div class="bg-white rounded-2xl border border-slate-200 p-4 hover:shadow-md transition group">
    <div class="flex items-start justify-between gap-2 mb-2">
        <h3 class="font-bold text-slate-900 text-sm line-clamp-2">{{ $resource->title }}</h3>
        @if($resource->week)
            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black bg-uew-navy text-white">
                Wk {{ $resource->week }}
            </span>
        @endif
    </div>
    <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $resource->description }}</p>
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-uew-scarlet">{{ $resource->course_code }}</span>
        <a href="{{ route('resources.show', $resource) }}" class="text-xs font-bold text-uew-navy hover:underline">
            View →
        </a>
    </div>
</div>
```

### Contributor Rank Badge

```blade
@php
    $rank = match(true) {
        $user->points >= 300 => ['label' => '👑 Master Scholar', 'class' => 'bg-purple-100 text-purple-800'],
        $user->points >= 150 => ['label' => '🥇 Top Contributor', 'class' => 'bg-blue-100 text-blue-800'],
        $user->points >= 50  => ['label' => '🥈 Scholar Contributor', 'class' => 'bg-amber-100 text-amber-800'],
        default              => ['label' => '🥉 Novice Contributor', 'class' => 'bg-slate-100 text-slate-600'],
    };
@endphp
<span class="px-2.5 py-1 rounded-full text-[11px] font-black {{ $rank['class'] }}">
    {{ $rank['label'] }}
</span>
```

---

## 5. JavaScript Dependencies

The app uses these frontend libraries, loaded either via Vite or CDN:

| Library | Source | Purpose |
|---------|--------|---------|
| Alpine.js v3 | CDN (`cdn.jsdelivr.net`) | Reactive UI widgets (switchers, toggles, dropdowns) |
| Tailwind CSS v4 | Vite (`npm`) | Utility-first styling with custom UEW tokens |

Alpine.js is loaded via CDN in every layout to avoid bundling complexity:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 6. Responsive Breakpoints

All views are built mobile-first. The primary responsive grid pattern:

```html
<!-- Single column on mobile, 2-col on md, 3-col on lg -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

<!-- Guest layout: single col on mobile, side-by-side on md -->
<div class="grid grid-cols-1 md:grid-cols-12 min-h-[540px]">
    <div class="hidden md:flex md:col-span-5 ..."><!-- Hero Panel --></div>
    <div class="md:col-span-7 ..."><!-- Form --></div>
</div>
```

---

## 7. Flash Notification Patterns

Success, error, and status messages are rendered in each layout's content wrapper:

```blade
@if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold mb-4">
        {{ session('error') }}
    </div>
@endif
```

---

## 8. Email Template Design

All transactional email templates extend `resources/views/emails/layout.blade.php` which uses a self-contained HTML/CSS layout (no Tailwind classes — inline styles for maximum email client compatibility).

| Template | View Path | Triggered By |
|----------|-----------|-------------|
| Welcome & Activation | `emails/auth/welcome-activation.blade.php` | New user account creation |
| Security Alert | `emails/security/alert.blade.php` | Password reset, login from new device |
| Admin Broadcast | `emails/admin/broadcast.blade.php` | Admin sends departmental announcement |

Preview all templates live at: `/admin/mail-studio`
