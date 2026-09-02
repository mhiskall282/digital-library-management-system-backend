@extends('layouts.app')

@section('title', 'Profile & Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Account & Academic Settings</h1>
        <p class="text-xs text-slate-500 mt-0.5">Manage your student profile, academic level, and notification preferences.</p>
    </div>

    <!-- Student Status Card -->
    <div class="bg-gradient-to-r from-uew-navy-dark to-uew-navy p-6 rounded-3xl text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center font-black text-xl text-white">
                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
            </div>
            <div>
                <span class="block text-lg font-bold">{{ $user->name }}</span>
                <span class="block text-xs text-blue-200">{{ $user->student_id ?: 'Staff Administrator' }} &middot; {{ $user->program }}</span>
                <div class="mt-1 flex items-center space-x-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-uew-scarlet text-white uppercase">{{ $user->level }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white/10 text-white">{{ $user->role }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-4 text-center">
            <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10">
                <span class="block text-xl font-bold">{{ $bookmarksCount }}</span>
                <span class="block text-[10px] uppercase text-blue-200">Bookmarks</span>
            </div>
            <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10">
                <span class="block text-xl font-bold">{{ $reviewsCount }}</span>
                <span class="block text-[10px] uppercase text-blue-200">Reviews</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Academic Profile Form -->
        <div class="md:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Student Information & Academic Stream
            </h2>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            First Name
                        </label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet">
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Last Name
                        </label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet focus:border-uew-scarlet">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Current Academic Level
                        </label>
                        <select id="level" name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                            @foreach(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'] as $lvl)
                                <option value="{{ $lvl }}" {{ old('level', $user->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="program" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Program of Study
                        </label>
                        <input id="program" name="program" type="text" value="{{ old('program', $user->program) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>
                </div>

                <!-- Readonly ID & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Student Index No.</label>
                        <input type="text" value="{{ $user->student_id }}" disabled class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
                        <input type="text" value="{{ $user->email }}" disabled class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Alert Preferences</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet focus:ring-uew-scarlet border-slate-300">
                            <span>Receive email alerts for exam schedule & system notifications</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="new_resource_alerts" value="1" {{ old('new_resource_alerts', $user->new_resource_alerts) ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet focus:ring-uew-scarlet border-slate-300">
                            <span>Instant alerts when lecturers upload new slides for my course level</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Change Password
            </h2>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Current Password
                    </label>
                    <input id="current_password" name="current_password" type="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        New Password
                    </label>
                    <input id="new_password" name="password" type="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Confirm New Password
                    </label>
                    <input id="new_password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
