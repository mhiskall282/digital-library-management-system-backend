@extends('layouts.admin')

@section('title', 'Admin Executive Command Center')
@section('page_title', 'Administrative Command Center')

@section('content')
<div class="space-y-6">

    <!-- Executive Greeting & Active Session Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-uew-navy-dark via-slate-900 to-uew-navy p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-uew-scarlet/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-full bg-white/10 text-white/90 text-xs font-semibold backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>System Operational &middot; {{ $academicYear }} {{ $activeSemester }} Semester</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    School of Business Operations Center
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-normal">
                    Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }}). Overseeing repository resources, student access, and course categorizations.
                </p>
            </div>

            <!-- Fast Action Shortcuts -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.resources.create') }}" class="px-4 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold shadow-md shadow-red-700/20 transition flex items-center space-x-1.5">
                    <span>+ Upload Resource</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition flex items-center space-x-1.5">
                    <span>⚙ Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Core Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Materials</span>
                <span class="p-1.5 rounded-lg bg-blue-50 text-uew-navy text-xs font-bold">Files</span>
            </div>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($totalResources) }}</span>
            <a href="{{ route('admin.resources.index') }}" class="text-[11px] font-bold text-uew-navy hover:underline block pt-1">
                View study directory &rarr;
            </a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Student Downloads</span>
                <span class="p-1.5 rounded-lg bg-red-50 text-uew-scarlet text-xs font-bold">Streams</span>
            </div>
            <span class="text-2xl font-black text-uew-scarlet block">{{ number_format($totalDownloads) }}</span>
            <span class="text-[11px] text-slate-500 block pt-1">Across all levels</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Students</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-bold">Accounts</span>
            </div>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($totalStudents) }}</span>
            <a href="{{ route('admin.users.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline block pt-1">
                Manage directory &rarr;
            </a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Course Categories</span>
                <span class="p-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold">Subjects</span>
            </div>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($totalCategories) }}</span>
            <a href="{{ route('admin.categories.index') }}" class="text-[11px] font-bold text-amber-800 hover:underline block pt-1">
                Manage courses &rarr;
            </a>
        </div>
    </div>

    <!-- System Health & Status Banner -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xs">
                ✓
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-800">System Engine Healthy</span>
                <span class="block text-[11px] text-slate-500">PostgreSQL/SQLite Relational Database &middot; Storage Disk Mounted &middot; Session Drivers Active</span>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <a href="{{ route('admin.analytics') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition">
                Detailed Analytics &rarr;
            </a>
            <a href="{{ route('admin.settings') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                System Settings
            </a>
        </div>
    </div>

    <!-- 2-Column Operational Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Column 1: Recently Ingested Materials -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Recent Materials Ingested</h3>
                <a href="{{ route('admin.resources.index') }}" class="text-xs font-bold text-uew-scarlet hover:underline">
                    View All
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentResources as $res)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="space-y-0.5 truncate pr-3">
                            <a href="{{ route('resources.show', $res) }}" class="font-bold text-slate-800 hover:text-uew-scarlet block truncate">
                                {{ $res->title }}
                            </a>
                            <span class="text-[10px] text-slate-400 block font-medium">
                                {{ $res->category->course_code ?? 'General' }} &middot; {{ $res->level }} &middot; {{ $res->type === 'SLIDE' ? 'Slide' : 'Exam' }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <span class="text-[10px] text-slate-500">{{ $res->downloads }} dl</span>
                            <a href="{{ route('admin.resources.edit', $res) }}" class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold text-[10px] hover:bg-slate-200">
                                Edit
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-4 text-center">No materials added yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Administrative Audit Feed -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">System Activity Audit Log</h3>
                <span class="text-[11px] font-bold text-slate-400">Live Security Trace</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentActivities as $act)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2.5 truncate pr-2">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase shrink-0 {{ str_contains($act->action, 'UPLOAD') ? 'bg-emerald-100 text-emerald-800' : (str_contains($act->action, 'DELETE') ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $act->action }}
                            </span>
                            <span class="text-slate-700 font-medium truncate block">
                                {{ $act->user ? $act->user->name : 'System/Student' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-slate-400 shrink-0">{{ $act->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-4 text-center">No recent audit logs.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
