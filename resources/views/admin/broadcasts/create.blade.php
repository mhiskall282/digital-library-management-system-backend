@extends('layouts.admin')

@section('title', 'Transmit Broadcast Announcement')
@section('page_title', 'Broadcast Communication Desk')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{ targetType: 'ALL' }">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Compose Departmental Broadcast</h1>
            <p class="text-xs text-slate-500 mt-0.5">Send targeted announcements to scholars directly via in-app alerts and official emails.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-uew-scarlet">
            &larr; Back to Dashboard
        </a>
    </div>

    <form method="POST" action="{{ route('admin.broadcasts.store') }}" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-5">
        @csrf

        <!-- Target Audience Selector -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Target Audience Cohort *
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                       :class="targetType === 'ALL' ? 'bg-red-50 border-uew-scarlet text-uew-scarlet font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                    <input type="radio" name="target_type" value="ALL" x-model="targetType" class="text-uew-scarlet focus:ring-uew-scarlet">
                    <span class="text-xs">All Enrolled Students</span>
                </label>

                <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                       :class="targetType === 'LEVEL' ? 'bg-red-50 border-uew-scarlet text-uew-scarlet font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                    <input type="radio" name="target_type" value="LEVEL" x-model="targetType" class="text-uew-scarlet focus:ring-uew-scarlet">
                    <span class="text-xs">Filter by Level</span>
                </label>

                <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                       :class="targetType === 'PROGRAM' ? 'bg-red-50 border-uew-scarlet text-uew-scarlet font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                    <input type="radio" name="target_type" value="PROGRAM" x-model="targetType" class="text-uew-scarlet focus:ring-uew-scarlet">
                    <span class="text-xs">Filter by Program</span>
                </label>
            </div>
        </div>

        <!-- Level Specific Target -->
        <div x-show="targetType === 'LEVEL'" class="space-y-1" x-cloak>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Select Academic Level *</label>
            <select name="target_level" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                    <option value="{{ $lvl }}">{{ $lvl }} Cohort</option>
                @endforeach
            </select>
        </div>

        <!-- Program Specific Target -->
        <div x-show="targetType === 'PROGRAM'" class="space-y-1" x-cloak>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Select Degree Program *</label>
            <select name="target_program" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                @foreach($programs as $prog)
                    <option value="{{ $prog }}">{{ $prog }}</option>
                @endforeach
            </select>
        </div>

        <!-- Title -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Announcement Headline *
            </label>
            <input type="text" name="title" required placeholder="e.g. End of Semester Examination Past Papers Uploaded for L300" 
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>

        <!-- Message -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Broadcast Content & Instructions *
            </label>
            <textarea name="message" rows="5" required placeholder="Provide comprehensive announcement details, syllabus links, or library operational updates..." 
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet"></textarea>
        </div>

        <!-- Delivery Channels -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <span class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Dispatch Channels</span>
            <div class="flex items-center space-x-6 text-xs text-slate-700">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" checked disabled class="w-4 h-4 text-uew-scarlet rounded">
                    <span>In-App Dashboard Notification (Instant)</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="send_email" value="1" checked class="w-4 h-4 text-uew-scarlet rounded">
                    <span>Dispatch Email Alert to Student Inbox</span>
                </label>
            </div>
        </div>

        <div class="pt-2 flex items-center space-x-3">
            <button type="submit" class="px-6 py-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                Transmit Broadcast &rarr;
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
