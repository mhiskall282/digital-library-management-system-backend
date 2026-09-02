@extends('layouts.admin')

@section('title', 'Download Access Requests')
@section('page_title', 'Material Download Approvals & IP Audit')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, currentReq: {} }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Material Download Approvals & IP Audit Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage intellectual property compliance, verify student download justifications, and track client IP logs.</p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 text-xs font-bold">
                {{ $pendingCount }} Pending Access Requests
            </span>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Material Details</th>
                        <th class="px-4 py-3.5">Student</th>
                        <th class="px-4 py-3.5">Academic Justification</th>
                        <th class="px-4 py-3.5">IP Address & Client</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 max-w-xs">
                                <a href="{{ route('resources.show', $req->resource) }}" class="font-bold text-slate-900 hover:text-uew-scarlet block truncate leading-tight">
                                    {{ $req->resource->title }}
                                </a>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ $req->resource->category->course_code ?? 'Course' }} &middot; {{ $req->resource->level }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">{{ $req->user->name ?? 'Student' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $req->user->student_id ?? '' }} &middot; {{ $req->user->program ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3.5 max-w-sm">
                                <p class="text-[11px] text-slate-700 italic leading-snug">
                                    "{{ $req->reason }}"
                                </p>
                                @if($req->rejection_reason)
                                    <p class="text-[10px] text-red-600 mt-1">Declined: {{ $req->rejection_reason }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-500 text-[10px]">
                                <span class="font-mono font-bold text-slate-700 block">{{ $req->ip_address ?: 'Unknown IP' }}</span>
                                <span class="text-slate-400 truncate block max-w-xs">{{ $req->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $req->isApproved() ? 'bg-emerald-100 text-emerald-800' : ($req->isPending() ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                @if($req->isPending())
                                    <form method="POST" action="{{ route('admin.downloads.approve', $req) }}" class="inline-block" onsubmit="return confirm('Grant download access for this student?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow-2xs">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <button type="button" 
                                            @click="currentReq = @js($req); rejectModalOpen = true"
                                            class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold text-[10px]">
                                        ✕ Decline
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">
                                No download approval requests logged.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div x-show="rejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="rejectModalOpen = false"></div>
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Decline Download Access</h3>
                    <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/download-requests') }}/' + currentReq.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <p class="text-xs text-slate-600 mb-2">State reason for declining access to this student:</p>
                        <textarea name="reason" rows="3" required placeholder="State reason (e.g. Inadequate justification, restricted exam archive)..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" @click="rejectModalOpen = false" class="px-3.5 py-2 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold">Decline Access</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
