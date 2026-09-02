@extends('layouts.admin')

@section('title', 'Document Moderation Desk')
@section('page_title', 'Student Document Moderation')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, currentResource: {} }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Submissions Moderation Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review study materials submitted by scholars before publishing to the library catalog.</p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 text-xs font-bold">
                {{ $pendingCount }} Pending Review
            </span>
        </div>
    </div>

    <!-- Table of Submissions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Document Details</th>
                        <th class="px-4 py-3.5">Contributor</th>
                        <th class="px-4 py-3.5">Course & Level</th>
                        <th class="px-4 py-3.5">Format & Size</th>
                        <th class="px-4 py-3.5">Submitted</th>
                        <th class="px-5 py-3.5 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingResources as $res)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="font-bold text-slate-900 block truncate">{{ $res->title }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ $res->file_name }}</span>
                                @if($res->description)
                                    <p class="text-[10px] text-slate-500 italic truncate mt-0.5">{{ $res->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">{{ $res->uploader->name ?? 'Student' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $res->uploader->student_id ?? '' }} &middot; {{ $res->uploader->program ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-uew-navy block">{{ $res->category->course_code ?? 'None' }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-600">{{ $res->level }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $res->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy' : 'bg-red-50 text-uew-scarlet' }}">
                                    {{ $res->type === 'SLIDE' ? 'Slide' : 'Exam' }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $res->formatted_size }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-500 text-[11px]">
                                {{ $res->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('resources.preview', $res) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-bold text-[10px] hover:bg-slate-200">
                                    Preview
                                </a>

                                <form method="POST" action="{{ route('admin.moderation.approve', $res) }}" class="inline-block" onsubmit="return confirm('Approve this document and award 50 points to contributor?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow-2xs">
                                        ✓ Approve (+50 Pts)
                                    </button>
                                </form>

                                <button type="button" 
                                        @click="currentResource = @js($res); rejectModalOpen = true"
                                        class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold text-[10px]">
                                    ✕ Reject
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">
                                <span class="text-2xl block mb-1">🎉</span>
                                All caught up! No student submissions currently pending moderation.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $pendingResources->links() }}
        </div>
    </div>

    <!-- Reject Feedback Modal -->
    <div x-show="rejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="rejectModalOpen = false"></div>
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Provide Rejection Feedback</h3>
                    <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/moderation') }}/' + currentResource.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <span class="block text-xs font-bold text-slate-700 mb-1" x-text="currentResource.title"></span>
                        <p class="text-[11px] text-slate-500 mb-3">Explain to the student why this document was not published (e.g. illegible scan, duplicate slide deck, incorrect syllabus course code):</p>
                        <textarea name="reason" rows="3" required placeholder="State feedback for the student..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" @click="rejectModalOpen = false" class="px-3.5 py-2 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold">Reject Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
