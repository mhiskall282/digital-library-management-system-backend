@extends('layouts.app')

@section('title', 'Contribute Study Material')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header & Incentives Banner -->
    <div class="bg-gradient-to-r from-uew-navy-dark via-slate-900 to-uew-navy p-6 rounded-3xl text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 text-[10px] font-bold uppercase tracking-wider">
                🏆 Contributor Rewards Active
            </span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                Submit Verified Study Materials
            </h1>
            <p class="text-xs text-slate-300">
                Help fellow School of Business scholars excel. Earn <strong>+50 Contributor Points</strong> for every approved document!
            </p>
        </div>
        <div class="bg-white/10 px-4 py-2.5 rounded-2xl border border-white/15 text-center shrink-0">
            <span class="block text-xl font-black text-amber-300">{{ $user->contributor_points }} pts</span>
            <span class="block text-[10px] uppercase font-bold text-slate-300">{{ $user->contributor_rank }}</span>
        </div>
    </div>

    <!-- Upload Form Card -->
    <form method="POST" action="{{ route('student.contribute.store') }}" enctype="multipart/form-data" 
          class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5"
          x-data="{ fileName: '', fileSize: '' }">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Document Title *
            </label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" required
                   placeholder="e.g. BNF 211 Banking Operations - Mid-Semester Revision Questions & Answers"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
            @error('title')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Document Format *
                </label>
                <select id="type" name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="SLIDE" {{ old('type') === 'SLIDE' ? 'selected' : '' }}>Lecture Slide Deck</option>
                    <option value="PAST_QUESTION" {{ old('type') === 'PAST_QUESTION' ? 'selected' : '' }}>Past Examination Paper</option>
                </select>
            </div>

            <!-- Course Category -->
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Associated Course *
                </label>
                <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="">Select subject...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->course_code }} &mdash; {{ $cat->course_name }} ({{ $cat->level }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Academic Level -->
            <div>
                <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Academic Level *
                </label>
                <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('level', $user->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Syllabus Week (Week 1-15) -->
            <div>
                <label for="week" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Syllabus Week (1–15)
                </label>
                <select id="week" name="week" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="">General / Past Exam</option>
                    @for($w = 1; $w <= 15; $w++)
                        <option value="{{ $w }}" {{ old('week') == $w ? 'selected' : '' }}>Week {{ $w }} Module</option>
                    @endfor
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label for="academic_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Academic Year *
                </label>
                <input id="academic_year" name="academic_year" type="text" value="{{ old('academic_year', '2023/2024') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Description / Coverage Summary
            </label>
            <textarea id="description" name="description" rows="3"
                      placeholder="List key modules, lecturer names, exam questions covered..."
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">{{ old('description') }}</textarea>
        </div>

        <!-- File Upload Dropzone -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Upload File (PDF, PPT, PPTX, DOC, DOCX, ZIP &middot; Max 100MB) *
            </label>
            <div class="border-2 border-dashed border-slate-300 hover:border-uew-scarlet rounded-2xl p-6 text-center transition cursor-pointer bg-slate-50 relative">
                <input type="file" name="file" required 
                       @change="fileName = $event.target.files[0]?.name; fileSize = ($event.target.files[0]?.size / 1048576).toFixed(2) + ' MB'"
                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">

                <div class="space-y-1" x-show="!fileName">
                    <span class="text-3xl">📤</span>
                    <div class="text-xs font-bold text-slate-700">
                        <span class="text-uew-scarlet">Click to browse file</span> or drag & drop here
                    </div>
                    <p class="text-[11px] text-slate-400">PDF, PowerPoint, Word documents up to 100MB</p>
                </div>

                <div x-show="fileName" class="space-y-1" x-cloak>
                    <span class="text-3xl">📄</span>
                    <span class="block text-xs font-bold text-slate-900" x-text="fileName"></span>
                    <span class="block text-[11px] text-slate-500 font-semibold" x-text="fileSize"></span>
                    <span class="inline-block text-[10px] text-uew-scarlet font-bold">Click to replace file</span>
                </div>
            </div>
            @error('file')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submission Policy Notice -->
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
            <span class="font-bold block">Academic Integrity & Review Policy:</span>
            <p class="text-[11px] leading-relaxed">
                All uploaded materials undergo review by library moderators to ensure clarity, correctness, and syllabus relevance. Once approved, the document is published and 50 points are credited to your scholar profile.
            </p>
        </div>

        <div class="pt-2 flex items-center justify-end space-x-3">
            <a href="{{ route('student.hub') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                Submit for Moderation &rarr;
            </button>
        </div>
    </form>

</div>
@endsection
