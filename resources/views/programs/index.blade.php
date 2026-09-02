@extends('layouts.app')

@section('title', 'Academic Programs & Level Directory')

@section('content')
<div class="space-y-6" x-data="{ selectedProgram: Object.keys(@js($programsCatalog))[0] || '', selectedLevel: 'L100' }">

    <!-- Header Banner -->
    <div class="p-6 sm:p-10 rounded-3xl text-white shadow-lg relative overflow-hidden"
         style="background-image: linear-gradient(to right, rgba(15, 23, 42, 0.92), rgba(30, 58, 138, 0.88), rgba(15, 23, 42, 0.85)), url('{{ asset('images/collaboration.jpg') }}'); background-size: cover; background-position: center;">
        <div class="max-w-3xl space-y-2 relative z-10">
            <span class="px-2.5 py-1 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-xs">
                Curriculum Structure & Stream Mapping
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Academic Programs & Level Directory
            </h1>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                Explore course notes, syllabus materials, and past exam archives organized hierarchically by degree program and academic level from Level 100 to PhD.
            </p>
        </div>
    </div>

    <!-- Multi-Program Tabs Bar -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-2 border-b border-slate-200 text-xs font-bold">
        @foreach($programsCatalog as $programName => $progData)
            <button type="button" 
                    @click="selectedProgram = '{{ $programName }}'; selectedLevel = 'L100'"
                    :class="selectedProgram === '{{ $programName }}' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2.5 rounded-xl whitespace-nowrap transition flex items-center space-x-2">
                <span>{{ $programName }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-black/20 text-white">
                    {{ $progData['total_resources'] }}
                </span>
            </button>
        @endforeach
    </div>

    <!-- Active Program Display Area -->
    @foreach($programsCatalog as $programName => $progData)
        <div x-show="selectedProgram === '{{ $programName }}'" class="space-y-6" x-cloak>

            <!-- Level Navigation Pills (L100 -> PHD) -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center space-x-1.5 overflow-x-auto text-xs font-bold">
                    <span class="text-slate-400 uppercase text-[10px] mr-2">Select Level:</span>
                    @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                        @php
                            $coursesAtLevel = $progData['levels'][$lvl] ?? [];
                            $materialsCount = collect($coursesAtLevel)->sum(fn($c) => $c->resources->count());
                        @endphp
                        <button type="button" 
                                @click="selectedLevel = '{{ $lvl }}'"
                                :class="selectedLevel === '{{ $lvl }}' ? 'bg-uew-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition flex items-center space-x-1.5">
                            <span>{{ $lvl }}</span>
                            @if($materialsCount > 0)
                                <span class="text-[10px] opacity-80">({{ $materialsCount }})</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <span class="text-xs text-slate-500 font-semibold">
                    Program: <strong class="text-slate-900">{{ $programName }}</strong>
                </span>
            </div>

            <!-- Courses & Materials for Selected Level -->
            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                <div x-show="selectedLevel === '{{ $lvl }}'" class="space-y-4" x-cloak>
                    @php
                        $coursesAtLevel = $progData['levels'][$lvl] ?? [];
                    @endphp

                    @if(count($coursesAtLevel) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($coursesAtLevel as $course)
                                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-sm text-uew-navy px-2.5 py-0.5 rounded-lg bg-blue-50 border border-blue-100">
                                                {{ $course->course_code }}
                                            </span>
                                            <span class="text-[11px] font-bold text-slate-500">
                                                {{ $course->semester }} Semester
                                            </span>
                                        </div>

                                        <h3 class="text-base font-bold text-slate-900 leading-snug">
                                            {{ $course->course_name }}
                                        </h3>

                                        @if($course->description)
                                            <p class="text-xs text-slate-500 line-clamp-2">{{ $course->description }}</p>
                                        @endif
                                    </div>

                                    <!-- Attached Materials List -->
                                    <div class="pt-3 border-t border-slate-100 space-y-2">
                                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                            <span>Repository Materials ({{ $course->resources->count() }})</span>
                                            <a href="{{ route('dashboard', ['category_id' => $course->id]) }}" class="text-uew-scarlet hover:underline text-[11px]">
                                                View in Catalog &rarr;
                                            </a>
                                        </div>

                                        @forelse($course->resources as $res)
                                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between text-xs gap-2">
                                                <div class="truncate space-y-0.5">
                                                    <a href="{{ route('resources.show', $res) }}" class="font-bold text-slate-800 hover:text-uew-scarlet truncate block">
                                                        {{ $res->title }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-400 block font-medium">
                                                        {{ $res->type === 'SLIDE' ? 'Lecture Slide' : 'Past Exam' }} &middot; {{ $res->formatted_size }} &middot; {{ $res->downloads }} dl
                                                    </span>
                                                </div>
                                                <a href="{{ route('resources.download', $res) }}" class="px-2.5 py-1 rounded-lg bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-[11px] shrink-0 shadow-xs">
                                                    Download
                                                </a>
                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-400 italic py-2 text-center bg-slate-50 rounded-xl">
                                                No materials published yet for this course. 
                                                <a href="{{ route('requests.index') }}" class="text-uew-navy font-bold hover:underline">Request slides &rarr;</a>
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
                            <span class="text-3xl">📚</span>
                            <h3 class="text-base font-bold text-slate-800">No courses listed at {{ $lvl }}</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                There are no courses registered under {{ $lvl }} for {{ $programName }}.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
    @endforeach

</div>
@endsection
