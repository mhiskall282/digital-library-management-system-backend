@extends('layouts.admin')

@section('title', 'Bulk Student Import')
@section('page_title', 'Bulk Student Onboarding & CSV Import')

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Bulk Student Account Ingestion</h1>
            <p class="text-xs text-slate-500 mt-0.5">Upload official class rosters to automatically create student accounts and dispatch activation invites.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-600 hover:text-uew-scarlet">
            &larr; Back to Directory
        </a>
    </div>

    <!-- Download Sample Template Box -->
    <div class="p-5 rounded-3xl bg-blue-50/70 border border-blue-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-uew-navy">
        <div class="space-y-1">
            <span class="font-bold block text-sm">Download Official CSV Template</span>
            <p class="text-slate-600 leading-relaxed text-[11px]">
                Ensure your Excel or CSV file conforms to required column headers: <code>student_id, first_name, last_name, email, level, program, department</code>.
            </p>
        </div>
        <a href="{{ route('admin.users.import.sample') }}" class="px-4 py-2 bg-uew-navy hover:bg-uew-navy-hover text-white text-xs font-bold rounded-xl shadow-xs transition shrink-0 flex items-center space-x-1.5">
            <span>📥 Download Template</span>
        </a>
    </div>

    <!-- Upload Form -->
    <form method="POST" action="{{ route('admin.users.import.store') }}" enctype="multipart/form-data" 
          class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-5"
          x-data="{ fileName: '' }">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Select CSV Roster File *
            </label>
            <div class="border-2 border-dashed border-slate-300 hover:border-uew-scarlet rounded-2xl p-8 text-center transition cursor-pointer bg-slate-50 relative">
                <input type="file" name="csv_file" required accept=".csv,.txt"
                       @change="fileName = $event.target.files[0]?.name"
                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">

                <div class="space-y-1.5" x-show="!fileName">
                    <span class="text-3xl">📑</span>
                    <div class="text-xs font-bold text-slate-700">
                        <span class="text-uew-scarlet">Click to browse file</span> or drop CSV here
                    </div>
                    <p class="text-[11px] text-slate-400">Comma-separated values (.csv) up to 5MB</p>
                </div>

                <div x-show="fileName" class="space-y-1" x-cloak>
                    <span class="text-3xl">✅</span>
                    <span class="block text-xs font-bold text-slate-900" x-text="fileName"></span>
                    <span class="inline-block text-[10px] text-uew-scarlet font-bold">Click to replace file</span>
                </div>
            </div>
            @error('csv_file')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Automated Actions Explainer -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
            <span class="font-bold text-slate-800 block">Automated Onboarding Sequence:</span>
            <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600">
                <li>Each student account is created with their official institutional index number and degree stream.</li>
                <li>A cryptographically randomized temporary password is assigned.</li>
                <li>An automated <strong>Welcome & Activation Email</strong> is dispatched with their initial sign-in credentials.</li>
                <li>Upon first login, students are guided through the Scholar Onboarding form to verify details and set their permanent password.</li>
            </ul>
        </div>

        <div class="pt-2 flex items-center space-x-3">
            <button type="submit" class="px-6 py-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                Start Batch Ingestion &rarr;
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
