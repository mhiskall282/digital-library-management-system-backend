@extends('layouts.guest')

@section('title', 'Complete Scholar Onboarding')
@section('header', 'Complete Your Scholar Profile')
@section('subheader', 'Confirm your degree stream, academic level, and personal learning preferences.')

@section('content')
<form method="POST" action="{{ route('onboarding.update') }}" class="space-y-4">
    @csrf

    <!-- Student Details Preview -->
    <div class="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs text-uew-navy flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-uew-navy text-white font-black flex items-center justify-center text-sm shrink-0">
            {{ strtoupper(substr($user->first_name, 0, 1)) }}
        </div>
        <div>
            <span class="block font-bold">{{ $user->name }}</span>
            <span class="block text-[11px] text-slate-500">{{ $user->email }} &middot; Index: {{ $user->student_id ?: 'Assigned' }}</span>
        </div>
    </div>

    <!-- Degree Program -->
    <div>
        <label for="program" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Enrolled Degree Program *
        </label>
        <select id="program" name="program" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
            @foreach($programs as $p)
                <option value="{{ $p }}" {{ old('program', $user->program) === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
        @error('program')
            <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <!-- Academic Level -->
    <div>
        <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Current Academic Level *
        </label>
        <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                <option value="{{ $lvl }}" {{ old('level', $user->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
            @endforeach
        </select>
    </div>

    <!-- Phone Number (Optional) -->
    <div>
        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Mobile Number (Optional &middot; for SMS/Alerts)
        </label>
        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +233 24 000 0000"
               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
    </div>

    <!-- Bio / Academic Interests -->
    <div>
        <label for="bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Academic Focus & Specialization
        </label>
        <textarea id="bio" name="bio" rows="2" placeholder="e.g. Focused on Corporate Finance, Auditing, and Data Analytics..."
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">{{ old('bio', $user->bio) }}</textarea>
    </div>

    <!-- Optional New Password Setup -->
    <div class="pt-3 border-t border-slate-100 space-y-3">
        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Set Permanent Password (Optional)</span>
        <div>
            <input name="password" type="password" placeholder="New Password (leave empty to keep current)"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>
        <div>
            <input name="password_confirmation" type="password" placeholder="Confirm New Password"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
        </div>
    </div>

    <div class="pt-2">
        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs shadow-md shadow-red-700/20 transition">
            Complete Onboarding (+25 Points Bonus) &rarr;
        </button>
    </div>
</form>
@endsection
