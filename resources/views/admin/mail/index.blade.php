@extends('layouts.admin')

@section('title', 'Email Templates & Dispatch Studio')
@section('page_title', 'Email Templates & Dispatch Studio')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ $tab }}', 
    showModal: false, 
    modalEmail: null,
    viewLog(log) {
        this.modalEmail = log;
        this.showModal = true;
    }
}">

    <!-- Header & System Health Pill -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Institutional Email Studio &amp; Simulator</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                    Active &bull; Live &amp; Simulated
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Preview institutional Blade mailables, test outgoing Zoho SMTP delivery, and simulate incoming/outgoing scholar correspondence with zero external subscription blockers.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-semibold shadow-2xs">
                <span class="text-slate-400">SMTP:</span>
                <span class="font-mono text-uew-navy font-bold">{{ config('mail.mailers.smtp.username') ?? 'test@johnokyere.xyz' }}</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-mono">smtppro.zoho.com:465</span>
            </div>
        </div>
    </div>

    <!-- Metric Counter Badges -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Dispatched Emails</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $outgoingCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Outbound to scholars</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inbound Inquiries</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $incomingCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Simulated scholar replies</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Templates Active</div>
            <div class="text-xl font-black text-uew-scarlet mt-1">3</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Welcome, Alert, Broadcast</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Simulation Mailbox</div>
            <div class="text-xl font-black text-emerald-600 mt-1">Ready</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Zero subscription required</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
        <div class="flex items-center space-x-2 text-xs font-bold">
            <button type="button" @click="activeTab = 'preview'" 
                    :class="activeTab === 'preview' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2 rounded-xl transition cursor-pointer">
                🎨 Template Preview &amp; Dispatch
            </button>
            <button type="button" @click="activeTab = 'mailbox'" 
                    :class="activeTab === 'mailbox' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                <span>📬 In-App Mailbox &amp; History</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-900 text-white font-mono" x-text="'{{ $logs->count() }}'"></span>
            </button>
            <button type="button" @click="activeTab = 'incoming'" 
                    :class="activeTab === 'incoming' ? 'bg-uew-scarlet text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2 rounded-xl transition cursor-pointer">
                📥 Simulate Inbound Reply
            </button>
        </div>

        @if($logs->count() > 0)
        <form method="POST" action="{{ route('admin.mail.clear-logs') }}" onsubmit="return confirm('Clear all simulated email logs?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[11px] font-semibold text-slate-400 hover:text-red-600 transition">
                Clear Mailbox Logs
            </button>
        </form>
        @endif
    </div>

    <!-- TAB 1: Template Preview & Dispatch -->
    <div x-show="activeTab === 'preview'" class="space-y-6">
        <!-- Sub-selector for templates -->
        <div class="flex items-center space-x-2 text-xs font-semibold">
            <span class="text-slate-500 font-bold mr-1">Choose Template:</span>
            <a href="{{ route('admin.mail.index', ['template' => 'welcome', 'tab' => 'preview']) }}" 
               class="px-3 py-1.5 rounded-lg transition {{ $template === 'welcome' ? 'bg-uew-navy text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                ✉️ Welcome &amp; Activation
            </a>
            <a href="{{ route('admin.mail.index', ['template' => 'security', 'tab' => 'preview']) }}" 
               class="px-3 py-1.5 rounded-lg transition {{ $template === 'security' ? 'bg-uew-navy text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🛡️ Security Alert
            </a>
            <a href="{{ route('admin.mail.index', ['template' => 'broadcast', 'tab' => 'preview']) }}" 
               class="px-3 py-1.5 rounded-lg transition {{ $template === 'broadcast' ? 'bg-uew-navy text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                📢 Departmental Broadcast
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Dispatch Panel -->
            <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Dispatch Email</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Test delivery via live Zoho SMTP or simulate in-app without external limits.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.mail.send') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="template" value="{{ $template }}">

                    <div>
                        <label for="recipient" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Recipient Address *
                        </label>
                        <input id="recipient" name="recipient" type="email" value="{{ old('recipient', 'test@johnokyere.xyz') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <!-- Dispatch Mode Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Delivery Mode *
                        </label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex flex-col p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-uew-scarlet has-[:checked]:bg-scarlet-50/20">
                                <span class="flex items-center gap-1.5 font-bold text-slate-800">
                                    <input type="radio" name="mode" value="simulate" checked class="text-uew-scarlet focus:ring-uew-scarlet">
                                    ⚡ In-App Simulator
                                </span>
                                <span class="text-[10px] text-slate-500 mt-1">Instant delivery to In-App Mailbox tab below (recommended)</span>
                            </label>

                            <label class="flex flex-col p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-uew-scarlet has-[:checked]:bg-scarlet-50/20">
                                <span class="flex items-center gap-1.5 font-bold text-slate-800">
                                    <input type="radio" name="mode" value="smtp" class="text-uew-scarlet focus:ring-uew-scarlet">
                                    🌐 Live Zoho SMTP
                                </span>
                                <span class="text-[10px] text-slate-500 mt-1">Direct via smtppro.zoho.com (auto-falls back if 554)</span>
                            </label>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <span class="font-bold text-slate-700 block">Selected Template:</span>
                        <span class="font-mono text-uew-scarlet font-semibold uppercase">{{ $template }} Mail</span>
                    </div>

                    <button type="submit" class="w-full py-3 bg-uew-navy hover:bg-uew-navy-hover text-white font-bold rounded-xl text-xs shadow-xs transition">
                        🚀 Dispatch Message
                    </button>
                </form>

                <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                    💡 <strong>Zoho Note:</strong> If live SMTP returns <code>554 5.7.8</code>, our automatic fallback saves the email immediately to your In-App Mailbox tab, so you can review full HTML delivery with zero downtime.
                </div>
            </div>

            <!-- Right: Live Rendered Iframe Preview -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-slate-100 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between text-xs text-slate-500 font-semibold">
                    <span>HTML Responsive Preview &mdash; {{ ucfirst($template) }} Template</span>
                    <span class="text-[10px] text-slate-400">Institutional UEW Scarlet &amp; Navy Theme</span>
                </div>
                <div class="p-4 bg-slate-50 flex justify-center overflow-x-auto">
                    <div class="w-full max-w-xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        {!! $previewHtml !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: In-App Mailbox & History -->
    <div x-show="activeTab === 'mailbox'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">In-App Mailbox &amp; Dispatched Telemetry</h3>
                    <p class="text-xs text-slate-500">Chronological history of all live SMTP dispatches, fallback archives, and simulated incoming/outgoing emails.</p>
                </div>
                <div class="text-xs font-semibold text-slate-500">
                    Total Records: <strong class="text-slate-900">{{ $logs->count() }}</strong>
                </div>
            </div>

            @if($logs->isEmpty())
                <div class="p-12 text-center text-slate-400 space-y-2">
                    <div class="text-3xl">📭</div>
                    <div class="text-sm font-bold text-slate-700">No emails logged in this session yet</div>
                    <p class="text-xs max-w-md mx-auto text-slate-500">
                        Dispatch a test email using the Template Preview tab or click "Simulate Inbound Reply" to test receiving an email.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/70 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-5 py-3">Direction</th>
                                <th class="px-5 py-3">Recipient / Sender</th>
                                <th class="px-5 py-3">Subject</th>
                                <th class="px-5 py-3">Delivery Status</th>
                                <th class="px-5 py-3">Timestamp</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($logs as $log)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3 font-bold">
                                        @if($log->direction === 'outgoing')
                                            <span class="inline-flex items-center gap-1 text-blue-700">
                                                <span>↗ Outgoing</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-emerald-700">
                                                <span>↙ Incoming</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="font-bold text-slate-800">
                                            {{ $log->direction === 'outgoing' ? $log->recipient : $log->sender }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $log->direction === 'outgoing' ? 'From: ' . $log->sender : 'To: ' . $log->recipient }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="font-semibold text-slate-900 line-clamp-1">{{ $log->subject }}</span>
                                        @if($log->template)
                                            <span class="text-[10px] font-mono text-uew-scarlet font-bold">[{{ $log->template }}]</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($log->status === 'delivered')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                ✓ Delivered (SMTP)
                                            </span>
                                        @elseif($log->status === 'received')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800">
                                                ✓ Received (Inbound)
                                            </span>
                                        @elseif($log->status === 'simulated')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900">
                                                ★ In-App Simulated
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800">
                                                ✕ Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-slate-500 font-mono text-[11px]">
                                        {{ $log->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button type="button" 
                                                @click="viewLog({{ json_encode($log) }})"
                                                class="px-2.5 py-1 rounded-lg bg-slate-900 text-white hover:bg-uew-scarlet font-bold text-[11px] transition cursor-pointer">
                                            Inspect &rarr;
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: Simulate Inbound Email -->
    <div x-show="activeTab === 'incoming'" class="space-y-6" style="display: none;">
        <div class="max-w-2xl bg-white p-6 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Simulate Inbound Student / Faculty Email</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Facilitate incoming inquiry handling and reply cycles without requiring premium IMAP or POP3 server subscriptions.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.mail.simulate-incoming') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="inbound_sender" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Scholar / Staff Email *
                    </label>
                    <input id="inbound_sender" name="sender" type="email" value="student.mensah@uew.edu.gh" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <div>
                    <label for="inbound_subject" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Inquiry Subject *
                    </label>
                    <input id="inbound_subject" name="subject" type="text" value="Request for BNF 311 Week 8 Lecture Slide Archive" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">
                </div>

                <div>
                    <label for="inbound_message" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Message Content *
                    </label>
                    <textarea id="inbound_message" name="message" rows="4" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet">Hello UEW Business Library Desk,

Could you please verify if the Week 8 slide deck for Financial Modeling (BNF 311) has been approved and published? I submitted it yesterday through the scholar contribute portal.

Thank you,
Kwame Mensah (Index: 5201040001)</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition">
                    📥 Simulate Inbound Message Arrival
                </button>
            </form>
        </div>
    </div>

    <!-- Email Inspection Modal (Alpine.js) -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs" 
         x-cloak>
        <div @click.away="showModal = false" 
             class="bg-white w-full max-w-2xl rounded-3xl shadow-xl border border-slate-200 overflow-hidden max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div>
                    <h3 class="font-bold text-sm" x-text="modalEmail ? modalEmail.subject : 'Email Details'"></h3>
                    <div class="text-[11px] text-slate-400 mt-0.5">
                        <span x-text="modalEmail ? 'To: ' + modalEmail.recipient : ''"></span> &bull; 
                        <span x-text="modalEmail ? 'From: ' + modalEmail.sender : ''"></span>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-white text-lg font-bold">✕</button>
            </div>

            <!-- Error banner if any -->
            <template x-if="modalEmail && modalEmail.error_message">
                <div class="p-3 bg-amber-50 border-b border-amber-200 text-amber-900 text-xs font-semibold">
                    ⚠️ SMTP Response: <span x-text="modalEmail.error_message"></span>
                </div>
            </template>

            <!-- Modal Body (HTML Rendering) -->
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs" x-html="modalEmail ? modalEmail.body_html : ''"></div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-100 border-t border-slate-200 flex justify-end">
                <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-uew-scarlet transition">
                    Close Inspector
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
