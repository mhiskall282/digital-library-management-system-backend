# Frontend Rules & Standards

1. **Aesthetic Excellence**:
   - Deliver high-end, polished UI/UX that feels like a modern SaaS or premier academic portal.
   - Avoid generic, bland styling. Use harmonious palettes (e.g., deep navy slate, vibrant indigo, subtle amber accents, clean white cards).
   - Use Google Fonts (Inter, Plus Jakarta Sans, or Outfit) for crisp readability.

2. **Responsive by Default**:
   - All layouts must adapt fluidly from mobile (375px) to desktop (1440px+).
   - Search filters should slide into a bottom sheet or drawer on mobile viewports.

3. **State Management & Data Fetching**:
   - Use TanStack Query (React Query) or SWR for server cache, pagination, and background refetching.
   - Use Zustand or lightweight React Context for client-only state (current user, auth token, theme, toast alerts).

4. **File Downloads & Uploads**:
   - File uploads must show a visual upload progress indicator or spinner.
   - File downloads must trigger authenticated blob streams and avoid broken plain links.

5. **Error & Loading States**:
   - Never show blank screens. Use skeleton loaders or shimmer placeholders while queries load.
   - Provide clear, user-friendly error banners or toast notifications when an API call fails.
