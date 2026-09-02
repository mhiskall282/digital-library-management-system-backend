@extends('layouts.app')

@section('title', 'Request Study Material')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-gradient-to-r from-uew-navy-dark via-slate-900 to-uew-navy p-6 sm:p-8 rounded-3xl text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white text-[10px] font-bold uppercase tracking-wider">
                Support & Communication Desk
            </span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                Request Missing Slides or Past Exam Papers
            </h1>
            <p class="text-xs text-slate-300">
                Can't find lecture slides or past questions for your course? Submit a ticket and our library team will source it.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Submit Request Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Submit Material Request
            </h2>

            <form method="POST" action="{{ route('requests.store') }}" class="space-y-3.5">
                @csrf

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Course Code *</label>
                        <input type="text" name="course_code" required placeholder="e.g. BNF 211" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Type *</label>
                        <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            <option value="SLIDE">Lecture Slides</option>
                            <option value="PAST_QUESTION">Past Exam Paper</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Course Title *</label>
                    <input type="text" name="course_name" required placeholder="e.g. Banking Operations and Practice" 
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Academic Level *</label>
                        <select name="level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                                <option value="{{ $lvl }}" {{ auth()->user()->level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Urgency *</label>
                        <select name="urgency" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            <option value="LOW">Low (General Revision)</option>
                            <option value="MEDIUM" selected>Medium (Upcoming Quiz)</option>
                            <option value="HIGH">High (Immediate Exam)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Program *</label>
                    <input type="text" name="program" value="{{ auth()->user()->program }}" required 
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Topic / Exam Year Needed *</label>
                    <textarea name="topic" rows="2" required placeholder="e.g. Need Week 4 Liquidity Risk slides or 2022/2023 End of Semester exam paper..." 
                              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Log Material Request &rarr;
                </button>
            </form>
        </div>

        <!-- Student's Existing Requests Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Your Request Status History
            </h2>

            <div class="divide-y divide-slate-100">
                @forelse($myRequests as $req)
                    <div class="py-3.5 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-black text-uew-navy">{{ $req->course_code }}</span>
                                <span class="text-slate-700 font-semibold">&middot; {{ $req->course_name }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ $req->urgency === 'HIGH' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $req->urgency }}
                                </span>
                            </div>
                            <p class="text-slate-600 text-[11px] leading-relaxed">
                                <strong>Request:</strong> {{ $req->topic }}
                            </p>
                            @if($req->admin_notes)
                                <p class="text-[10px] text-blue-700 bg-blue-50/70 p-1.5 rounded-lg border border-blue-100">
                                    💬 <strong>Librarian Response:</strong> {{ $req->admin_notes }}
                                </p>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $req->status === 'FULFILLED' ? 'bg-emerald-100 text-emerald-800' : ($req->status === 'IN_PROGRESS' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $req->status }}
                            </span>
                            <span class="block text-[10px] text-slate-400 mt-1">{{ $req->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-8 text-center">
                        You have not submitted any material requests yet.
                    </p>
                @endforelse
            </div>

            <div class="pt-2">
                {{ $myRequests->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
