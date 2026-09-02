<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administrative Command Center') &mdash; UEW School of Business</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col selection:bg-uew-scarlet selection:text-white" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden bg-slate-100">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs md:hidden" 
         x-cloak></div>

    <!-- Sidebar Navigation Drawer -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 flex flex-col transition-transform duration-200 ease-in-out md:static md:inset-auto md:translate-x-0 shrink-0">
        
        <!-- Sidebar Brand Header -->
        <div class="h-18 flex items-center justify-between px-5 border-b border-slate-800/90 bg-slate-950/30">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white shadow-md font-black text-lg group-hover:scale-105 transition-transform">
                    U
                </div>
                <div>
                    <span class="block text-sm font-black text-white tracking-tight leading-tight">
                        UEW <span class="text-uew-scarlet">ADMIN</span>
                    </span>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">
                        School of Business
                    </span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">✕</button>
        </div>

        @php
            $navPendingSubmissions = \App\Models\Resource::pendingReview()->count();
            $navPendingDownloads = \App\Models\DownloadRequest::where('status', 'PENDING')->count();
        @endphp

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-4 overflow-y-auto">
            
            <!-- Group 1: Executive Command -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500">Executive Command</div>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>⚡ Command Center</span>
                </a>

                <a href="{{ route('admin.analytics') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.analytics') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📊 Analytics & Metrics</span>
                </a>
            </div>

            <!-- Group 2: Desks & Moderation -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500">Desks & Moderation</div>

                <a href="{{ route('admin.moderation.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.moderation.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📝 Moderation Desk</span>
                    @if($navPendingSubmissions > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">
                            {{ $navPendingSubmissions }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.downloads.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.downloads.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>🔒 Download Approvals</span>
                    @if($navPendingDownloads > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-red-400 text-white">
                            {{ $navPendingDownloads }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.requests.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.requests.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>💬 Material Requests Desk</span>
                </a>
            </div>

            <!-- Group 3: Curriculum & Materials -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500">Curriculum & Materials</div>

                <a href="{{ route('admin.resources.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.resources.index*') || request()->routeIs('admin.resources.edit*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📁 Study Materials Directory</span>
                </a>

                <a href="{{ route('admin.resources.create') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.resources.create') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📤 Upload Material</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📚 Course Categories</span>
                </a>
            </div>

            <!-- Group 4: Scholars & Governance -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500">Scholars & Governance</div>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.users.index') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>👥 Student & Staff Directory</span>
                </a>

                <a href="{{ route('admin.users.import') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.users.import*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📑 Bulk CSV Ingestion</span>
                </a>

                <a href="{{ route('admin.broadcasts.create') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.broadcasts.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📢 Broadcast Announcements</span>
                </a>
            </div>

            <!-- Group 5: System & Settings -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500">System & Governance</div>

                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>📑 Audit Logs & Reports</span>
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.settings*') ? 'bg-uew-scarlet text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    <span>⚙ System Policies & Limits</span>
                </a>

                <a href="{{ route('home') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <span>🌐 Public Landing Page</span>
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <span>🎓 Student Catalog View</span>
                </a>
            </div>

        </nav>

        <!-- Admin Profile Footnote -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 truncate">
                    <div class="w-8 h-8 rounded-lg bg-red-900/50 border border-red-700/50 flex items-center justify-center font-bold text-xs text-red-200">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <span class="block text-xs font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] text-emerald-400 font-bold uppercase">{{ auth()->user()->role }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Main Content Stage -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Header Bar -->
        <header class="h-18 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between z-30 shrink-0">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div>
                    <span class="block text-sm font-black text-slate-900 tracking-tight">@yield('page_title', 'Administrative Command Center')</span>
                    <span class="block text-[10px] font-bold uppercase text-slate-400">UEW School of Business &middot; Academic Administration</span>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('admin.resources.create') }}" class="hidden sm:inline-flex items-center space-x-1 px-3.5 py-1.5 rounded-xl bg-uew-scarlet text-white font-bold shadow-xs hover:bg-uew-scarlet-hover transition">
                    <span>+ Upload File</span>
                </a>
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition">
                    Landing Page
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between">
                <span>⚠ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-600 font-bold">✕</button>
            </div>
        @endif

        <!-- Scrollable Main View Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</div>

</body>
</html>
