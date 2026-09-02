<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $tempPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to UEW Digital Library — Account Activation & Sign In',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.welcome-activation',
        );
    }
}
