<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student & Faculty User Manual (Step-by-Step Guide) — UEW School of Business Digital Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col selection:bg-uew-scarlet selection:text-white" x-data="{ activeTab: 'students' }">

    <!-- Top University Banner Stripe -->
    <div class="bg-uew-scarlet text-white text-[11px] font-semibold py-1.5 px-4 sm:px-6 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>University of Education, Winneba &middot; School of Business</span>
            </div>
            <div class="hidden sm:flex items-center space-x-4 text-white/90 text-[10px] tracking-wider uppercase font-bold">
                <span>Official Non-Technical User Manual &middot; Student &amp; Staff Guide</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-uew-scarlet to-uew-navy flex items-center justify-center text-white shadow-md font-black text-xl group-hover:scale-105 transition-transform">
                        U
                    </div>
                    <div>
                        <span class="block text-lg font-black text-slate-900 tracking-tight leading-none">
                            UEW <span class="text-uew-scarlet">User Guide</span>
                        </span>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                            Step-by-Step Instructions
                        </span>
                    </div>
                </a>

                <!-- Quick Nav Links -->
                <nav class="hidden md:flex items-center space-x-4 text-xs font-bold text-slate-600">
                    <a href="{{ url('/') }}" class="hover:text-uew-scarlet transition">Public Home</a>
                    <a href="{{ route('programs.index') }}" class="hover:text-uew-scarlet transition">Programs Directory</a>
                    <a href="{{ route('dashboard') }}" class="hover:text-uew-scarlet transition">Catalog Explorer</a>
                    <a href="{{ route('requests.index') }}" class="hover:text-uew-scarlet transition">Request Desk</a>
                </nav>

                <!-- Auth / Portal Action -->
                <div class="flex items-center space-x-2">
                    @auth
                        <a href="{{ auth()->user()->canModerate() ? route('admin.dashboard') : route('student.hub') }}" 
                           class="px-4 py-2 bg-uew-scarlet hover:bg-uew-scarlet-hover text-white text-xs font-bold rounded-xl shadow-xs transition">
                            Open Portal &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-uew-navy hover:bg-uew-navy-hover text-white text-xs font-bold rounded-xl shadow-xs transition">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-uew-navy-dark to-slate-900 text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden border-b border-slate-800"
         style="background-image: linear-gradient(to right, rgba(15, 23, 42, 0.94), rgba(30, 58, 138, 0.90), rgba(15, 23, 42, 0.96)), url('{{ asset('images/collaboration.jpg') }}'); background-size: cover; background-position: center;">
        <div class="max-w-7xl mx-auto space-y-3 relative z-10">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-uew-scarlet text-white w-fit inline-block">
                Easy Plain-English Guide
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight">How to Use the UEW Business Digital Library</h1>
            <p class="text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed">
                A simple, non-technical walkthrough for students, class reps, and faculty members. Learn how to find your lecture slides, download past exams, track weekly modules, and earn contributor badges.
            </p>
        </div>
    </div>

    <!-- Main Guide Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Sticky Navigation Index -->
            <aside class="lg:col-span-3 space-y-3">
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs sticky top-24 space-y-1 text-xs">
                    <span class="block px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">Chapters &amp; Tutorials</span>
                    
                    <a href="#quick-start" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        ⚡ Quick Start in 3 Minutes
                    </a>
                    <a href="#step-login" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        1. Signing In &amp; Profile Setup
                    </a>
                    <a href="#step-find-slides" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        2. Finding Slides &amp; Past Exams
                    </a>
                    <a href="#step-weeks" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        3. Using Syllabus Weeks (1–15)
                    </a>
                    <a href="#step-downloads" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        4. Downloading &amp; Access Requests
                    </a>
                    <a href="#step-contribute" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        5. Uploading Slides (+50 Pts)
                    </a>
                    <a href="#step-gamification" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        6. Points, Badges &amp; Leaderboard
                    </a>
                    <a href="#step-support" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        7. Requesting Missing Materials
                    </a>
                    <a href="#step-faq" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-100 hover:text-uew-scarlet transition">
                        8. Frequently Asked Questions
                    </a>
                </div>
            </aside>

            <!-- Guide Sections -->
            <main class="lg:col-span-9 space-y-10 text-sm leading-relaxed text-slate-700">

                <!-- Quick Start -->
                <section id="quick-start" class="bg-gradient-to-br from-uew-navy to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-md space-y-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 w-fit inline-block">
                        ⚡ Quick Start Guide (3 Minutes)
                    </span>
                    <h2 class="text-2xl font-black text-white">How the Library Works at a Glance</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/10 space-y-1">
                            <span class="block text-xl">1️⃣</span>
                            <h3 class="font-bold text-white text-xs">Sign In with Index Number</h3>
                            <p class="text-[11px] text-slate-300">Enter your official UEW student ID (e.g. 5201040001) and account password.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/10 space-y-1">
                            <span class="block text-xl">2️⃣</span>
                            <h3 class="font-bold text-white text-xs">Pick Course &amp; Week</h3>
                            <p class="text-[11px] text-slate-300">Browse by degree program (BIS, Accounting, Finance) and choose the lecture week (1 to 15).</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/10 space-y-1">
                            <span class="block text-xl">3️⃣</span>
                            <h3 class="font-bold text-white text-xs">Preview, Study &amp; Bookmark</h3>
                            <p class="text-[11px] text-slate-300">Preview slides in your browser, save bookmarks with personal study notes, or download.</p>
                        </div>
                    </div>
                </section>

                <!-- Chapter 1: Sign In & Onboarding -->
                <section id="step-login" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 1</span>
                        <span>&bull;</span>
                        <span>Getting Started</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">How to Sign In &amp; Set Up Your Profile</h2>
                    
                    <div class="space-y-3 pt-2">
                        <div class="flex items-start space-x-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-full bg-uew-navy text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">1</div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">Go to the Login Screen</span>
                                <p class="text-xs text-slate-600">
                                    Click <a href="{{ route('login') }}" class="text-uew-scarlet font-bold underline">Sign In</a> from the top navigation bar. If you are a student, ensure the <strong>"Student (Index No.)"</strong> tab is selected.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-full bg-uew-navy text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">2</div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">Enter Your Credentials</span>
                                <p class="text-xs text-slate-600">
                                    Type your 10-digit Student Index Number (e.g. <code class="font-mono bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-800">5201040001</code>) and your password. You can click the eye icon to see what you are typing.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-full bg-uew-navy text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">3</div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">First-Time Profile Onboarding (+25 Points)</span>
                                <p class="text-xs text-slate-600">
                                    If you are logging in for the first time, you will see a profile setup screen. Select your <strong>Degree Program</strong> (e.g. <em>BSc. Business Information Systems</em>), your <strong>Level</strong> (L100, L200, L300, etc.), and phone number. Completing this instantly awards you <strong>+25 bonus contributor points</strong>!
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Chapter 2: Finding Slides & Past Exams -->
                <section id="step-find-slides" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 2</span>
                        <span>&bull;</span>
                        <span>Searching &amp; Browsing</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">How to Find Slides &amp; Past Examination Papers</h2>
                    
                    <p class="text-xs text-slate-600">
                        The library offers multiple ways to locate study materials so you never struggle to find what you need:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="font-bold text-xs text-uew-navy block">🔍 Search Bar</span>
                            <p class="text-xs text-slate-600">
                                In the Catalog Explorer (<a href="{{ route('dashboard') }}" class="text-uew-scarlet underline">/dashboard</a>), type any course code (e.g. <code class="font-bold">BBA 111</code> or <code class="font-bold">ACT 211</code>), lecturer name, or topic like "Capital Budgeting".
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="font-bold text-xs text-uew-navy block">🎓 Degree Programs Directory</span>
                            <p class="text-xs text-slate-600">
                                Visit the <a href="{{ route('programs.index') }}" class="text-uew-scarlet underline">Programs Directory</a> to view all courses arranged by semester and academic level (L100 to PhD).
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Chapter 3: Syllabus Weeks (Weeks 1 to 15) -->
                <section id="step-weeks" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 3</span>
                        <span>&bull;</span>
                        <span>Syllabus Modules</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">How to Use Syllabus Weeks (Weeks 1 to 15)</h2>
                    
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                        <span class="font-bold block">💡 Why are slides organized into weeks?</span>
                        <p>
                            Instead of dumping 30 slides into one course list, each presentation is tagged with its syllabus module (e.g. <strong>Week 1</strong> for introduction, <strong>Week 8</strong> for mid-term revisions, and <strong>Week 15</strong> for end-of-semester exams).
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 text-xs text-slate-600">
                        <span class="font-bold text-slate-800 block">How to filter by week:</span>
                        <ol class="list-decimal list-inside space-y-1.5">
                            <li>Open the <strong>Catalog Explorer</strong> (<a href="{{ route('dashboard') }}" class="text-uew-scarlet underline">/dashboard</a>).</li>
                            <li>In the filter bar, find the <strong>"Syllabus Week"</strong> dropdown.</li>
                            <li>Select the week you are currently studying (e.g. <em>Week 4 Module</em>).</li>
                            <li>The page instantly filters down to show only the slides taught during that week!</li>
                        </ol>
                    </div>
                </section>

                <!-- Chapter 4: Downloading & IP Approvals -->
                <section id="step-downloads" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 4</span>
                        <span>&bull;</span>
                        <span>Access &amp; Permissions</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">Downloading Slides &amp; Requesting Approval</h2>
                    
                    <p class="text-xs text-slate-600">
                        To protect faculty intellectual property and prevent unauthorized distribution of academic materials, downloading some slide decks requires authorization:
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                            <span class="font-bold text-slate-800 block">How to request a download:</span>
                            <ol class="list-decimal list-inside space-y-1 text-slate-600">
                                <li>Click on the study resource to view its details.</li>
                                <li>If the download button says <strong>"Request Download Access"</strong>, click it.</li>
                                <li>Type a short, honest study purpose (e.g. <em>"Preparing for Mid-Semester Exam"</em>).</li>
                                <li>Click <strong>"Submit Request"</strong>. The department librarian receives a notification immediately.</li>
                                <li>Once approved, you will receive an in-app alert and can download the file.</li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- Chapter 5: Contributing Materials -->
                <section id="step-contribute" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 5</span>
                        <span>&bull;</span>
                        <span>Student Submissions</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">How to Upload Materials &amp; Earn +50 Points</h2>
                    
                    <p class="text-xs text-slate-600">
                        Any student or course representative who has lecture slides, past question papers, or revision summaries can submit them to help fellow scholars!
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-2">
                            <span class="font-bold text-emerald-900 block">Step-by-Step Upload Instructions:</span>
                            <ol class="list-decimal list-inside space-y-1 text-emerald-800">
                                <li>Click <strong>"+ Submit (+50 Pts)"</strong> in the top header or visit <a href="{{ route('student.contribute') }}" class="font-bold underline">/student/contribute</a>.</li>
                                <li>Enter the title of the material (e.g. <em>"Week 3 Principles of Marketing Slides"</em>).</li>
                                <li>Select the course code (e.g. <code class="bg-white px-1 py-0.5 rounded">MKT 211</code>).</li>
                                <li>Select the <strong>Syllabus Week</strong> (Week 1 to 15).</li>
                                <li>Attach your PDF or PPTX file and click <strong>"Submit for Library Review"</strong>.</li>
                                <li>Library staff review the submission. When approved, <strong>+50 points</strong> are automatically credited to your profile!</li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- Chapter 6: Gamification & Leaderboard -->
                <section id="step-gamification" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 6</span>
                        <span>&bull;</span>
                        <span>Gamification</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">Points, Badges &amp; Contributor Ranks</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-1">
                            <span class="block text-2xl">🥉</span>
                            <span class="block font-black text-slate-800 text-xs">Novice Contributor</span>
                            <span class="block text-[10px] text-slate-400 font-bold">0 – 49 Points</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-1">
                            <span class="block text-2xl">🥈</span>
                            <span class="block font-black text-amber-800 text-xs">Scholar Contributor</span>
                            <span class="block text-[10px] text-amber-600 font-bold">50 – 149 Points</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-center space-y-1">
                            <span class="block text-2xl">🥇</span>
                            <span class="block font-black text-blue-800 text-xs">Top Contributor</span>
                            <span class="block text-[10px] text-blue-600 font-bold">150 – 299 Points</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-center space-y-1">
                            <span class="block text-2xl">👑</span>
                            <span class="block font-black text-purple-800 text-xs">Master Scholar</span>
                            <span class="block text-[10px] text-purple-600 font-bold">300+ Points</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1 mt-3">
                        <span class="font-bold text-slate-800 block">How to earn points:</span>
                        <ul class="list-disc list-inside space-y-1 text-slate-600">
                            <li><strong>+25 Points</strong>: Completing first-time onboarding.</li>
                            <li><strong>+50 Points</strong>: Having a lecture slide or past exam approved by library staff.</li>
                            <li><strong>+10 Points</strong>: Writing a verified course review and rating.</li>
                        </ul>
                    </div>
                </section>

                <!-- Chapter 7: Material Request Desk -->
                <section id="step-support" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 7</span>
                        <span>&bull;</span>
                        <span>Support Desk</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">What to Do if a Course Material is Missing</h2>
                    
                    <p class="text-xs text-slate-600">
                        If a specific week's slides or a past exam paper is not yet available in the library, don't worry! You can request it directly:
                    </p>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <ol class="list-decimal list-inside space-y-1 text-slate-600">
                            <li>Visit the <strong>Material Request Desk</strong> at <a href="{{ route('requests.index') }}" class="text-uew-scarlet font-bold underline">/requests</a>.</li>
                            <li>Click <strong>"+ New Material Request"</strong>.</li>
                            <li>Select the Course Code, Academic Level, and specify what you need (e.g. <em>"Mid-Semester Past Exam 2023 for FIN 311"</em>).</li>
                            <li>Submit the request. Our library curators will source the verified material from the lecturer and upload it!</li>
                        </ol>
                    </div>
                </section>

                <!-- Chapter 8: FAQ -->
                <section id="step-faq" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs space-y-4">
                    <div class="flex items-center space-x-2 text-uew-scarlet font-bold text-xs uppercase tracking-wider">
                        <span>Chapter 8</span>
                        <span>&bull;</span>
                        <span>Help &amp; Answers</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">Frequently Asked Questions (FAQ)</h2>
                    
                    <div class="space-y-3 pt-2 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 block">Q: Can I access this website on my phone?</span>
                            <p class="text-slate-600">Yes! The website is 100% responsive and autoscales smoothly on smartphones, tablets, laptops, and desktop computers.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 block">Q: I forgot my password. How do I get back in?</span>
                            <p class="text-slate-600">On the login screen, click "Forgot Password". Enter your email or student ID, and a password reset link will be dispatched to your registered email.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 block">Q: Who approves submitted study documents?</span>
                            <p class="text-slate-600">Department librarians and administrative curators review all uploads to ensure they are virus-free, legitimate, and match official UEW syllabi.</p>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- Institutional Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2 text-white font-black text-xs">
                <div class="w-6 h-6 rounded-lg bg-uew-scarlet flex items-center justify-center text-white text-[10px]">U</div>
                <span>UEW School of Business Digital Library &middot; User Guide</span>
            </div>
            <div class="text-[11px] text-slate-500">
                &copy; {{ date('Y') }} University of Education, Winneba. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
