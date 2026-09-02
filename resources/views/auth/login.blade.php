@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div x-data="{ showPass: false, loginType: 'student' }" class="w-full max-w-md mx-auto">
    <!-- Header -->
    <div class="mb-6 space-y-1 text-center sm:text-left">
        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-red-50 border border-red-200 text-uew-scarlet text-[11px] font-bold mb-1">
            <span>🔒 Institutional Scholar Portal</span>
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Sign In to Your Account</h2>
        <p class="text-xs text-slate-500">Access verified lecture slides, past examination papers, and course notes.</p>
    </div>

    <!-- Login Mode Switcher Pills -->
    <div class="flex items-center p-1 bg-slate-100 rounded-xl mb-5 text-xs font-bold">
        <button type="button" 
                @click="loginType = 'student'" 
                :class="loginType === 'student' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-1.5 rounded-lg transition text-center">
            🎓 Student (Index No.)
        </button>
        <button type="button" 
                @click="loginType = 'staff'" 
                :class="loginType === 'staff' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-1.5 rounded-lg transition text-center">
            🏛️ Faculty &amp; Staff (Email)
        </button>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Username / Student ID input -->
        <div>
            <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                <span x-text="loginType === 'student' ? 'Student ID (Index Number) *' : 'Institutional Email *'"></span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <template x-if="loginType === 'student'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </template>
                    <template x-if="loginType === 'staff'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </template>
                </div>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                       :placeholder="loginType === 'student' ? 'e.g. 5201040001' : 'e.g. jmensah@uew.edu.gh'"
                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet transition placeholder:text-slate-400 @error('login') border-red-500 @enderror">
            </div>
            @error('login')
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password input with toggle -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Password *
                </label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-uew-scarlet hover:underline">
                    Forgot password?
                </a>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password" name="password" :type="showPass ? 'text' : 'password'" required
                       placeholder="••••••••"
                       class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet transition placeholder:text-slate-400 @error('password') border-red-500 @enderror">
                <button type="button" 
                        @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between py-1">
            <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer select-none">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-uew-scarlet focus:ring-uew-scarlet border-slate-300">
                <span>Keep me signed in on this browser</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full py-3.5 px-4 bg-gradient-to-r from-uew-scarlet to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-xl shadow-md shadow-red-700/20 text-sm transition-transform active:scale-[0.99] flex items-center justify-center space-x-2">
            <span>Sign In to Portal</span>
            <span>&rarr;</span>
        </button>
    </form>

    <!-- Registration Link -->
    <div class="mt-6 text-center text-xs text-slate-600">
        New student at UEW School of Business? 
        <a href="{{ route('register') }}" class="font-bold text-uew-scarlet hover:underline ml-1">Register Index No.</a>
    </div>

    <!-- Help & Documentation Footer -->
    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
        <span>🔒 256-bit Encrypted</span>
        <a href="{{ url('/docs') }}" class="text-uew-navy font-bold hover:underline">📖 Need Help? Read Guide</a>
    </div>
</div>
@endsection
