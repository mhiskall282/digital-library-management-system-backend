# Frontend Design System & UI Guide: UEW School of Business

## 1. Visual Design Philosophy & UEW Brand Palette

The user interface adheres to the official institutional color palette of the University of Education, Winneba:

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

## 2. Component Patterns

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
</div>
```

### Study Notes Modal / Inline Editor
Implemented directly on each bookmark card in `bookmarks/index.blade.php`, permitting seamless note annotations without page reloads.
