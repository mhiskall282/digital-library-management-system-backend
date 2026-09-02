@extends('layouts.admin')

@section('title', 'Student Directory')
@section('page_title', 'Student Directory & User Administration')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Registered Library Users</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review student accounts, enforce security policies, or activate/deactivate accounts.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search ID, name, email..." 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
            </div>

            <div>
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                    <option value="">All Roles</option>
                    <option value="student" {{ $role === 'student' ? 'selected' : '' }}>Students</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrators</option>
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-900 text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                    Filter
                </button>
                @if($search || $role || $status || $level)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 font-semibold text-xs rounded-lg hover:bg-slate-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">User</th>
                        <th class="px-4 py-3.5">Student Index / Staff ID</th>
                        <th class="px-4 py-3.5">Level & Program</th>
                        <th class="px-4 py-3.5">Role</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Activity</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $usr)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-700">
                                        {{ strtoupper(substr($usr->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block leading-tight">{{ $usr->name }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $usr->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-slate-700">
                                {{ $usr->student_id ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ $usr->level }}</span>
                                <span class="text-[11px] text-slate-500 block truncate max-w-xs mt-0.5">{{ $usr->program }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $usr->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-50 text-uew-navy' }}">
                                    {{ $usr->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($usr->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                        Deactivated
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-500 text-[11px]">
                                <span>{{ $usr->bookmarks_count }} saved</span> &middot; 
                                <span>{{ $usr->reviews_count }} reviews</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                @if($usr->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $usr) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold {{ $usr->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            {{ $usr->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.role', $usr) }}" class="inline-block" onsubmit="return confirm('Alter administrative status for this user?')">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $usr->isAdmin() ? 'student' : 'admin' }}">
                                        <button type="submit" class="text-xs font-bold text-slate-500 hover:text-uew-navy">
                                            {{ $usr->isAdmin() ? 'Make Student' : 'Make Admin' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 italic">
                                No student records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
