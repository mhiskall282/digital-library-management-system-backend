@extends('layouts.admin')

@section('title', 'Upload Material')
@section('page_title', 'Upload Course Material')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Upload Lecture Slides or Examination Paper</h1>
            <p class="text-xs text-slate-500 mt-0.5">Uploaded files are immediately indexed and made available to authorized students.</p>
        </div>
        <a href="{{ route('admin.resources.index') }}" class="text-xs font-bold text-slate-600 hover:text-uew-scarlet">
            &larr; Back to Directory
        </a>
    </div>

    <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data" class="space-y-6 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs" x-data="{ fileName: '', fileSize: '' }">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Document Title *
            </label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" required
                   placeholder="e.g. BBA 111 Principles of Management - Week 1 Slides"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Description / Syllabus Coverage
            </label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Summary of lecture concepts, questions covered, lecturer notes..."
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Resource Type *
                </label>
                <select id="type" name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="SLIDE" {{ old('type') === 'SLIDE' ? 'selected' : '' }}>Lecture Slide Deck</option>
                    <option value="PAST_QUESTION" {{ old('type') === 'PAST_QUESTION' ? 'selected' : '' }}>Past Examination Paper</option>
                </select>
            </div>

            <!-- Course Category -->
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Course Code / Subject *
                </label>
                <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="">Select course...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->course_code }} &mdash; {{ $cat->course_name }} ({{ $cat->level }})
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Academic Level -->
            <div>
                <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Target Academic Level *
                </label>
                <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Syllabus Week -->
            <div>
                <label for="week" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Syllabus Week (1–15)
                </label>
                <select id="week" name="week" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="">General / Exam Paper</option>
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
                       placeholder="e.g. 2023/2024"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
            </div>
        </div>

        <!-- Tags -->
        <div>
            <label for="tags" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Topic Tags (Comma Separated)
            </label>
            <input id="tags" name="tags" type="text" value="{{ old('tags') }}"
                   placeholder="e.g. Management, Taylorism, Classical School"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>

        <!-- File Upload Dropzone -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Attachment File * (PDF, PPT, PPTX, DOC, DOCX, ZIP &middot; Max 100MB)
            </label>
            <div class="border-2 border-dashed border-slate-300 hover:border-uew-scarlet rounded-2xl p-6 text-center transition cursor-pointer bg-slate-50 relative">
                <input type="file" name="file" required 
                       @change="fileName = $event.target.files[0]?.name; fileSize = ($event.target.files[0]?.size / 1048576).toFixed(2) + ' MB'"
                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">

                <div class="space-y-1" x-show="!fileName">
                    <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="text-xs font-bold text-slate-700">
                        <span class="text-uew-scarlet">Click to upload</span> or drag and drop document
                    </div>
                    <p class="text-[11px] text-slate-400">PDF, PowerPoint, Word documents up to 100MB</p>
                </div>

                <div x-show="fileName" class="space-y-1" x-cloak>
                    <span class="text-2xl">📄</span>
                    <span class="block text-xs font-bold text-slate-900" x-text="fileName"></span>
                    <span class="block text-[11px] text-slate-500 font-semibold" x-text="fileSize"></span>
                    <span class="inline-block mt-1 text-[10px] text-uew-scarlet font-bold">Click to choose a different file</span>
                </div>
            </div>
            @error('file')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-uew-navy flex items-center space-x-2">
            <svg class="w-4 h-4 text-uew-navy shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <span>All students registered at this level will receive an automated notification alert once uploaded.</span>
        </div>

        <div class="pt-2 flex items-center space-x-3">
            <button type="submit" class="px-6 py-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                Publish & Index Material
            </button>
            <a href="{{ route('admin.resources.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
