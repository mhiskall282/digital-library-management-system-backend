@extends('layouts.guest')

@section('title', 'Student Registration')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Create Student Account</h2>
    <p class="text-xs text-slate-500 mt-1">Register with your UEW student ID to gain access to academic materials.</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="first_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                First Name
            </label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required
                   placeholder="e.g. Kwame"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('first_name') border-red-500 @enderror">
            @error('first_name')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="last_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Last Name
            </label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required
                   placeholder="e.g. Mensah"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('last_name') border-red-500 @enderror">
            @error('last_name')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="student_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            UEW Student ID (Index No.)
        </label>
        <input id="student_id" name="student_id" type="text" value="{{ old('student_id') }}" required
               placeholder="e.g. 5201040001"
               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('student_id') border-red-500 @enderror">
        @error('student_id')
            <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Institutional Email
        </label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required
               placeholder="e.g. student@st.uew.edu.gh"
               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Academic Level
            </label>
            <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet bg-white">
                <option value="L100" {{ old('level') == 'L100' ? 'selected' : '' }}>Level 100</option>
                <option value="L200" {{ old('level') == 'L200' ? 'selected' : '' }}>Level 200</option>
                <option value="L300" {{ old('level') == 'L300' ? 'selected' : '' }}>Level 300</option>
                <option value="L400" {{ old('level') == 'L400' ? 'selected' : '' }}>Level 400</option>
                <option value="MASTERS" {{ old('level') == 'MASTERS' ? 'selected' : '' }}>Masters / MBA</option>
                <option value="PHD" {{ old('level') == 'PHD' ? 'selected' : '' }}>Doctorate / PhD</option>
            </select>
        </div>

        <div>
            <label for="program" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Program of Study
            </label>
            <select id="program" name="program" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet bg-white">
                <option value="BSc. Business Information Systems (BIS)" {{ old('program') == 'BSc. Business Information Systems (BIS)' ? 'selected' : '' }}>BSc. Business Information Systems (BIS)</option>
                <option value="BSc. Accounting" {{ old('program') == 'BSc. Accounting' ? 'selected' : '' }}>BSc. Accounting</option>
                <option value="BSc. Banking and Finance" {{ old('program') == 'BSc. Banking and Finance' ? 'selected' : '' }}>BSc. Banking &amp; Finance</option>
                <option value="BBA. Marketing" {{ old('program') == 'BBA. Marketing' ? 'selected' : '' }}>BBA. Marketing</option>
                <option value="BBA. Human Resource Management" {{ old('program') == 'BBA. Human Resource Management' ? 'selected' : '' }}>BBA. Human Resource Management</option>
                <option value="BSc. Procurement & Supply Chain" {{ old('program') == 'BSc. Procurement & Supply Chain' ? 'selected' : '' }}>BSc. Procurement &amp; Supply Chain</option>
                <option value="MBA. Strategic Management" {{ old('program') == 'MBA. Strategic Management' ? 'selected' : '' }}>MBA. Strategic Management</option>
                <option value="PhD Business Administration" {{ old('program') == 'PhD Business Administration' ? 'selected' : '' }}>PhD Business Administration</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Password (min. 8)
            </label>
            <input id="password" name="password" type="password" required placeholder="••••••••"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Confirm Password
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="••••••••"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet">
        </div>
    </div>

    <button type="submit" 
            class="w-full py-3.5 px-4 mt-2 bg-gradient-to-r from-uew-scarlet to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-xl shadow-md shadow-red-700/20 text-sm transition-transform active:scale-[0.99]">
        Complete Registration
    </button>
</form>

<div class="mt-6 text-center text-xs text-slate-600">
    Already have an active student account? 
    <a href="{{ route('login') }}" class="font-bold text-uew-scarlet hover:underline ml-1">Sign In</a>
</div>
@endsection
