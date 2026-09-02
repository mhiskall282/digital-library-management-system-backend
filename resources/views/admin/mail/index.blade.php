@extends('layouts.admin')

@section('title', 'Email Templates & SMTP Dispatch Studio')
@section('page_title', 'Email Templates & Dispatch Studio')

@section('content')
<div class="space-y-6">

    <!-- Header & Information -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Institutional Email Templates &amp; Dispatch Studio</h1>
            <p class="text-xs text-slate-500 mt-0.5">Preview live HTML email templates and trigger diagnostic SMTP dispatches to verify deliverability.</p>
        </div>

        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">
            <span>SMTP User:</span>
            <code class="font-mono text-uew-navy font-bold">{{ config('mail.mailers.smtp.username') ?? 'test@johnokyere.xyz' }}</code>
        </div>
    </div>

    <!-- Template Selector Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('admin.mail.index', ['template' => 'welcome']) }}" 
           class="px-4 py-2 rounded-xl transition {{ $template === 'welcome' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            ✉️ Welcome &amp; Activation
        </a>
        <a href="{{ route('admin.mail.index', ['template' => 'security']) }}" 
           class="px-4 py-2 rounded-xl transition {{ $template === 'security' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            🛡️ Security Alert
        </a>
        <a href="{{ route('admin.mail.index', ['template' => 'broadcast']) }}" 
           class="px-4 py-2 rounded-xl transition {{ $template === 'broadcast' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            📢 Departmental Broadcast
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Dispatch Test Panel -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Trigger Live SMTP Test</h3>
            <p class="text-xs text-slate-500">
                Send a real test email with the currently selected template using your configured credentials (<code class="text-uew-navy font-semibold">test@johnokyere.xyz</code>).
            </p>

            <form method="POST" action="{{ route('admin.mail.send') }}" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="template" value="{{ $template }}">

                <div>
                    <label for="recipient" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Recipient Address *
                    </label>
                    <input id="recipient" name="recipient" type="email" value="{{ old('recipient', 'test@johnokyere.xyz') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <span class="font-bold text-slate-700 block">Current Template:</span>
                    <span class="font-mono text-uew-scarlet font-semibold uppercase">{{ $template }} Mail</span>
                </div>

                <button type="submit" class="w-full py-3 bg-uew-navy hover:bg-uew-navy-hover text-white font-bold rounded-xl text-xs shadow-xs transition">
                    🚀 Dispatch Test Email
                </button>
            </form>
        </div>

        <!-- Right: Live Rendered Iframe Preview -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="bg-slate-100 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between text-xs text-slate-500 font-semibold">
                <span>HTML Responsive Preview &mdash; {{ ucfirst($template) }} Template</span>
                <span class="text-[10px] text-slate-400">Rendered via Blade Mailable</span>
            </div>
            <div class="p-4 bg-slate-50 flex justify-center">
                <div class="w-full max-w-xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    {!! $previewHtml !!}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
