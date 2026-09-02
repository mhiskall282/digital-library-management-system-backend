@extends('emails.layout')

@section('content')
    <div style="background-color: #FEF2F2; border-left: 4px solid #EF4444; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
        <span style="font-weight: 800; color: #991B1B; font-size: 13px;">SECURITY NOTICE</span>
        <p style="margin: 4px 0 0; font-size: 12px; color: #B91C1C;">A security-sensitive action was performed on your UEW library account.</p>
    </div>

    <p>Dear <strong>{{ $user->name }}</strong>,</p>

    <p>We are notifying you that the following action occurred on your account:</p>

    <div class="credential-box">
        <p style="margin: 4px 0; font-size: 13px;"><strong>Event:</strong> {{ $actionTitle }}</p>
        <p style="margin: 4px 0; font-size: 13px;"><strong>Details:</strong> {{ $actionDetails }}</p>
        @if($ipAddress)
            <p style="margin: 4px 0; font-size: 13px;"><strong>Client IP Address:</strong> <code style="font-family: monospace;">{{ $ipAddress }}</code></p>
        @endif
        <p style="margin: 4px 0; font-size: 13px;"><strong>Timestamp:</strong> {{ now()->toFormattedDateString() }} at {{ now()->toTimeString() }} GMT</p>
    </div>

    <p style="font-size: 13px; color: #475569;">If you performed this action, no further steps are required. If this was not you, please immediately log in and change your password, or alert the library security desk at <a href="mailto:library@uew.edu.gh" style="color: #C41E3A;">library@uew.edu.gh</a>.</p>

    <div style="text-align: center;">
        <a href="{{ url('/profile') }}" class="btn">
            Review Account Security &rarr;
        </a>
    </div>
@endsection
