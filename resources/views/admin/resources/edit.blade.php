@extends('layouts.admin')

@section('title', 'Edit ' . $resource->title)
@section('page_title', 'Edit Material Metadata')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Resource Metadata</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update title, syllabus coverage, course mapping, or academic year.</p>
        </div>
        <a href="{{ route('admin.resources.index') }}" class="text-xs font-bold text-slate-600 hover:text-uew-scarlet">
            &larr; Back to Directory
        </a>
    </div>

    <form method="POST" action="{{ route('admin.resources.update', $resource) }}" class="space-y-6 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Document Title *
            </label>
            <input id="title" name="title" type="text" value="{{ old('title', $resource->title) }}" required
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Description / Syllabus Coverage
            </label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">{{ old('description', $resource->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Resource Type *
                </label>
                <select id="type" name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <option value="SLIDE" {{ old('type', $resource->type) === 'SLIDE' ? 'selected' : '' }}>Lecture Slide Deck</option>
                    <option value="PAST_QUESTION" {{ old('type', $resource->type) === 'PAST_QUESTION' ? 'selected' : '' }}>Past Examination Paper</option>
                </select>
            </div>

            <!-- Course Category -->
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Course Code / Subject *
                </label>
                <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $resource->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->course_code }} &mdash; {{ $cat->course_name }} ({{ $cat->level }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Academic Level -->
            <div>
                <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Target Academic Level *
                </label>
                <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                    @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('level', $resource->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label for="academic_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Academic Year *
                </label>
                <input id="academic_year" name="academic_year" type="text" value="{{ old('academic_year', $resource->academic_year) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
            </div>
        </div>

        <!-- Tags -->
        <div>
            <label for="tags" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Topic Tags (Comma Separated)
            </label>
            <input id="tags" name="tags" type="text" value="{{ old('tags', is_array($resource->tags) ? implode(', ', $resource->tags) : '') }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
            Current attachment: <strong>{{ $resource->file_name }}</strong> ({{ $resource->formatted_size }} &middot; {{ $resource->downloads }} downloads)
        </div>

        <div class="pt-2 flex items-center space-x-3">
            <button type="submit" class="px-6 py-3 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                Save Changes
            </button>
            <a href="{{ route('admin.resources.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
