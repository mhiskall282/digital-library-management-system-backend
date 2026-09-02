@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'Library Management & System Configuration')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ request('tab', session('active_tab', 'academic')) }}',
    showPassword: false,
    setPreset(provider) {
        if (provider === 'gmail') {
            document.getElementById('mail_mailer').value = 'smtp';
            document.getElementById('mail_host').value = 'smtp.gmail.com';
            document.getElementById('mail_port').value = '587';
            document.getElementById('mail_encryption').value = 'tls';
        } else if (provider === 'zoho') {
            document.getElementById('mail_mailer').value = 'smtp';
            document.getElementById('mail_host').value = 'smtppro.zoho.com';
            document.getElementById('mail_port').value = '465';
            document.getElementById('mail_encryption').value = 'ssl';
        } else if (provider === 'log') {
            document.getElementById('mail_mailer').value = 'log';
        }
    }
}">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">System & Library Policies</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure academic semesters, upload limits, level gating, and server maintenance.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.cache-clear') }}">
            @csrf
            <button type="submit" onclick="return confirm('Flush application and view caches?')" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Flush System Cache</span>
            </button>
        </form>
    </div>

    <!-- Server Telemetry Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Framework</span>
            <span class="text-xs font-black text-slate-800">Laravel {{ $systemInfo['laravel_version'] }}</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">PHP Runtime</span>
            <span class="text-xs font-black text-slate-800">v{{ $systemInfo['php_version'] }}</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Database</span>
            <span class="text-xs font-black text-uew-navy uppercase">{{ $systemInfo['db_connection'] }}</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Cache Driver</span>
            <span class="text-xs font-black text-slate-800 uppercase">{{ $systemInfo['cache_driver'] }}</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Environment</span>
            <span class="text-xs font-black text-emerald-600 uppercase">{{ $systemInfo['environment'] }}</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Debug Mode</span>
            <span class="text-xs font-black text-slate-800">{{ $systemInfo['debug_mode'] }}</span>
        </div>
    </div>

    <!-- Tabbed Settings Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xs overflow-hidden">
        <!-- Tab Navigation Header -->
        <div class="flex items-center space-x-1 border-b border-slate-100 px-6 pt-4 bg-slate-50/50">
            <button @click="activeTab = 'academic'" type="button" 
                    :class="activeTab === 'academic' ? 'border-uew-scarlet text-uew-scarlet font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="py-3 px-4 text-xs border-b-2 transition">
                Academic & Sessions
            </button>
            <button @click="activeTab = 'storage'" type="button" 
                    :class="activeTab === 'storage' ? 'border-uew-scarlet text-uew-scarlet font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="py-3 px-4 text-xs border-b-2 transition">
                Storage & Upload Quotas
            </button>
            <button @click="activeTab = 'security'" type="button" 
                    :class="activeTab === 'security' ? 'border-uew-scarlet text-uew-scarlet font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="py-3 px-4 text-xs border-b-2 transition">
                Access Control & Gating
            </button>
            <button @click="activeTab = 'notifications'" type="button" 
                    :class="activeTab === 'notifications' ? 'border-uew-scarlet text-uew-scarlet font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="py-3 px-4 text-xs border-b-2 transition">
                Alerts & Mail
            </button>
            <button @click="activeTab = 'smtp'" type="button" 
                    :class="activeTab === 'smtp' ? 'border-uew-scarlet text-uew-scarlet font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="py-3 px-4 text-xs border-b-2 transition flex items-center space-x-1.5">
                <span>✉️ SMTP & Email Logins</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Tab 1: Academic & Sessions -->
            <div x-show="activeTab === 'academic'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Active Academic Year *</label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $settings['academic_year']->value ?? '2023/2024') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                        <span class="text-[11px] text-slate-400 mt-1 block">Default session attached to newly uploaded lecture slides.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Active Semester *</label>
                        <select name="active_semester" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                            <option value="FIRST" {{ ($settings['active_semester']->value ?? 'FIRST') === 'FIRST' ? 'selected' : '' }}>First Semester</option>
                            <option value="SECOND" {{ ($settings['active_semester']->value ?? '') === 'SECOND' ? 'selected' : '' }}>Second Semester</option>
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">Prioritizes course materials matching the active semester in the catalog.</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Repository Institution Name *</label>
                    <input type="text" name="institution_name" value="{{ old('institution_name', $settings['institution_name']->value ?? 'University of Education, Winneba — School of Business') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                </div>
            </div>

            <!-- Tab 2: Storage & Upload Limits -->
            <div x-show="activeTab === 'storage'" class="space-y-4" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Maximum Upload File Size (MB) *</label>
                        <input type="number" name="max_upload_size_mb" value="{{ old('max_upload_size_mb', $settings['max_upload_size_mb']->value ?? 100) }}" min="5" max="500" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                        <span class="text-[11px] text-slate-400 mt-1 block">Enforced on all lecturer and admin file uploads.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Permitted File Extensions *</label>
                        <input type="text" name="allowed_file_extensions" value="{{ old('allowed_file_extensions', $settings['allowed_file_extensions']->value ?? 'pdf, ppt, pptx, doc, docx, zip') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                        <span class="text-[11px] text-slate-400 mt-1 block">Comma-separated list of allowed document extensions.</span>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Security & Access Control -->
            <div x-show="activeTab === 'security'" class="space-y-4" x-cloak>
                <div class="space-y-3">
                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="allow_student_registration" value="1" {{ ($settings['allow_student_registration']->value ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet mt-0.5">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Enable Student Self-Registration</span>
                            <span class="block text-[11px] text-slate-500">Allow enrolled students to create their own library portal accounts using their index numbers.</span>
                        </div>
                    </label>

                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="enforce_level_gating" value="1" {{ ($settings['enforce_level_gating']->value ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet mt-0.5">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Enforce Academic Level Gating</span>
                            <span class="block text-[11px] text-slate-500">Restrict junior students from downloading materials intended exclusively for higher academic years.</span>
                        </div>
                    </label>

                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-red-200 bg-red-50/50 cursor-pointer">
                        <input type="checkbox" name="portal_maintenance_mode" value="1" {{ ($settings['portal_maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-red-600 mt-0.5">
                        <div>
                            <span class="block text-xs font-bold text-red-800">Scheduled Catalog Maintenance Mode</span>
                            <span class="block text-[11px] text-red-600">Displays a temporary maintenance banner across the public student portal.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Tab 4: Alerts & Mail -->
            <div x-show="activeTab === 'notifications'" class="space-y-4" x-cloak>
                <div class="space-y-3">
                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="enable_email_alerts" value="1" {{ ($settings['enable_email_alerts']->value ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet mt-0.5">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Enable SMTP Email Alerts</span>
                            <span class="block text-[11px] text-slate-500">Send password recovery and exam announcement emails through the configured mailer.</span>
                        </div>
                    </label>

                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="auto_notify_new_resources" value="1" {{ ($settings['auto_notify_new_resources']->value ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-uew-scarlet mt-0.5">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Automated New Resource Alerts</span>
                            <span class="block text-[11px] text-slate-500">Instantly generate in-app alerts for students when relevant lecture slides are published.</span>
                        </div>
                    </label>

                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Administrative Contact Email *</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']->value ?? 'library@uew.edu.gh') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>
                </div>
            </div>

            <!-- Tab 5: SMTP & Email Server Logins -->
            <div x-show="activeTab === 'smtp'" class="space-y-6" x-cloak>
                <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-xs text-blue-900 leading-relaxed">
                        <span class="font-bold block text-blue-950 mb-0.5">Live Outbound SMTP Gateway Configuration</span>
                        Configure live outbound email delivery (Google Gmail, Google Workspace, Zoho, AWS SES, or Custom SMTP). Changes saved here are stored in the database and immediately apply to all automated welcome emails, password resets, and announcements in real-time.
                    </div>
                </div>

                <!-- Provider Quick-Presets -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">One-Click Server Presets</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setPreset('gmail')" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-2xs">
                            <span>⚡ Google Gmail / Workspace (Port 587 TLS)</span>
                        </button>
                        <button type="button" @click="setPreset('zoho')" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-2xs">
                            <span>✉️ Zoho Mail (Port 465 SSL)</span>
                        </button>
                        <button type="button" @click="setPreset('log')" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-2xs">
                            <span>📋 Log Driver (In-App Only)</span>
                        </button>
                    </div>
                </div>

                <!-- Connection Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mail Driver *</label>
                        <select id="mail_mailer" name="mail_mailer" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                            <option value="smtp" {{ ($settings['mail_mailer']->value ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP (Network Dispatch)</option>
                            <option value="log" {{ ($settings['mail_mailer']->value ?? '') === 'log' ? 'selected' : '' }}>Log (Developer / Simulation)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SMTP Host *</label>
                        <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host']->value ?? 'smtp.gmail.com') }}" placeholder="smtp.gmail.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SMTP Port *</label>
                        <input type="number" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port']->value ?? '587') }}" placeholder="587"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Transport Encryption</label>
                        <select id="mail_encryption" name="mail_encryption" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet bg-white">
                            <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS / STARTTLS (Port 587)</option>
                            <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="none" {{ ($settings['mail_encryption']->value ?? '') === 'none' ? 'selected' : '' }}>None (Unencrypted)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SMTP Username / Email *</label>
                        <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username']->value ?? 'johnotchere282@gmail.com') }}" placeholder="username@gmail.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">SMTP Password / App Key</label>
                            @if(!empty($settings['mail_password']->value))
                                <span class="text-[10px] font-bold text-emerald-600 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Configured</span>
                                </span>
                            @endif
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="mail_password" name="mail_password" placeholder="{{ !empty($settings['mail_password']->value) ? '•••••••••••••••• (Leave blank to keep current)' : 'Enter 16-char App Password' }}"
                                   class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">From Sender Address</label>
                        <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']->value ?? 'johnotchere282@gmail.com') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">From Sender Display Name</label>
                        <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']->value ?? 'UEW School of Business Digital Library') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-uew-scarlet">
                    </div>
                </div>

                <div class="p-4 bg-amber-50/60 border border-amber-200/80 rounded-2xl flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-[11px] text-amber-900 leading-relaxed">
                        <span class="font-bold block text-amber-950 mb-0.5">Google Workspace / Gmail Security Note:</span>
                        Google does not accept regular account passwords over SMTP. You must generate a 16-character <strong>App Password</strong> by visiting <a href="https://myaccount.google.com/apppasswords" target="_blank" class="underline font-bold text-amber-950">myaccount.google.com/apppasswords</a> with 2-Step Verification enabled.
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div x-show="activeTab === 'smtp'" class="flex items-center space-x-2">
                    <a href="{{ route('admin.mail.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-uew-navy hover:text-uew-scarlet transition">
                        <span>Open In-App Email Studio & Mailbox &rarr;</span>
                    </a>
                </div>
                <div x-show="activeTab !== 'smtp'"></div>

                <div class="flex items-center space-x-3">
                    <button type="submit" class="px-6 py-2.5 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Save System Parameters
                    </button>
                </div>
            </div>
        </form>

        <!-- Quick SMTP Test Dispatch Form (Standalone card in footer) -->
        <div x-show="activeTab === 'smtp'" class="bg-slate-50 border-t border-slate-200/80 p-6 sm:p-8" x-cloak>
            <div class="max-w-xl">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-1">Instant SMTP Connection Ping</h3>
                <p class="text-[11px] text-slate-500 mb-3">Send a real-time verification email to any address using the current SMTP credentials to verify connectivity.</p>
                <form method="POST" action="{{ route('admin.settings.test-smtp') }}" class="flex items-center space-x-2">
                    @csrf
                    <input type="email" name="test_recipient" value="{{ auth()->user()->email }}" required placeholder="Enter recipient email..."
                           class="flex-1 px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-uew-scarlet bg-white">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-2xs whitespace-nowrap">
                        Send Test Ping
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
