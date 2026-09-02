@extends('layouts.app')

@section('title', 'Saved Materials')

@section('content')
<div class="space-y-6">

    <!-- Header & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Saved Academic Materials</h1>
            <p class="text-xs text-slate-500 mt-0.5">Quick access to lecture slides, questions, and personal revision notes.</p>
        </div>

        <form method="GET" action="{{ route('bookmarks.index') }}" class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search saved items..." 
                   class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet">
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('bookmarks.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if($bookmarks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($bookmarks as $bookmark)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4" x-data="{ editingNotes: false, notesText: @js($bookmark->notes) }">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $bookmark->resource->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy' : 'bg-red-50 text-uew-scarlet' }}">
                                    {{ $bookmark->resource->type === 'SLIDE' ? 'Lecture Slide' : 'Past Exam' }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $bookmark->resource->level }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}" onsubmit="return confirm('Remove this material from your bookmarks?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-red-600 transition" title="Remove bookmark">
                                    ✕ Remove
                                </button>
                            </form>
                        </div>

                        <div>
                            <span class="block text-[11px] font-bold text-uew-navy uppercase">
                                {{ $bookmark->resource->category->course_code ?? '' }} &middot; {{ $bookmark->resource->category->course_name ?? '' }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 hover:text-uew-scarlet transition-colors mt-0.5 leading-snug">
                                <a href="{{ route('resources.show', $bookmark->resource) }}">
                                    {{ $bookmark->resource->title }}
                                </a>
                            </h3>
                        </div>

                        <!-- Personal Study Notes Section -->
                        <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 text-xs space-y-1.5">
                            <div class="flex items-center justify-between text-amber-900 font-bold text-[11px]">
                                <span>Personal Study Notes</span>
                                <button type="button" @click="editingNotes = !editingNotes" class="text-uew-scarlet hover:underline font-semibold">
                                    <span x-text="editingNotes ? 'Cancel' : (notesText ? 'Edit Note' : '+ Add Note')"></span>
                                </button>
                            </div>

                            <!-- View Mode -->
                            <div x-show="!editingNotes">
                                <p class="text-slate-700 italic text-[11px] leading-relaxed" x-text="notesText || 'No custom notes added yet. Click edit to jot down important slide numbers, revision reminders, or questions.'"></p>
                            </div>

                            <!-- Edit Form Mode -->
                            <form x-show="editingNotes" method="POST" action="{{ route('bookmarks.update', $bookmark) }}" class="space-y-2" x-cloak>
                                @csrf
                                @method('PUT')
                                <textarea name="notes" rows="2" class="w-full p-2 text-xs rounded-lg border border-amber-300 focus:ring-2 focus:ring-uew-scarlet bg-white" placeholder="Write reminders, formula notes, exam focus..." x-model="notesText"></textarea>
                                <button type="submit" class="px-3 py-1 bg-uew-scarlet text-white font-bold text-[10px] rounded-md shadow-xs">
                                    Save Note
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Footer Details & Download Button -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400">
                            Saved {{ $bookmark->created_at->diffForHumans() }}
                        </span>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('resources.show', $bookmark->resource) }}" class="font-bold text-slate-700 hover:text-uew-scarlet">
                                View
                            </a>
                            <a href="{{ route('resources.download', $bookmark->resource) }}" class="px-3 py-1.5 bg-uew-navy hover:bg-uew-navy-hover text-white font-bold text-xs rounded-lg shadow-xs transition">
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $bookmarks->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-md mx-auto space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-uew-amber flex items-center justify-center mx-auto text-2xl">
                ★
            </div>
            <h3 class="text-base font-bold text-slate-900">No saved resources</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                You haven't bookmarked any lecture slides or exam papers yet. Browse the catalog and click the bookmark button on materials you want to save.
            </p>
            <div class="pt-2">
                <a href="{{ route('dashboard') }}" class="inline-flex px-4 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Browse Resource Catalog
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
