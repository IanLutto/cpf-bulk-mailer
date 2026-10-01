<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName = 'Recipient'
    ) {}

    public function build()
    {
        return $this
            ->subject('Important Notice from CPF')
            ->view('emails.bulk-notice')
            ->attach(
                storage_path(
                    'app/private/documents/Ian Lutomiah_ADA.pdf'
                ),
                [
                    'as' => 'CPF-Notice.pdf',
                    'mime' => 'application/pdf',
                ]
            );
    }
}
