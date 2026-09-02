<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catalog') — UEW School of Business Digital Library</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind / Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js fallback CDN if needed -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased selection:bg-uew-scarlet selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Top University Banner Stripe -->
    <div class="bg-gradient-to-r from-uew-scarlet via-red-700 to-uew-navy text-white text-xs py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="font-bold tracking-wider uppercase text-[11px]">University of Education, Winneba</span>
                <span class="text-red-200">|</span>
                <span class="text-red-100 hidden sm:inline">School of Business Digital Library Repository</span>
            </div>
            <div class="flex items-center space-x-4 text-[11px]">
                <span class="hidden md:inline text-red-100">Academic Year: 2023/2024</span>
                @auth
                    <span class="bg-white/15 px-2 py-0.5 rounded font-medium">{{ auth()->user()->level }}</span>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white border-b border-slate-200/90 sticky top-0 z-40 shadow-xs" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Title -->
                <div class="flex items-center space-x-2.5 sm:space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 sm:space-x-3 group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white shadow-sm font-black text-base sm:text-lg group-hover:scale-105 transition-transform shrink-0">
                            U
                        </div>
                        <div class="leading-none">
                            <span class="block text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                                UEW <span class="text-uew-scarlet">Library</span>
                            </span>
                            <span class="hidden min-[420px]:block text-[10px] sm:text-[11px] font-semibold text-slate-500 uppercase tracking-widest mt-0.5">
                                School of Business
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links (hidden on mobile, visible on md+) -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('student.hub') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('student.hub') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        My Study Hub
                    </a>
                    <a href="{{ route('programs.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('programs.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Programs & Levels
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('dashboard*') && !request()->routeIs('resources.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Catalog
                    </a>
                    <a href="{{ route('student.contribute') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('student.contribute*') ? 'bg-amber-100 text-amber-900' : 'text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200' }}">
                        + Submit (+50 Pts)
                    </a>
                    <a href="{{ route('requests.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('requests.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Request Material
                    </a>
                    <a href="{{ route('bookmarks.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('bookmarks.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Saved
                    </a>
                    @php
                        $unreadNavCount = auth()->check() ? auth()->user()->notifications()->where('is_read', false)->count() : 0;
                    @endphp
                    <a href="{{ route('notifications.index') }}" 
                       class="relative px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('notifications.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span>Alerts</span>
                        @if($unreadNavCount > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 ml-1 text-[10px] font-bold leading-none text-white bg-uew-scarlet rounded-full">
                                {{ $unreadNavCount }}
                            </span>
                        @endif
                    </a>

                    @if(auth()->user() && auth()->user()->canModerate())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-2.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider text-uew-navy bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                            Admin Portal
                        </a>
                    @endif
                </nav>

                <!-- Right Side Actions & Mobile Toggle -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    @auth
                        <!-- Desktop User Profile Dropdown (hidden on mobile) -->
                        <div class="relative hidden md:block" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" type="button" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center font-bold text-xs text-slate-700 overflow-hidden">
                                    @if(auth()->user()->avatar_path)
                                        <img src="{{ Storage::url(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->first_name }}</span>
                                    <span class="block text-[10px] font-medium text-slate-500">{{ auth()->user()->student_id ?: auth()->user()->role }}</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Desktop Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 divide-y divide-slate-100" 
                                 x-cloak>
                                <div class="px-4 py-2.5">
                                    <p class="text-xs text-slate-500 font-medium">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                    <div class="mt-1.5 flex items-center space-x-1.5">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700">{{ auth()->user()->level }}</span>
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded bg-blue-50 text-uew-navy truncate max-w-[120px]">{{ auth()->user()->program }}</span>
                                    </div>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-uew-scarlet">
                                        Account & Preferences
                                    </a>
                                    <a href="{{ route('bookmarks.index') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-uew-scarlet">
                                        My Saved Items
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-uew-navy hover:bg-blue-50">
                                            Admin Command Center
                                        </a>
                                        <a href="{{ route('admin.resources.create') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-uew-navy hover:bg-blue-50">
                                            Upload New Resource
                                        </a>
                                    @endif
                                </div>

                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Direct Alerts Bell Icon (visible only on mobile) -->
                        <a href="{{ route('notifications.index') }}" 
                           class="md:hidden relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
                           aria-label="Alerts">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($unreadNavCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-uew-scarlet ring-2 ring-white"></span>
                            @endif
                        </a>
                    @else
                        <!-- Desktop Guest Links (hidden on mobile to prevent navbar crowding) -->
                        <div class="hidden sm:flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-uew-scarlet px-3 py-2 transition">Sign In</a>
                            <a href="{{ route('register') }}" class="bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold px-4 py-2 rounded-xl shadow-xs transition">Register Index No.</a>
                        </div>
                    @endauth

                    <!-- Mobile Menu Hamburger / Close Toggle Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="md:hidden p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none border border-slate-200" 
                            aria-label="Toggle Navigation Menu">
                        <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5 text-uew-scarlet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer with Backdrop -->
        <div x-show="mobileMenuOpen" 
             class="fixed inset-0 top-16 z-50 md:hidden flex flex-col"
             x-cloak>
            
            <!-- Backdrop Overlay -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenuOpen = false"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs"></div>

            <!-- Drawer Container -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 -translate-y-3" 
                 x-transition:enter-end="opacity-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 -translate-y-3" 
                 class="relative bg-white border-b border-slate-200 shadow-2xl rounded-b-3xl px-4 pt-3 pb-6 max-h-[82vh] overflow-y-auto space-y-4">
                
                @auth
                    <!-- Mobile User Snapshot Card -->
                    <div class="p-3 bg-gradient-to-br from-slate-50 to-slate-100/70 rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white font-bold text-xs shadow-xs shrink-0">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <span class="block text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</span>
                                <span class="block text-[10px] text-slate-500 font-medium truncate">{{ auth()->user()->student_id ?: auth()->user()->email }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-lg bg-uew-scarlet/10 text-uew-scarlet">{{ auth()->user()->level }}</span>
                            <span class="block text-[10px] font-black text-amber-700 mt-0.5">🏆 {{ auth()->user()->contributor_points }} pts</span>
                        </div>
                    </div>

                    <!-- Quick Navigation Grid (2x2) -->
                    <div>
                        <div class="px-1 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">Study Hub &amp; Catalog</div>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('student.hub') }}" 
                               class="p-3 rounded-2xl border transition flex flex-col justify-between space-y-1 {{ request()->routeIs('student.hub') ? 'bg-uew-scarlet text-white border-uew-scarlet shadow-xs' : 'bg-slate-50 border-slate-200/80 text-slate-800 hover:bg-slate-100' }}">
                                <span class="text-lg">🎓</span>
                                <div>
                                    <span class="block text-xs font-bold leading-tight">My Study Hub</span>
                                    <span class="block text-[10px] opacity-75">Personalized</span>
                                </div>
                            </a>

                            <a href="{{ route('programs.index') }}" 
                               class="p-3 rounded-2xl border transition flex flex-col justify-between space-y-1 {{ request()->routeIs('programs.*') ? 'bg-uew-scarlet text-white border-uew-scarlet shadow-xs' : 'bg-slate-50 border-slate-200/80 text-slate-800 hover:bg-slate-100' }}">
                                <span class="text-lg">📚</span>
                                <div>
                                    <span class="block text-xs font-bold leading-tight">Programs</span>
                                    <span class="block text-[10px] opacity-75">L100 &ndash; PhD</span>
                                </div>
                            </a>

                            <a href="{{ route('dashboard') }}" 
                               class="p-3 rounded-2xl border transition flex flex-col justify-between space-y-1 {{ request()->routeIs('dashboard*') && !request()->routeIs('resources.*') ? 'bg-uew-scarlet text-white border-uew-scarlet shadow-xs' : 'bg-slate-50 border-slate-200/80 text-slate-800 hover:bg-slate-100' }}">
                                <span class="text-lg">🔍</span>
                                <div>
                                    <span class="block text-xs font-bold leading-tight">Catalog</span>
                                    <span class="block text-[10px] opacity-75">Explorer</span>
                                </div>
                            </a>

                            <a href="{{ route('student.contribute') }}" 
                               class="p-3 rounded-2xl border transition flex flex-col justify-between space-y-1 {{ request()->routeIs('student.contribute*') ? 'bg-amber-500 text-white border-amber-600 shadow-xs' : 'bg-amber-50/80 border-amber-200 text-amber-950 hover:bg-amber-100' }}">
                                <span class="text-lg">✨</span>
                                <div>
                                    <span class="block text-xs font-black leading-tight">+ Submit</span>
                                    <span class="block text-[10px] font-bold opacity-85">+50 Points</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Secondary Library Tools (Pill list) -->
                    <div class="space-y-1 pt-1 border-t border-slate-100">
                        <div class="px-1 mb-1 text-[10px] font-black uppercase tracking-wider text-slate-400">Library Tools</div>

                        <a href="{{ route('requests.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('requests.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="flex items-center space-x-2.5">
                                <span>💬</span>
                                <span>Request Materials Desk</span>
                            </span>
                            <span class="text-slate-400">&rarr;</span>
                        </a>

                        <a href="{{ route('bookmarks.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('bookmarks.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="flex items-center space-x-2.5">
                                <span>🔖</span>
                                <span>Saved Materials</span>
                            </span>
                            <span class="text-slate-400">&rarr;</span>
                        </a>

                        <a href="{{ route('notifications.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('notifications.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="flex items-center space-x-2.5">
                                <span>🔔</span>
                                <span>Notifications &amp; Alerts</span>
                            </span>
                            @if($unreadNavCount > 0)
                                <span class="px-2 py-0.5 rounded-full bg-uew-scarlet text-white text-[10px] font-bold">
                                    {{ $unreadNavCount }} new
                                </span>
                            @else
                                <span class="text-slate-400">&rarr;</span>
                            @endif
                        </a>

                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('profile.*') ? 'bg-red-50 text-uew-scarlet' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="flex items-center space-x-2.5">
                                <span>⚙️</span>
                                <span>Profile &amp; Account Settings</span>
                            </span>
                            <span class="text-slate-400">&rarr;</span>
                        </a>

                        <a href="{{ url('/docs') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                            <span class="flex items-center space-x-2.5">
                                <span>📖</span>
                                <span>User Guide &amp; Documentation</span>
                            </span>
                            <span class="text-slate-400">&rarr;</span>
                        </a>
                    </div>

                    @if(auth()->user()->canModerate())
                        <!-- Academic Administration for Staff -->
                        <div class="space-y-1 pt-1 border-t border-slate-100">
                            <div class="px-1 mb-1 text-[10px] font-black uppercase tracking-wider text-uew-navy">Academic Administration</div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="px-3 py-2 rounded-xl text-xs font-bold bg-blue-50/80 border border-blue-200 text-uew-navy hover:bg-blue-100 transition flex items-center space-x-2">
                                    <span>⚡</span>
                                    <span>Command Center</span>
                                </a>

                                <a href="{{ route('admin.moderation.index') }}" 
                                   class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition flex items-center space-x-2">
                                    <span>⚖️</span>
                                    <span>Moderation Desk</span>
                                </a>

                                <a href="{{ route('admin.mail.index') }}" 
                                   class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition flex items-center space-x-2">
                                    <span>✉️</span>
                                    <span>Email Studio</span>
                                </a>

                                <a href="{{ route('admin.settings') }}" 
                                   class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition flex items-center space-x-2">
                                    <span>🛠️</span>
                                    <span>Settings &amp; SMTP</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Sign Out Button -->
                    <div class="pt-2 border-t border-slate-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs transition border border-red-200">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest Mobile Section -->
                    <div class="space-y-4 pt-1">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition text-center">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center justify-center px-4 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold transition text-center shadow-xs">
                                Register Index No.
                            </a>
                        </div>
                        <div class="space-y-1 pt-2 border-t border-slate-100">
                            <a href="{{ route('programs.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50">📚 Academic Programs</a>
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50">🔍 Catalog Explorer</a>
                            <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50">💬 Request Material</a>
                            <a href="{{ url('/docs') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-uew-scarlet hover:bg-red-50">📖 User Guide &amp; Docs</a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Toast & Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs" role="alert">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-xs" role="alert">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">✕</button>
            </div>
        @endif

        @if(session('status'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm rounded-xl bg-blue-50 border border-blue-200 text-blue-800 shadow-xs" role="alert">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">✕</button>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- University Footer -->
    <footer class="bg-white border-t border-slate-200 mt-16 text-slate-600 text-xs py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded-md bg-uew-scarlet flex items-center justify-center text-white font-bold text-xs">U</div>
                    <span class="font-bold text-slate-800 text-sm">UEW School of Business Digital Library</span>
                </div>
                <p class="text-slate-500 max-w-md text-xs leading-relaxed">
                    The official digital archive for lecture slides, past examination question papers, and study resources for undergraduate and postgraduate students at the University of Education, Winneba.
                </p>
                <div class="text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} University of Education, Winneba. All rights reserved.
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Academic Levels</h4>
                <ul class="space-y-1.5 text-slate-500">
                    <li><a href="{{ route('dashboard', ['level' => 'L100']) }}" class="hover:text-uew-scarlet">Level 100 Resources</a></li>
                    <li><a href="{{ route('dashboard', ['level' => 'L200']) }}" class="hover:text-uew-scarlet">Level 200 Resources</a></li>
                    <li><a href="{{ route('dashboard', ['level' => 'L300']) }}" class="hover:text-uew-scarlet">Level 300 Resources</a></li>
                    <li><a href="{{ route('dashboard', ['level' => 'L400']) }}" class="hover:text-uew-scarlet">Level 400 Resources</a></li>
                    <li><a href="{{ route('dashboard', ['level' => 'MASTERS']) }}" class="hover:text-uew-scarlet">Postgraduate / MBA</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Library Support</h4>
                <ul class="space-y-1.5 text-slate-500">
                    <li><span class="text-slate-700 font-medium">Campus:</span> Winneba, Central Region, Ghana</li>
                    <li><span class="text-slate-700 font-medium">Email:</span> library@uew.edu.gh</li>
                    <li><span class="text-slate-700 font-medium">Hours:</span> Mon - Fri, 8:00 AM - 9:00 PM</li>
                    <li class="pt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                            ● System Operational
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
