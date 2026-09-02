@extends('layouts.guest')

@section('title', 'Set New Password')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Set New Password</h2>
    <p class="text-xs text-slate-500 mt-1">Enter your new secure password below to complete account recovery.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Email Address
        </label>
        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            New Password (min. 8 characters)
        </label>
        <input id="password" name="password" type="password" required placeholder="••••••••"
               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('password') border-red-500 @enderror">
        @error('password')
            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Confirm New Password
        </label>
        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="••••••••"
               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet">
    </div>

    <button type="submit" 
            class="w-full py-3.5 px-4 bg-gradient-to-r from-uew-scarlet to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-xl shadow-md shadow-red-700/20 text-sm transition-transform active:scale-[0.99]">
        Save New Password & Sign In
    </button>
</form>
@endsection
