@extends('layouts.admin')

@section('title', 'Student Material Requests')
@section('page_title', 'Material & Support Requests Desk')

@section('content')
<div class="space-y-6" x-data="{ updateModalOpen: false, currentReq: {} }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Support & Material Requests Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Track missing lecture slides, syllabus topics, and past examination papers requested by students.</p>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Course Code & Topic</th>
                        <th class="px-4 py-3.5">Requesting Student</th>
                        <th class="px-4 py-3.5">Level & Urgency</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Logged</th>
                        <th class="px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="font-black text-uew-navy block text-sm">{{ $req->course_code }}</span>
                                <span class="font-bold text-slate-800 block text-xs truncate leading-snug">{{ $req->course_name }}</span>
                                <span class="text-[11px] text-slate-600 block mt-0.5">{{ $req->topic }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">{{ $req->user->name ?? 'Student' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $req->user->student_id ?? '' }} &middot; {{ $req->user->program ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700 block w-max">{{ $req->level }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase block w-max mt-1 {{ $req->urgency === 'HIGH' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $req->urgency }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $req->status === 'FULFILLED' ? 'bg-emerald-100 text-emerald-800' : ($req->status === 'IN_PROGRESS' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-400 text-[11px]">
                                {{ $req->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <button type="button" 
                                        @click="currentReq = @js($req); updateModalOpen = true"
                                        class="px-3 py-1.5 rounded-lg bg-uew-navy hover:bg-uew-navy-hover text-white font-bold text-[10px] shadow-2xs">
                                    Update Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">
                                No material requests currently logged.
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

    <!-- Update Request Modal -->
    <div x-show="updateModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="updateModalOpen = false"></div>
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Update Request Status</h3>
                    <button @click="updateModalOpen = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/material-requests') }}/' + currentReq.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status *</label>
                        <select name="status" x-model="currentReq.status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            <option value="OPEN">OPEN (Pending Investigation)</option>
                            <option value="IN_PROGRESS">IN_PROGRESS (Sourcing from Department)</option>
                            <option value="FULFILLED">FULFILLED (Uploaded to Repository)</option>
                            <option value="CLOSED">CLOSED (Unavailable / Outdated Syllabus)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Staff Note to Student</label>
                        <textarea name="admin_notes" rows="3" x-model="currentReq.admin_notes" placeholder="e.g. Slides for Week 4 have been uploaded under BNF 211!" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" @click="updateModalOpen = false" class="px-3.5 py-2 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white rounded-xl text-xs font-bold">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
