@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'Library Management & System Configuration')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'academic' }">

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

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="submit" class="px-6 py-2.5 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Save System Parameters
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
