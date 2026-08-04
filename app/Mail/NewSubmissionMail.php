<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $type,
        public string $name,
        public string $details,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[NGuliner] {$this->type} baru dari {$this->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-submission',
        );
    }
}
