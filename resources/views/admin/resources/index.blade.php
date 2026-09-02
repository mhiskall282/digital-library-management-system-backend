@extends('layouts.admin')

@section('title', 'Manage Resources')
@section('page_title', 'Course Study Materials & Archives')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Study Materials Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage, review, or archive course lecture slides and past examination papers.</p>
        </div>

        <a href="{{ route('admin.resources.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Upload New Material</span>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.resources.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by title..." 
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
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                    <option value="">All Formats</option>
                    <option value="SLIDE" {{ $type === 'SLIDE' ? 'selected' : '' }}>Lecture Slides</option>
                    <option value="PAST_QUESTION" {{ $type === 'PAST_QUESTION' ? 'selected' : '' }}>Past Examinations</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                    Filter
                </button>
                @if($search || $level || $type || $categoryId)
                    <a href="{{ route('admin.resources.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 font-semibold text-xs rounded-lg hover:bg-slate-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Resource Details</th>
                        <th class="px-4 py-3.5">Course & Level</th>
                        <th class="px-4 py-3.5">Type</th>
                        <th class="px-4 py-3.5">Size & Stats</th>
                        <th class="px-4 py-3.5">Rating</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($resources as $res)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 max-w-xs">
                                <a href="{{ route('resources.show', $res) }}" class="font-bold text-slate-900 hover:text-uew-scarlet block truncate leading-tight">
                                    {{ $res->title }}
                                </a>
                                <span class="text-[10px] text-slate-400 block truncate mt-0.5">{{ $res->file_name }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-uew-navy block">{{ $res->category->course_code ?? 'None' }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-600">{{ $res->level }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $res->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy' : 'bg-red-50 text-uew-scarlet' }}">
                                    {{ $res->type === 'SLIDE' ? 'Slide' : 'Exam' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-600">
                                <span class="block">{{ $res->formatted_size }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $res->downloads }} downloads</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-amber-500">★ {{ number_format($res->average_rating, 1) }}</span>
                                <span class="text-[10px] text-slate-400">({{ $res->total_reviews }})</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.resources.edit', $res) }}" class="text-xs font-bold text-slate-600 hover:text-uew-scarlet">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.resources.destroy', $res) }}" class="inline-block" onsubmit="return confirm('Permanently delete this material from the repository?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 italic">
                                No study materials found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $resources->links() }}
        </div>
    </div>

</div>
@endsection
