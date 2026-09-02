@extends('layouts.admin')

@section('title', 'Audit Logs & Governance Reports')
@section('page_title', 'System Audit Logs & Compliance Reporting')

@section('content')
<div class="space-y-6">

    <!-- Header & Export Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Institutional Audit Logs & Compliance Reports</h1>
            <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit trail of material downloads, IP tracking, student submissions, and security authentications.</p>
        </div>

        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.reports.export', request()->all()) }}" 
               class="px-4 py-2 bg-uew-navy hover:bg-uew-navy-hover text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center space-x-1.5">
                <span>📥 Export Audit Report (CSV)</span>
            </a>
        </div>
    </div>

    <!-- Metrics Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="block text-2xl font-black text-slate-900">{{ number_format($totalLogs) }}</span>
            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Total System Events</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="block text-2xl font-black text-amber-500">{{ number_format($totalDownloadsLogged) }}</span>
            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">IP-Audited Downloads</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="block text-2xl font-black text-emerald-600">{{ number_format($totalSubmissions) }}</span>
            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Submissions & Approvals</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="block text-2xl font-black text-uew-scarlet">{{ number_format($totalSecurityEvents) }}</span>
            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Security & Auth Logs</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col lg:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by student index, user name, IP address, or course code..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Action Filter -->
            <select name="action" onchange="this.form.submit()" class="w-full lg:w-48 px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                <option value="ALL">All Event Types</option>
                @foreach($actionTypes as $act)
                    <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>

            <!-- Date Range Filter -->
            <select name="date_range" onchange="this.form.submit()" class="w-full lg:w-40 px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                <option value="ALL" {{ $dateRange === 'ALL' ? 'selected' : '' }}>All Time</option>
                <option value="TODAY" {{ $dateRange === 'TODAY' ? 'selected' : '' }}>Today Only</option>
                <option value="WEEK" {{ $dateRange === 'WEEK' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="MONTH" {{ $dateRange === 'MONTH' ? 'selected' : '' }}>Last 30 Days</option>
            </select>

            <button type="submit" class="w-full lg:w-auto px-5 py-2 bg-uew-scarlet text-white font-bold text-xs rounded-xl shadow-xs hover:bg-uew-scarlet-hover transition">
                Filter Logs
            </button>

            @if($search || ($action && $action !== 'ALL') || ($dateRange && $dateRange !== 'ALL'))
                <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-uew-scarlet">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Timestamp (GMT)</th>
                        <th class="px-4 py-3.5">User / Initiator</th>
                        <th class="px-4 py-3.5">Action Event</th>
                        <th class="px-4 py-3.5">Subject Material / Course</th>
                        <th class="px-4 py-3.5">IP & Client Info</th>
                        <th class="px-5 py-3.5 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block">{{ $log->user->name ?? 'System / Anonymous' }}</span>
                                <span class="text-[10px] text-slate-400 block">
                                    {{ $log->user->student_id ?? $log->user->email ?? '' }} 
                                    @if($log->user)
                                        &middot; <span class="uppercase font-semibold text-slate-600">{{ $log->user->role }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->action) {
                                        'DOWNLOAD' => 'bg-amber-100 text-amber-900',
                                        'DOWNLOAD_REQUESTED', 'DOWNLOAD_APPROVED' => 'bg-blue-100 text-blue-900',
                                        'STUDENT_SUBMISSION', 'SUBMISSION_APPROVED' => 'bg-emerald-100 text-emerald-900',
                                        'BROADCAST_SENT' => 'bg-purple-100 text-purple-900',
                                        'LOGIN', 'LOGOUT' => 'bg-slate-100 text-slate-700',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 max-w-xs">
                                @if($log->resource)
                                    <span class="font-bold text-slate-900 block truncate leading-tight">{{ $log->resource->title }}</span>
                                    <span class="text-[10px] text-uew-navy font-semibold block">{{ $log->resource->category->course_code ?? '' }} &middot; {{ $log->resource->level }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-[10px] text-slate-500 font-mono">
                                <span class="font-bold text-slate-700 block">{{ $log->details['ip'] ?? $log->ip_address ?? '127.0.0.1' }}</span>
                                <span class="text-slate-400 truncate block max-w-[180px]">{{ $log->details['user_agent'] ?? '' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if(!empty($log->details))
                                    <span class="text-[10px] text-slate-500 font-mono bg-slate-100 px-2 py-1 rounded">
                                        {{ count($log->details) }} fields
                                    </span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">
                                No activity records match the selected audit criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
