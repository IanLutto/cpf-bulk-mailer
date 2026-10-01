<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $policyFiles
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Request for Your Feedback on the Scheme's Draft Policies by 7th October 2026",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.member-consultation',
        );
    }

    public function attachments(): array
    {
        return array_map(
            fn (string $path) => Attachment::fromPath($path)
                ->as(basename($path))
                ->withMime('application/pdf'),
            $this->policyFiles
        );
    }
}
