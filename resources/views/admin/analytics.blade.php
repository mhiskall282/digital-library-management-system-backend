@extends('layouts.admin')

@section('title', 'Analytics & Metrics')
@section('page_title', 'Analytics & Operational Metrics')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Resources -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Materials</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalResources) }}</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-uew-navy flex items-center justify-center font-bold text-xs">
                    📁
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block">Lecture slides & past examination papers</span>
        </div>

        <!-- Card 2: Total Downloads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Student Downloads</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-uew-scarlet">{{ number_format($totalDownloads) }}</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 text-uew-scarlet flex items-center justify-center font-bold text-xs">
                    ⚡
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block">Student study file streams served</span>
        </div>

        <!-- Card 3: Enrolled Students -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Students</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalUsers) }}</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    🎓
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block">Registered School of Business accounts</span>
        </div>

        <!-- Card 4: Average Satisfaction -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Average Rating</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-amber-500">{{ number_format($averageRating, 1) }} / 5.0</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                    ★
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block">Across {{ $totalReviews }} student review submissions</span>
        </div>
    </div>

    <!-- Distribution Breakdown Grids -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Academic Level Distribution -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Resource Distribution by Academic Level</h3>
                <span class="text-xs font-semibold text-slate-400">L100 - PHD</span>
            </div>

            <div class="space-y-3">
                @foreach($levelDistribution as $levelStat)
                    @php
                        $pct = $totalResources > 0 ? round(($levelStat->count / $totalResources) * 100) : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $levelStat->level }}</span>
                            <span class="text-slate-500">{{ $levelStat->count }} materials ({{ $levelStat->total_downloads }} downloads &middot; {{ $pct }}%)</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-uew-navy to-uew-scarlet rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Material Type Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Format Proportions</h3>
            </div>

            <div class="space-y-4 pt-2">
                @foreach($typeDistribution as $typeStat)
                    @php
                        $typePct = $totalResources > 0 ? round(($typeStat->count / $totalResources) * 100) : 0;
                    @endphp
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">
                                {{ $typeStat->type === 'SLIDE' ? 'Lecture Slides' : 'Past Exams' }}
                            </span>
                            <span class="text-[11px] text-slate-400">{{ $typeStat->count }} files available</span>
                        </div>
                        <span class="text-lg font-black text-slate-900">{{ $typePct }}%</span>
                    </div>
                @endforeach

                <div class="pt-2 text-center">
                    <a href="{{ route('admin.resources.create') }}" class="w-full block py-2 px-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold rounded-xl shadow-xs transition">
                        + Upload More Materials
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Downloaded & Top Rated Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Downloaded -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                🔥 Most Downloaded Study Materials
            </h3>

            <div class="divide-y divide-slate-100">
                @foreach($topDownloaded as $res)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="space-y-0.5 truncate pr-3">
                            <span class="font-bold text-slate-800 block truncate">{{ $res->title }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $res->category->course_code ?? '' }} &middot; {{ $res->level }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-red-50 text-uew-scarlet font-bold shrink-0">
                            {{ number_format($res->downloads) }} dl
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Rated -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                ⭐ Highest Rated Materials
            </h3>

            <div class="divide-y divide-slate-100">
                @foreach($topRated as $res)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="space-y-0.5 truncate pr-3">
                            <span class="font-bold text-slate-800 block truncate">{{ $res->title }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $res->category->course_code ?? '' }} &middot; {{ $res->level }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-bold shrink-0">
                            ★ {{ number_format($res->average_rating, 1) }} ({{ $res->total_reviews }})
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Administrative Activity Stream -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
            System Activity Audit Trail
        </h3>

        <div class="divide-y divide-slate-100">
            @foreach($recentActivities as $act)
                <div class="py-2.5 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ str_contains($act->action, 'UPLOAD') ? 'bg-emerald-100 text-emerald-800' : (str_contains($act->action, 'DELETE') ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                            {{ $act->action }}
                        </span>
                        <span class="text-slate-700 font-medium">
                            {{ $act->user ? $act->user->name : 'Anonymous Student' }}
                        </span>
                        @if($act->ip_address)
                            <span class="text-slate-400 text-[10px] hidden sm:inline">[{{ $act->ip_address }}]</span>
                        @endif
                    </div>
                    <span class="text-slate-400 text-[11px]">{{ $act->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
