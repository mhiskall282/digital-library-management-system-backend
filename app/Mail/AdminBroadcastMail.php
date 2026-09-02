<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public string $subjectTitle,
        public string $content
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "UEW Library Announcement: {$this->subjectTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.broadcast',
        );
    }
}
