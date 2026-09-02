<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>UEW School of Business &mdash; Digital Library Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-uew-scarlet selection:text-white">

    <!-- Top University Banner Stripe -->
    <div class="bg-uew-scarlet text-white text-[11px] font-semibold py-1.5 px-4 sm:px-6 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>University of Education, Winneba &middot; Faculty of Business Education</span>
            </div>
            <div class="hidden sm:flex items-center space-x-4 text-white/90 text-[10px] tracking-wider uppercase font-bold">
                <span>{{ $academicYear }} {{ $activeSemester }} Semester</span>
                <span>&bull;</span>
                <span>Accredited Academic Repository</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2.5 sm:space-x-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white shadow-md font-black text-lg sm:text-xl group-hover:scale-105 transition-transform shrink-0">
                        U
                    </div>
                    <div class="leading-none">
                        <span class="block text-base sm:text-lg font-black text-slate-900 tracking-tight">
                            UEW <span class="text-uew-scarlet">Library</span>
                        </span>
                        <span class="hidden min-[420px]:block text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                            School of Business
                        </span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-3 text-xs font-bold text-slate-600">
                    <a href="{{ route('programs.index') }}" class="px-3 py-2 rounded-xl hover:text-uew-scarlet hover:bg-slate-100 transition">
                        Academic Programs
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl hover:text-uew-scarlet hover:bg-slate-100 transition">
                        Catalog Explorer
                    </a>
                    <a href="#leaderboard" class="px-3 py-2 rounded-xl hover:text-uew-scarlet hover:bg-slate-100 transition">
                        Top Contributors 🎖️
                    </a>
                    <a href="{{ route('requests.index') }}" class="px-3 py-2 rounded-xl hover:text-uew-scarlet hover:bg-slate-100 transition">
                        Request Materials
                    </a>
                    <a href="{{ url('/docs') }}" class="px-3 py-2 rounded-xl text-uew-scarlet hover:bg-red-50 transition flex items-center space-x-1">
                        <span>📖</span>
                        <span>Guide &amp; Docs</span>
                    </a>
                </nav>

                <!-- Auth CTAs & Mobile Toggle -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 sm:px-4 py-2 rounded-xl bg-uew-navy text-white text-xs font-bold shadow-xs hover:bg-uew-navy-hover transition">
                                Command Center &rarr;
                            </a>
                        @else
                            <a href="{{ route('student.hub') }}" class="px-3 sm:px-4 py-2 rounded-xl bg-uew-scarlet text-white text-xs font-bold shadow-xs hover:bg-uew-scarlet-hover transition">
                                My Study Hub &rarr;
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-block px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-uew-scarlet transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-block px-4 py-2 rounded-xl bg-uew-scarlet text-white text-xs font-bold shadow-md shadow-red-700/20 hover:bg-uew-scarlet-hover transition">
                            Register Index No.
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none border border-slate-200" 
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

        <!-- Mobile Navigation Drawer with Backdrop -->
        <div x-show="mobileMenuOpen" 
             class="fixed inset-0 top-18 z-50 md:hidden flex flex-col" 
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenuOpen = false"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs"></div>

            <!-- Drawer Body -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 -translate-y-3" 
                 x-transition:enter-end="opacity-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 -translate-y-3" 
                 class="relative bg-white border-b border-slate-200 shadow-2xl rounded-b-3xl px-4 pt-3 pb-6 space-y-4 max-h-[82vh] overflow-y-auto">
                
                <div class="space-y-1">
                    <div class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-400">Navigation</div>
                    <a href="{{ route('programs.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        <span class="flex items-center space-x-2.5">
                            <span>📚</span>
                            <span>Academic Programs (L100–PhD)</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        <span class="flex items-center space-x-2.5">
                            <span>🔍</span>
                            <span>Catalog Explorer</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="#leaderboard" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        <span class="flex items-center space-x-2.5">
                            <span>🎖️</span>
                            <span>Top Contributors Leaderboard</span>
                        </span>
                    </a>
                    <a href="{{ route('requests.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        <span class="flex items-center space-x-2.5">
                            <span>💬</span>
                            <span>Request Materials</span>
                        </span>
                    </a>
                    <a href="{{ url('/docs') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-uew-scarlet bg-red-50/60 hover:bg-red-100 transition">
                        <span class="flex items-center space-x-2.5">
                            <span>📖</span>
                            <span>User Guide &amp; Documentation</span>
                        </span>
                    </a>
                </div>

                <div class="pt-3 border-t border-slate-200">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-uew-navy text-white font-bold text-xs shadow-xs transition">
                                <span>⚡ Admin Command Center &rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('student.hub') }}" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-uew-scarlet text-white font-bold text-xs shadow-xs transition">
                                <span>🎓 My Study Hub &rarr;</span>
                            </a>
                        @endif
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition text-center">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center justify-center px-4 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold transition text-center shadow-xs">
                                Register Index No.
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden text-white py-20 lg:py-28"
             style="background-image: linear-gradient(to bottom, rgba(15, 23, 42, 0.88), rgba(30, 58, 138, 0.90), rgba(15, 23, 42, 0.96)), url('{{ asset('images/hero_library.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-uew-scarlet/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-uew-navy/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-xs border border-white/10">
                <span class="w-2 h-2 rounded-full bg-uew-scarlet animate-ping"></span>
                <span>The Official University of Education, Winneba Business Archive</span>
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white max-w-4xl mx-auto leading-tight">
                Verified Lecture Slides & Past Exams for <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-amber-300 to-white">Business Scholars</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-normal leading-relaxed">
                Streamline revision with level-gated slides, past question papers, and student notes tailored for BIS, Banking & Finance, Accounting, Marketing, and Human Resource Management.
            </p>

            <!-- Search Jump Bar -->
            <div class="max-w-2xl mx-auto pt-4">
                <form method="GET" action="{{ route('dashboard') }}" class="bg-white/10 backdrop-blur-md p-2 rounded-2xl border border-white/20 shadow-2xl flex flex-col sm:flex-row gap-2">
                    <input type="text" name="search" placeholder="Search by course code (e.g. BNF 211, BIS 311), topic or lecturer..." 
                           class="flex-1 px-4 py-3 rounded-xl bg-white text-slate-900 text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-uew-scarlet">
                    <button type="submit" class="px-6 py-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-md">
                        Explore Repository &rarr;
                    </button>
                </form>
            </div>

            <!-- Repository Quick Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-3xl mx-auto pt-8">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl sm:text-3xl font-black text-white">{{ number_format($totalResources) }}+</span>
                    <span class="block text-[11px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Study Materials</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl sm:text-3xl font-black text-amber-300">{{ number_format($totalDownloads) }}+</span>
                    <span class="block text-[11px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Total Downloads</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl sm:text-3xl font-black text-red-300">{{ number_format($totalStudents) }}+</span>
                    <span class="block text-[11px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Active Scholars</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl sm:text-3xl font-black text-emerald-300">{{ number_format($totalCourses) }}</span>
                    <span class="block text-[11px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Courses Offered</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Programs Showcase -->
    <section class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Explore by Degree Program
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Course slide archives and examination papers organized systematically from Level 100 to PhD.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredPrograms as $prog)
                    <div class="group p-6 rounded-3xl border border-slate-200 hover:border-uew-navy hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-4 bg-slate-50/50">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-2xl shadow-xs">
                                {{ $prog['icon'] }}
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700">
                                    {{ $prog['code'] }} Stream
                                </span>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-uew-navy transition mt-1.5 leading-snug">
                                    {{ $prog['name'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ $prog['description'] }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-400">L100 &middot; L200 &middot; L300 &middot; L400</span>
                            <a href="{{ route('programs.index') }}" class="text-uew-scarlet group-hover:underline">
                                View Stream &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('programs.index') }}" class="inline-flex items-center space-x-2 px-6 py-3 rounded-2xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition shadow-xs">
                    <span>Browse All Academic Programs & Streams</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Campus & Academic Experience Visual Showcase -->
    <section class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold text-uew-scarlet uppercase tracking-wider">World-Class Learning Environment</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Dedicated to Scholarly Excellence in Business Education</h2>
                <p class="text-xs sm:text-sm text-slate-500">Equipping students with modern lecture materials, accredited syllabi, and collaborative research archives.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Image 1: Faculty Complex -->
                <div class="relative group rounded-3xl overflow-hidden shadow-xl border border-slate-200 aspect-16/10">
                    <img src="{{ asset('images/campus_building.jpg') }}" alt="UEW School of Business Complex" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent flex flex-col justify-end p-6 text-white">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-uew-scarlet text-white w-fit mb-2">
                            Academic Complex &middot; Winneba
                        </span>
                        <h3 class="text-xl font-black">Modern Academic Facilities</h3>
                        <p class="text-xs text-slate-200 mt-1 max-w-md">World-class lecture theatres and digital research labs supporting over 3,000 enrolled business students.</p>
                    </div>
                </div>

                <!-- Image 2: Student Collaboration -->
                <div class="relative group rounded-3xl overflow-hidden shadow-xl border border-slate-200 aspect-16/10">
                    <img src="{{ asset('images/collaboration.jpg') }}" alt="Scholars Collaborating" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent flex flex-col justify-end p-6 text-white">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-uew-navy text-white w-fit mb-2">
                            Peer Learning &middot; Study Circles
                        </span>
                        <h3 class="text-xl font-black">Collaborative Revision & Notes</h3>
                        <p class="text-xs text-slate-200 mt-1 max-w-md">Share verified lecture summaries, past question analyses, and earn recognized scholar contributor badges.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Study Materials Grid -->
    <section class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                        Trending Study Materials
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Most downloaded lecture decks and examination revisions this semester.
                    </p>
                </div>
                <a href="{{ route('dashboard', ['sort' => 'popular']) }}" class="text-xs font-bold text-uew-scarlet hover:underline">
                    View Complete Catalog &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($trendingResources as $res)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $res->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy border border-blue-200' : 'bg-red-50 text-uew-scarlet border border-red-200' }}">
                                    {{ $res->type === 'SLIDE' ? 'Lecture Slide' : 'Past Exam' }}
                                </span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $res->level }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 mt-2 leading-snug">
                                <a href="{{ route('resources.show', $res) }}" class="hover:text-uew-scarlet transition">
                                    {{ $res->title }}
                                </a>
                            </h3>
                            <span class="text-[11px] font-bold text-uew-navy block mt-1">
                                {{ $res->category->course_code ?? '' }} &middot; {{ $res->category->course_name ?? '' }}
                            </span>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-bold text-amber-500">★ {{ number_format($res->average_rating, 1) }}</span>
                            <span>{{ number_format($res->downloads) }} downloads</span>
                            <a href="{{ route('resources.show', $res) }}" class="font-bold text-uew-scarlet hover:underline">
                                Details &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Student Contributor Leaderboard (Incentivization & Gamification) -->
    <section id="leaderboard" class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold">
                    <span>🏆 Academic Contribution Leaderboard</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Top Student Scholars & Contributors
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Scholars who contribute verified lecture notes, exam papers, and insightful study reviews earn points, badges, and recognition.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @forelse($topContributors as $index => $contributor)
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 text-center space-y-2 relative overflow-hidden">
                        <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-amber-400 text-white font-black text-xs flex items-center justify-center shadow-xs">
                            #{{ $index + 1 }}
                        </div>
                        <div class="w-14 h-14 rounded-full bg-white border-2 border-amber-300 flex items-center justify-center font-black text-base text-slate-800 mx-auto shadow-xs">
                            {{ strtoupper(substr($contributor->first_name, 0, 1) . substr($contributor->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900 leading-snug">{{ $contributor->name }}</span>
                            <span class="block text-[10px] text-slate-500">{{ $contributor->level }} &middot; {{ $contributor->program }}</span>
                        </div>
                        <div class="pt-1">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900">
                                🎖️ {{ $contributor->contributor_rank }}
                            </span>
                            <span class="block text-[11px] font-black text-uew-scarlet mt-1">
                                {{ number_format($contributor->contributor_points) }} Points
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-5 p-8 text-center text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-200">
                        Join the community! Upload verified past exams or lecture slides to appear on the top contributors leaderboard.
                    </div>
                @endforelse
            </div>

            <!-- Call to Contribute -->
            <div class="p-6 rounded-3xl bg-gradient-to-r from-uew-navy to-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
                <div class="space-y-1 text-center sm:text-left">
                    <span class="text-sm font-bold text-amber-300 block">Have Lecture Slides or Exam Papers?</span>
                    <p class="text-xs text-slate-300">Earn +50 Contributor Points when your submitted document is approved by library staff.</p>
                </div>
                <a href="{{ route('student.contribute') }}" class="px-5 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold shadow-xs transition shrink-0">
                    Submit Study Document &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- Institutional Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-3 md:col-span-2">
                <div class="flex items-center space-x-2 text-white font-black text-base">
                    <div class="w-7 h-7 rounded-lg bg-uew-scarlet flex items-center justify-center text-white text-xs">U</div>
                    <span>UEW School of Business Digital Library</span>
                </div>
                <p class="text-slate-400 text-xs max-w-md leading-relaxed">
                    Official centralized repository platform serving undergraduate and postgraduate students, faculty members, and research staff of the University of Education, Winneba.
                </p>
                <div class="text-[11px] text-slate-500">
                    Location: School of Business Complex, North Campus, Winneba, Ghana.
                </div>
            </div>

            <div class="space-y-2">
                <span class="block text-xs font-bold uppercase tracking-wider text-white">Direct Navigation</span>
                <ul class="space-y-1.5 text-xs">
                    <li><a href="{{ route('programs.index') }}" class="hover:text-white transition">Academic Programs</a></li>
                    <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Catalog Explorer</a></li>
                    <li><a href="{{ route('requests.index') }}" class="hover:text-white transition">Request Missing Material</a></li>
                    <li><a href="{{ route('student.contribute') }}" class="hover:text-white transition">Submit Document</a></li>
                </ul>
            </div>

            <div class="space-y-2">
                <span class="block text-xs font-bold uppercase tracking-wider text-white">Institutional Support</span>
                <p class="text-xs text-slate-400">
                    Email: <a href="mailto:library@uew.edu.gh" class="text-uew-scarlet hover:underline">library@uew.edu.gh</a><br>
                    Hours: Mon &ndash; Fri &middot; 8:00 AM &ndash; 6:00 PM GMT
                </p>
                <div class="pt-2">
                    <a href="{{ route('login') }}" class="inline-block px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-[11px] transition">
                        Staff & Student Login
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-900 py-4 px-4 text-center text-[11px] text-slate-500">
            &copy; {{ date('Y') }} University of Education, Winneba (UEW) &mdash; School of Business. All rights reserved.
        </div>
    </footer>

</body>
</html>
