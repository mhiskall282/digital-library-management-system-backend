@extends('layouts.app')

@section('title', $resource->title)

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb & Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-600 hover:text-uew-scarlet transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Catalog</span>
        </a>

        <div class="flex items-center space-x-2">
            <!-- Bookmark Toggle -->
            <form method="POST" action="{{ route('resources.bookmark.toggle', $resource) }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold transition {{ $isBookmarked ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 {{ $isBookmarked ? 'text-uew-amber fill-current' : 'text-slate-400' }}" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                    </svg>
                    <span>{{ $isBookmarked ? 'Saved to Bookmarks' : 'Bookmark Resource' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Resource Hero Details -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <!-- Top Category & Badges -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $resource->type === 'SLIDE' ? 'bg-blue-50 text-uew-navy border border-blue-200' : 'bg-red-50 text-uew-scarlet border border-red-200' }}">
                {{ $resource->type === 'SLIDE' ? 'Lecture Slide' : 'Past Examination Paper' }}
            </span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                Academic Level: {{ $resource->level }}
            </span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                Year: {{ $resource->academic_year }}
            </span>
            @if($resource->category)
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-uew-navy">
                    {{ $resource->category->semester }} Semester
                </span>
            @endif
        </div>

        <!-- Title -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-uew-navy mb-1">
                {{ $resource->category->course_code ?? 'UEW' }} &mdash; {{ $resource->category->course_name ?? 'Business Studies' }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                {{ $resource->title }}
            </h1>
        </div>

        <!-- Description -->
        @if($resource->description)
            <div class="prose prose-slate max-w-none text-sm text-slate-600 leading-relaxed bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                {{ $resource->description }}
            </div>
        @endif

        <!-- Tags -->
        @if(!empty($resource->tags))
            <div class="flex items-center flex-wrap gap-1.5 pt-2">
                <span class="text-xs font-bold text-slate-400 uppercase mr-1">Tags:</span>
                @foreach($resource->tags as $tag)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                        #{{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- File Download & Action Box -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-uew-navy text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-lg shadow-slate-900/10">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center font-black text-sm uppercase text-amber-300 border border-white/15">
                    {{ $resource->extension }}
                </div>
                <div>
                    <span class="block text-sm font-bold text-white truncate max-w-xs sm:max-w-md">{{ $resource->file_name }}</span>
                    <span class="block text-xs text-slate-300 font-medium mt-0.5">
                        {{ $resource->formatted_size }} &middot; {{ number_format($resource->downloads) }} verified downloads
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <a href="{{ route('resources.preview', $resource) }}" target="_blank"
                   class="flex-1 sm:flex-none text-center px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition">
                    Preview in Tab
                </a>

                @if(!$requiresApproval || (auth()->user() && !auth()->user()->isStudent()) || ($userDownloadRequest && $userDownloadRequest->isApproved()))
                    <a href="{{ route('resources.download', $resource) }}" 
                       class="flex-1 sm:flex-none text-center px-6 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs shadow-md shadow-red-700/30 transition">
                        Download File
                    </a>
                @elseif($userDownloadRequest && $userDownloadRequest->isPending())
                    <span class="px-4 py-2.5 rounded-xl bg-amber-500/20 text-amber-200 border border-amber-400/30 text-xs font-bold">
                        ⏳ Request Pending Approval
                    </span>
                @else
                    <button type="button" onclick="document.getElementById('download-request-box').scrollIntoView({behavior: 'smooth'})"
                            class="flex-1 sm:flex-none text-center px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md transition">
                        Request Download Access &rarr;
                    </button>
                @endif
            </div>
        </div>

        @if($requiresApproval && auth()->user() && auth()->user()->isStudent() && (!$userDownloadRequest || !$userDownloadRequest->isApproved()))
            <div id="download-request-box" class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-3">
                <div class="flex items-center space-x-2 text-amber-900 font-bold text-xs">
                    <span>🔒 Intellectual Property Protection Notice:</span>
                </div>
                <p class="text-xs text-amber-800 leading-relaxed">
                    This department archive requires academic download authorization. Please submit a brief study justification (e.g. course assignment, mid-semester exam revision) for librarian review.
                </p>

                <form method="POST" action="{{ route('resources.request-download', $resource) }}" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="text" name="reason" required 
                           placeholder="State your academic purpose (e.g. Revision for BNF 211 Mid-Semester Exam)..." 
                           class="flex-1 px-3.5 py-2 rounded-xl border border-amber-300 text-xs bg-white focus:ring-2 focus:ring-uew-scarlet">
                    <button type="submit" class="px-5 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Submit Download Request &rarr;
                    </button>
                </form>
            </div>
        @endif

        @if(!$hasAccess)
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center space-x-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Notice:</strong> This material belongs to {{ $resource->level }}. You are currently registered as {{ auth()->user()->level }}.</span>
            </div>
        @endif
    </div>

    <!-- Reviews and Ratings Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Student Ratings & Reviews</h2>
                <p class="text-xs text-slate-500 mt-0.5">Feedback from students enrolled in {{ $resource->category->course_code ?? 'this course' }}.</p>
            </div>

            <!-- Score Badge -->
            <div class="flex items-center space-x-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-200">
                <div class="text-3xl font-black text-slate-900 leading-none">
                    {{ number_format($resource->average_rating, 1) }}
                </div>
                <div>
                    <div class="flex text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($resource->average_rating))
                                ★
                            @else
                                <span class="text-slate-300">★</span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500">{{ $resource->total_reviews }} total reviews</span>
                </div>
            </div>
        </div>

        <!-- Interactive Review Submission Box (Alpine.js) -->
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80" x-data="{ rating: {{ $userReview ? $userReview->rating : 5 }}, hoverRating: 0 }">
            <h3 class="text-sm font-bold text-slate-900 mb-1">
                {{ $userReview ? 'Update Your Review' : 'Rate & Review this Material' }}
            </h3>
            <p class="text-xs text-slate-500 mb-4">Help fellow students know if these lecture slides or questions are helpful for revision.</p>

            <form method="POST" action="{{ route('resources.reviews.store', $resource) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="rating" :value="rating">

                <!-- Interactive Stars -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Rating</label>
                    <div class="flex items-center space-x-1">
                        <template x-for="star in [1, 2, 3, 4, 5]">
                            <button type="button" 
                                    @click="rating = star"
                                    @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="text-2xl transition-transform hover:scale-110 focus:outline-none"
                                    :class="(hoverRating || rating) >= star ? 'text-amber-400' : 'text-slate-300'">
                                ★
                            </button>
                        </template>
                        <span class="text-xs font-bold text-slate-700 ml-2" x-text="rating + ' of 5 Stars'"></span>
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Your Feedback / Advice for Classmates</label>
                    <textarea id="comment" name="comment" rows="3" 
                              placeholder="Share key topics, clarity of slides, or specific exam questions covered..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet bg-white">{{ old('comment', $userReview?->comment) }}</textarea>
                </div>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold shadow-xs transition">
                    {{ $userReview ? 'Save Updated Review' : 'Submit Review' }}
                </button>
            </form>
        </div>

        <!-- Student Reviews Feed -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Recent Student Reviews</h3>

            @forelse($resource->reviews->sortByDesc('created_at') as $review)
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                                {{ strtoupper(substr($review->user->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">{{ $review->user->name }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $review->user->level }} &middot; {{ $review->user->program }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        <span class="text-slate-200">★</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if($review->comment)
                        <p class="text-xs text-slate-600 leading-relaxed pt-1">
                            {{ $review->comment }}
                        </p>
                    @endif

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 text-xs">
                        <form method="POST" action="{{ route('reviews.helpful', $review) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center space-x-1 text-[11px] font-semibold text-slate-500 hover:text-uew-navy transition">
                                <span>👍 Helpful</span>
                                @if($review->helpful_count > 0)
                                    <span class="bg-slate-100 px-1.5 py-0.2 rounded text-[10px]">{{ $review->helpful_count }}</span>
                                @endif
                            </button>
                        </form>

                        @if(auth()->id() === $review->user_id || (auth()->user() && auth()->user()->isAdmin()))
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Remove your review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] font-semibold text-red-500 hover:text-red-700">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic py-4 text-center">No reviews yet. Be the first to share feedback on this resource!</p>
            @endforelse
        </div>
    </div>

    <!-- Related Materials Card Deck -->
    @if($relatedResources->count() > 0)
        <div class="space-y-3 pt-4">
            <h3 class="text-base font-bold text-slate-900">More Resources from this Academic Stream</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($relatedResources as $rel)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-uew-scarlet/50 shadow-xs transition space-y-2 flex flex-col justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                {{ $rel->type === 'SLIDE' ? 'Slide' : 'Exam Paper' }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-2 mt-1.5 leading-snug">
                                <a href="{{ route('resources.show', $rel) }}" class="hover:text-uew-scarlet">
                                    {{ $rel->title }}
                                </a>
                            </h4>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                            <span>★ {{ number_format($rel->average_rating, 1) }}</span>
                            <a href="{{ route('resources.download', $rel) }}" class="font-bold text-uew-scarlet hover:underline">Download</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
