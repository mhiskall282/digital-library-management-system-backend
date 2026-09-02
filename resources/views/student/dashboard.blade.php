@extends('layouts.app')

@section('title', 'Student Study Hub')

@section('content')
<div class="space-y-6">

    <!-- Personalized Student Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-uew-navy-dark via-slate-900 to-uew-navy p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-uew-scarlet/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Enrolled in {{ $user->program }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ $user->first_name }}! 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed">
                    Access course lecture slide archives, past examination papers, and your personal revision notes for <strong>{{ $user->level }} &middot; {{ $activeSemester }} Semester</strong>.
                </p>
            </div>

            <!-- Student Metrics Card Group -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 shrink-0">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-lg font-black text-amber-300">{{ $user->contributor_points }} pts</span>
                    <span class="block text-[9px] font-bold text-slate-300 uppercase tracking-wider">{{ $user->contributor_rank }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-lg font-black text-white">{{ $bookmarksCount }}</span>
                    <span class="block text-[9px] font-bold text-slate-300 uppercase tracking-wider">Bookmarks</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-lg font-black text-emerald-300">{{ $reviewsCount }}</span>
                    <span class="block text-[9px] font-bold text-slate-300 uppercase tracking-wider">Reviews</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-lg font-black text-red-300">{{ $unreadNotificationsCount }}</span>
                    <span class="block text-[9px] font-bold text-slate-300 uppercase tracking-wider">Alerts</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts & Action Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-3">
        <div class="flex items-center space-x-2 text-xs font-bold w-full lg:w-auto overflow-x-auto pb-1 lg:pb-0">
            <span class="text-slate-400 uppercase text-[10px] tracking-wider whitespace-nowrap">Study Desk:</span>
            <a href="{{ route('student.contribute') }}" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition whitespace-nowrap">
                + Submit Slides (+50 Pts)
            </a>
            <a href="{{ route('requests.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition whitespace-nowrap">
                Request Material
            </a>
            <a href="{{ route('programs.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition whitespace-nowrap">
                Programs & Levels Directory
            </a>
            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition whitespace-nowrap">
                Full Catalog
            </a>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="w-full lg:w-72">
            <div class="relative">
                <input type="text" name="search" placeholder="Search slides, courses..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-uew-scarlet bg-slate-50 focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>
    </div>

    <!-- Active Stream Courses Grid -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Your Enrolled Stream Courses ({{ $user->level }})</h2>
                <p class="text-xs text-slate-500">Curriculum courses tailored to your degree program for active revision.</p>
            </div>
            <a href="{{ route('programs.index') }}" class="text-xs font-bold text-uew-scarlet hover:underline">
                View All Streams &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($myCourses as $course)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-uew-navy transition flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-uew-navy border border-blue-100">
                                {{ $course->course_code }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400">
                                {{ $course->semester }} Semester
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-2 leading-snug">
                            {{ $course->course_name }}
                        </h3>
                        @if($course->description)
                            <p class="text-xs text-slate-500 line-clamp-2 mt-1">{{ $course->description }}</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-500">{{ $course->resources_count }} materials available</span>
                        <a href="{{ route('dashboard', ['category_id' => $course->id]) }}" class="text-uew-scarlet hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-8 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-500">No courses listed under your level yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recommendations, Bookmarks, and Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Intelligent Recommended Materials -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Recommended for You</h3>
                    <span class="text-[10px] text-slate-400">Targeted to {{ $user->program }}</span>
                </div>
                <a href="{{ route('dashboard', ['level' => $user->level]) }}" class="text-xs font-bold text-uew-scarlet hover:underline">
                    Explore &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recommendedResources as $res)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 truncate pr-2">
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">
                                {{ $res->type === 'SLIDE' ? 'Lecture Slide' : 'Past Exam' }} &middot; {{ $res->category->course_code ?? '' }}
                            </span>
                            <a href="{{ route('resources.show', $res) }}" class="text-xs font-bold text-slate-900 hover:text-uew-scarlet block truncate">
                                {{ $res->title }}
                            </a>
                            <span class="text-[10px] text-slate-400 block">{{ $res->downloads }} dl &middot; ★ {{ number_format($res->average_rating, 1) }}</span>
                        </div>
                        <a href="{{ route('resources.download', $res) }}" class="px-2.5 py-1 rounded-lg bg-uew-scarlet text-white text-[10px] font-bold shrink-0">
                            Download
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-6 text-center">No recommended materials found for this level yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Saved Revision Bookmarks -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Saved Study Bookmarks</h3>
                <a href="{{ route('bookmarks.index') }}" class="text-xs font-bold text-uew-scarlet hover:underline">
                    View All ({{ $bookmarksCount }})
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentBookmarks as $bm)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50 flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-uew-navy uppercase block">
                                {{ $bm->resource->category->course_code ?? '' }} &middot; {{ $bm->resource->level }}
                            </span>
                            <a href="{{ route('resources.show', $bm->resource) }}" class="text-xs font-bold text-slate-900 hover:text-uew-scarlet block leading-snug">
                                {{ $bm->resource->title }}
                            </a>
                            @if($bm->notes)
                                <p class="text-[11px] text-amber-800 bg-amber-50/80 p-1.5 rounded-md italic border border-amber-200/60">
                                    "{{ $bm->notes }}"
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('resources.download', $bm->resource) }}" class="px-2.5 py-1 rounded-lg bg-uew-navy text-white text-[10px] font-bold shrink-0">
                            Download
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-6 text-center">No bookmarks saved yet. Click the bookmark icon on any lecture slide to save it here!</p>
                @endforelse
            </div>
        </div>

        <!-- Contributor Leaderboard Preview -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Top Student Contributors</h3>
                <a href="{{ route('home') }}#leaderboard" class="text-xs font-bold text-uew-scarlet hover:underline">
                    Leaderboard &rarr;
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($topContributors as $idx => $c)
                    <div class="p-2.5 rounded-xl bg-slate-50 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2.5 truncate">
                            <span class="w-5 h-5 rounded-full bg-amber-400 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <span class="font-bold text-slate-900 block truncate">{{ $c->name }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $c->level }} &middot; {{ $c->contributor_rank }}</span>
                            </div>
                        </div>
                        <span class="font-black text-uew-scarlet text-[11px] shrink-0">
                            {{ $c->contributor_points }} pts
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-6 text-center">Be the first to upload lecture notes!</p>
                @endforelse
            </div>

            <div class="pt-2">
                <a href="{{ route('student.contribute') }}" class="block text-center py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold text-xs rounded-xl transition">
                    + Earn Points by Uploading
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
