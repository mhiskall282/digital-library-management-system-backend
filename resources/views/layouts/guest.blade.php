<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — UEW School of Business Digital Library</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind / Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-dvh bg-slate-950 flex flex-col justify-between text-slate-800 antialiased relative overflow-x-hidden selection:bg-uew-scarlet selection:text-white p-3 sm:p-6 lg:p-8">

    <!-- Decorative Glow Orbs -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[800px] h-[350px] bg-gradient-to-br from-uew-scarlet/20 via-uew-navy/30 to-transparent blur-3xl pointer-events-none rounded-full"></div>

    <!-- Top Mini Brand Bar -->
    <header class="w-full max-w-5xl mx-auto py-2 flex items-center justify-between relative z-10">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-2.5 text-white group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white shadow-md font-black text-base group-hover:scale-105 transition-transform">
                U
            </div>
            <div class="text-left">
                <span class="block text-sm font-black tracking-tight leading-none text-white">UEW School of Business</span>
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Digital Library Repository</span>
            </div>
        </a>

        <a href="{{ url('/docs') }}" class="text-xs font-bold text-slate-300 hover:text-white transition flex items-center space-x-1 bg-white/10 hover:bg-white/15 px-3 py-1.5 rounded-xl border border-white/10">
            <span>📖</span>
            <span class="hidden sm:inline">User Guide &amp; Docs</span>
        </a>
    </header>

    <!-- Main Card Container -->
    <main class="w-full max-w-5xl mx-auto my-auto relative z-10 py-4">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-3.5 mb-4 text-xs font-semibold rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-center max-w-md mx-auto">
                {{ session('success') }}
            </div>
        @endif

        @if(session('status'))
            <div class="p-3.5 mb-4 text-xs font-semibold rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-400 text-center max-w-md mx-auto">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 mb-4 text-xs font-semibold rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-center max-w-md mx-auto">
                {{ session('error') }}
            </div>
        @endif

        <!-- Card Container: 2 Columns on md+ -->
        <div class="bg-white shadow-2xl shadow-black/50 rounded-2xl sm:rounded-3xl border border-slate-200/80 overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[540px]">
            <!-- Left Branding Image Panel (Hidden on Mobile, Visible on md+) -->
            <div class="hidden md:flex md:col-span-5 relative bg-slate-900 flex-col justify-between p-8 text-white"
                 style="background-image: linear-gradient(to bottom, rgba(15, 23, 42, 0.78), rgba(30, 58, 138, 0.88), rgba(15, 23, 42, 0.96)), url('{{ asset('images/hero_library.jpg') }}'); background-size: cover; background-position: center;">
                <div class="space-y-3 relative z-10">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-uew-scarlet text-white w-fit inline-block shadow-xs">
                        Accredited Academic Repository
                    </span>
                    <h2 class="text-2xl font-black leading-tight text-white">Empowering Business Scholars</h2>
                    <p class="text-xs text-slate-200 leading-relaxed">
                        Access official lecture presentations, curriculum revision decks, and past examinations verified by department faculty.
                    </p>
                </div>

                <div class="relative z-10 space-y-3 pt-6 border-t border-white/20">
                    <div class="flex items-center space-x-2 text-xs font-bold text-amber-300">
                        <span>🎖️</span>
                        <span>Earn Contributor Recognition</span>
                    </div>
                    <p class="text-[11px] text-slate-300">
                        Upload verified course materials, gain scholar points, and climb the School of Business leaderboard.
                    </p>
                    <div class="pt-2">
                        <a href="{{ url('/docs') }}" class="text-xs text-white font-bold underline hover:text-amber-300 transition">
                            View Step-by-Step Student Guide &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column Form Panel: Autoscales smoothly on mobile & desktop -->
            <div class="md:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer Disclaimer -->
    <footer class="w-full max-w-5xl mx-auto py-3 text-center text-xs text-slate-500 relative z-10">
        <p>&copy; {{ date('Y') }} University of Education, Winneba &mdash; School of Business.</p>
        <p class="text-[10px] text-slate-500 mt-0.5">North Campus, Winneba, Ghana &bull; Accredited Institutional Repository</p>
    </footer>

</body>
</html>
