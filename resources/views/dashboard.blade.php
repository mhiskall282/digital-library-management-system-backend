@extends('layouts.app')

@section('title', 'Catalog Explorer')

@section('content')
<div class="space-y-6">

    <!-- Hero Welcome & Stats Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-uew-navy-dark via-slate-900 to-uew-navy p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-uew-scarlet/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-full bg-white/10 text-white/90 text-xs font-semibold backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-uew-scarlet animate-pulse"></span>
                    <span>School of Business Repository</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Discover Lecture Slides & Exam Archives
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed">
                    Verified course notes, past questions, and academic resources tailored for undergraduate and postgraduate business students at UEW.
                </p>
            </div>

            <!-- Quick Counter Metric Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-xl font-black text-white">{{ $totalResources }}</span>
                    <span class="block text-[11px] font-medium text-slate-300 uppercase tracking-wider">Total Files</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-xl font-black text-amber-300">{{ $totalSlides }}</span>
                    <span class="block text-[11px] font-medium text-slate-300 uppercase tracking-wider">Slides</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-xl font-black text-red-300">{{ $totalPastQuestions }}</span>
                    <span class="block text-[11px] font-medium text-slate-300 uppercase tracking-wider">Past Exams</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                    <span class="block text-xl font-black text-emerald-300">{{ $myBookmarksCount }}</span>
                    <span class="block text-[11px] font-medium text-slate-300 uppercase tracking-wider">Saved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Multi-Filter Control Panel -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs" x-data="{ expanded: false }">
        <form method="GET" action="{{ route('dashboard') }}" class="space-y-4">
            <!-- Search Bar Row -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Search by topic, course title, code (e.g. BBA 111), or keywords..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet transition placeholder:text-slate-400">
                </div>

                <div class="flex items-center space-x-2">
                    <select name="sort" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-uew-scarlet">
                        <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest Uploads</option>
                        <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Most Downloaded</option>
                        <option value="top_rated" {{ $sort == 'top_rated' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="title_asc" {{ $sort == 'title_asc' ? 'selected' : '' }}>Title (A - Z)</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold shadow-xs transition">
                        Filter
                    </button>

                    @if($search || $level || $semester || $type || $categoryId)
                        <a href="{{ route('dashboard') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Clear all filters">
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            <!-- Academic Level Pills -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 pt-1 text-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-2 shrink-0">Level:</span>
                <a href="{{ route('dashboard', array_merge(request()->except('level', 'page'), ['level' => ''])) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition {{ !$level ? 'bg-uew-navy text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Levels
                </a>
                @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                    <a href="{{ route('dashboard', array_merge(request()->except('level', 'page'), ['level' => $lvl])) }}" 
                       class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition {{ $level === $lvl ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $lvl }}
                    </a>
                @endforeach
            </div>

            <!-- Secondary Filter Toggles: Type, Week, Semester, Category Dropdown -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                <!-- Type Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Resource Type</label>
                    <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                        <option value="">All Types (Slides & Past Exams)</option>
                        <option value="SLIDE" {{ $type == 'SLIDE' ? 'selected' : '' }}>Lecture Slides Only</option>
                        <option value="PAST_QUESTION" {{ $type == 'PAST_QUESTION' ? 'selected' : '' }}>Past Examination Papers</option>
                    </select>
                </div>

                <!-- Syllabus Week Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Syllabus Week</label>
                    <select name="week" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                        <option value="">All Weeks (1–15)</option>
                        @for($w = 1; $w <= 15; $w++)
                            <option value="{{ $w }}" {{ request('week') == $w ? 'selected' : '' }}>Week {{ $w }} Module</option>
                        @endfor
                    </select>
                </div>

                <!-- Semester Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Semester</label>
                    <select name="semester" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                        <option value="">Both Semesters</option>
                        <option value="FIRST" {{ $semester == 'FIRST' ? 'selected' : '' }}>First Semester</option>
                        <option value="SECOND" {{ $semester == 'SECOND' ? 'selected' : '' }}>Second Semester</option>
                    </select>
                </div>

                <!-- Course Category Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Specific Course</label>
                    <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                        <option value="">All Courses</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                {{ $cat->course_code }} - {{ $cat->course_name }} ({{ $cat->level }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Chips (if any applied) -->
    @if($search || $level || $semester || $type || $categoryId)
        <div class="flex items-center flex-wrap gap-2 text-xs">
            <span class="text-slate-400 font-medium">Active filters:</span>
            @if($search)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-200 text-slate-800 font-medium">
                    Search: "{{ $search }}"
                </span>
            @endif
            @if($level)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-100 text-uew-scarlet font-semibold">
                    {{ $level }}
                </span>
            @endif
            @if($type)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100 text-uew-navy font-semibold">
                    {{ $type === 'SLIDE' ? 'Lecture Slides' : 'Past Examination' }}
                </span>
            @endif
            @if($semester)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">
                    {{ $semester }} Semester
                </span>
            @endif
        </div>
    @endif

    <!-- Resource Cards Grid -->
    @if($resources->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($resources as $resource)
                <div class="group bg-white rounded-2xl border border-slate-200/90 hover:border-slate-300 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    <div class="p-5 space-y-3">
                        <!-- Card Header Badges & Bookmark Icon -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $resource->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy border border-blue-200' : 'bg-red-50 text-uew-scarlet border border-red-200' }}">
                                    {{ $resource->type === 'SLIDE' ? 'Lecture Slide' : 'Past Exam' }}
                                </span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $resource->level }}
                                </span>
                                @if($resource->week)
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                        Week {{ $resource->week }}
                                    </span>
                                @endif
                            </div>

                            <!-- Bookmark Action -->
                            <form method="POST" action="{{ route('resources.bookmark.toggle', $resource) }}">
                                @csrf
                                <button type="submit" title="Toggle Bookmark" class="p-1.5 rounded-lg text-slate-400 hover:text-uew-amber hover:bg-amber-50 transition">
                                    @if($resource->isBookmarkedBy(auth()->user()))
                                        <svg class="w-5 h-5 text-uew-amber fill-current" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <!-- Course Code & Title -->
                        <div>
                            <span class="block text-xs font-bold text-uew-navy">
                                {{ $resource->category->course_code ?? 'Course' }} &middot; {{ $resource->category->course_name ?? '' }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-uew-scarlet transition-colors line-clamp-2 mt-1 leading-snug">
                                <a href="{{ route('resources.show', $resource) }}">
                                    {{ $resource->title }}
                                </a>
                            </h3>
                        </div>

                        <!-- Description Snippet -->
                        @if($resource->description)
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $resource->description }}
                            </p>
                        @endif

                        <!-- Rating & Downloads Metadata Row -->
                        <div class="pt-2 flex items-center justify-between text-xs text-slate-500 border-t border-slate-100">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1">
                                <div class="flex text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($resource->average_rating))
                                            ★
                                        @else
                                            <span class="text-slate-300">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <span class="font-bold text-slate-800 text-[11px]">{{ number_format($resource->average_rating, 1) }}</span>
                                <span class="text-[10px] text-slate-400">({{ $resource->total_reviews }})</span>
                            </div>

                            <!-- Downloads Count -->
                            <div class="flex items-center space-x-1 text-slate-500 font-medium text-[11px]">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>{{ number_format($resource->downloads) }} downloads</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="bg-slate-50/90 px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            {{ $resource->extension }} &middot; {{ $resource->formatted_size }}
                        </span>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('resources.show', $resource) }}" 
                               class="text-xs font-bold text-slate-700 hover:text-uew-scarlet px-2 py-1 rounded transition">
                                View Details
                            </a>
                            <a href="{{ route('resources.download', $resource) }}" 
                               class="inline-flex items-center space-x-1 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xs transition">
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="pt-6">
            {{ $resources->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-xs max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-uew-scarlet flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">No resources found</h3>
            <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto">
                We couldn't find any lecture slides or exam papers matching your current search parameters. Try adjusting your level or category filters.
            </p>
            <div class="mt-5">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Reset All Filters
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
