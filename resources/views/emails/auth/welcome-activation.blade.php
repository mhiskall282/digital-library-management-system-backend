@extends('emails.layout')

@section('content')
    <h2 style="font-size: 18px; color: #0F172A; margin-top: 0;">Welcome to the UEW School of Business Repository!</h2>

    <p>Dear <strong>{{ $user->first_name }}</strong>,</p>

    <p>Your student account has been created on the <strong>University of Education, Winneba Digital Library Management System</strong>. You can now access verified lecture slide archives, past examination papers, syllabus materials, and personalized study notes for <strong>{{ $user->level }}</strong>.</p>

    <div class="credential-box">
        <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748B; display: block; margin-bottom: 8px;">Initial Access Credentials</span>
        <p style="margin: 4px 0; font-size: 13px;"><strong>Student Index No / Email:</strong> {{ $user->student_id ?: $user->email }}</p>
        <p style="margin: 4px 0; font-size: 13px;"><strong>Temporary Password:</strong> <code style="background: #E2E8F0; padding: 2px 6px; border-radius: 4px; font-family: monospace;">{{ $tempPassword }}</code></p>
        <p style="margin: 4px 0; font-size: 13px;"><strong>Enrolled Program:</strong> {{ $user->program }}</p>
    </div>

    <div style="text-align: center;">
        <a href="{{ url('/login') }}" class="btn">
            Sign In & Complete Onboarding &rarr;
        </a>
    </div>

    <p style="font-size: 12px; color: #64748B;">Upon your first sign-in, you will be prompted to complete your scholar profile and set your permanent password.</p>
@endsection
