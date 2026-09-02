@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notification Center</h1>
            <p class="text-xs text-slate-500 mt-0.5">Stay updated on newly uploaded course materials and department announcements.</p>
        </div>

        <div class="flex items-center space-x-2">
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                        ✓ Mark All Read
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('notifications.clear-read') }}">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                    Clear Read
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('notifications.index') }}" 
           class="px-3 py-1.5 rounded-lg transition {{ $filter === 'all' ? 'bg-uew-scarlet text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            All Notifications
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
           class="px-3 py-1.5 rounded-lg transition flex items-center space-x-1.5 {{ $filter === 'unread' ? 'bg-uew-scarlet text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Unread</span>
            @if($unreadCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'unread' ? 'bg-white text-uew-scarlet' : 'bg-uew-scarlet text-white' }}">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Notifications List -->
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="p-4 rounded-2xl border transition-all flex items-start justify-between gap-4 {{ !$notification->is_read ? 'bg-blue-50/50 border-blue-200/80 shadow-xs' : 'bg-white border-slate-200/80' }}">
                    <div class="flex items-start space-x-3.5">
                        <!-- Icon -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $notification->type === 'NEW_RESOURCE' ? 'bg-emerald-100 text-emerald-700' : ($notification->type === 'SYSTEM' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-uew-navy') }}">
                            @if($notification->type === 'NEW_RESOURCE')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            @elseif($notification->type === 'SYSTEM')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                    {{ $notification->title }}
                                </h3>
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 rounded-full bg-uew-scarlet"></span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ $notification->message }}
                            </p>

                            <div class="flex items-center space-x-3 text-[11px] pt-1">
                                <span class="text-slate-400 font-medium">{{ $notification->created_at->diffForHumans() }}</span>

                                @if($notification->link)
                                    <a href="{{ $notification->link }}" class="font-bold text-uew-scarlet hover:underline">
                                        Open Material &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Individual Actions -->
                    <div class="flex items-center space-x-1 shrink-0">
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Mark as read">
                                    ✓
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete notification">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-md mx-auto space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                🔔
            </div>
            <h3 class="text-base font-bold text-slate-900">No notifications</h3>
            <p class="text-xs text-slate-500">You're all caught up! There are no unread announcements or material alerts right now.</p>
        </div>
    @endif

</div>
@endsection
