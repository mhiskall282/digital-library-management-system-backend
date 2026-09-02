@extends('layouts.admin')

@section('title', 'Course Categories')
@section('page_title', 'Course Categories & Subject Directory')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, editCategory: {} }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">School of Business Course Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Organize lecture slides and examinations by course code, academic level, and semester.</p>
        </div>

        <button @click="createModalOpen = true" type="button" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add Course Category</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search code or title..." 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
            </div>

            <div>
                <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                    <option value="">All Academic Levels</option>
                    @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                        <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                    <option value="">All Semesters</option>
                    <option value="FIRST" {{ $semester === 'FIRST' ? 'selected' : '' }}>First Semester</option>
                    <option value="SECOND" {{ $semester === 'SECOND' ? 'selected' : '' }}>Second Semester</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                    Filter
                </button>
                @if($search || $level || $semester)
                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 font-semibold text-xs rounded-lg hover:bg-slate-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Categories -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Course Code</th>
                        <th class="px-4 py-3.5">Course Title</th>
                        <th class="px-4 py-3.5">Level</th>
                        <th class="px-4 py-3.5">Semester</th>
                        <th class="px-4 py-3.5">Materials</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap font-black text-uew-navy text-sm">
                                {{ $cat->course_code }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-800 block">{{ $cat->course_name }}</span>
                                @if($cat->description)
                                    <span class="text-[10px] text-slate-400 block truncate max-w-sm">{{ $cat->description }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ $cat->level }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cat->semester === 'FIRST' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-uew-navy' }}">
                                    {{ $cat->semester }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-bold text-slate-700">
                                {{ $cat->resources_count }} files
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                <button type="button" 
                                        @click="editCategory = @js($cat); editModalOpen = true" 
                                        class="text-xs font-bold text-uew-navy hover:underline">
                                    Edit
                                </button>
                                @if($cat->resources_count === 0)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline-block" onsubmit="return confirm('Delete course category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 italic">
                                No course categories registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Create Category Modal (Alpine.js) -->
    <div x-show="createModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="createModalOpen = false"></div>
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Add New Course Category</h3>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Code *</label>
                            <input type="text" name="course_code" required placeholder="e.g. BBA 111" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Semester *</label>
                            <select name="semester" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                                <option value="FIRST">First Semester</option>
                                <option value="SECOND">Second Semester</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Title *</label>
                        <input type="text" name="course_name" required placeholder="e.g. Principles of Management" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Academic Level *</label>
                        <select name="level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Syllabus</label>
                        <textarea name="description" rows="2" placeholder="Brief subject summary..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end space-x-2">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white rounded-xl text-xs font-bold">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal (Alpine.js) -->
    <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="editModalOpen = false"></div>
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Edit Course Category</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/categories') }}/' + editCategory.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Code *</label>
                            <input type="text" name="course_code" required x-model="editCategory.course_code" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Semester *</label>
                            <select name="semester" required x-model="editCategory.semester" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                                <option value="FIRST">First Semester</option>
                                <option value="SECOND">Second Semester</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Title *</label>
                        <input type="text" name="course_name" required x-model="editCategory.course_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Academic Level *</label>
                        <select name="level" required x-model="editCategory.level" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Syllabus</label>
                        <textarea name="description" rows="2" x-model="editCategory.description" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end space-x-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white rounded-xl text-xs font-bold">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
