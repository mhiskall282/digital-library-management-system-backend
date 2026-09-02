@extends('emails.layout')

@section('content')
    <span class="badge">OFFICIAL ANNOUNCEMENT</span>

    <h2 style="font-size: 18px; color: #0F172A; margin: 12px 0 16px;">{{ $subjectTitle }}</h2>

    <p>Dear <strong>{{ $recipient->first_name }}</strong>,</p>

    <div style="font-size: 13px; line-height: 1.7; color: #334155; margin: 16px 0;">
        {!! nl2br(e($content)) !!}
    </div>

    <div style="text-align: center; margin-top: 24px;">
        <a href="{{ url('/student/hub') }}" class="btn">
            Open Your Student Study Hub &rarr;
        </a>
    </div>
@endsection
