@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Recover Account Access</h2>
    <p class="text-xs text-slate-500 mt-1">Provide your registered email address and we will generate instructions to reset your password.</p>
</div>

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Registered Institutional Email
        </label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
               placeholder="e.g. student@st.uew.edu.gh"
               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" 
            class="w-full py-3.5 px-4 bg-gradient-to-r from-uew-scarlet to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-xl shadow-md shadow-red-700/20 text-sm transition-transform active:scale-[0.99]">
        Send Password Reset Link
    </button>
</form>

@if(session('demo_reset_token'))
    <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
        <span class="font-bold block mb-1">Local Testing Reset Link:</span>
        <a href="{{ route('password.reset', ['token' => session('demo_reset_token'), 'email' => session('demo_reset_email')]) }}" class="text-uew-scarlet underline break-all font-semibold">
            Click here to directly reset password for {{ session('demo_reset_email') }}
        </a>
    </div>
@endif

<div class="mt-6 text-center text-xs text-slate-600">
    Remember your password credentials? 
    <a href="{{ route('login') }}" class="font-bold text-uew-scarlet hover:underline ml-1">Return to Sign In</a>
</div>
@endsection
