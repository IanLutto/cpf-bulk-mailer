<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName
    ) {}

    public function build()
    {
        return $this->subject('Important Notice from CPF')
            ->view('emails.bulk-notice')
            ->attach(
                storage_path('app/public/documents/cpf-notice.pdf'),
                [
                    'as' => 'CPF-Notice.pdf',
                    'mime' => 'application/pdf',
                ]
            );
    }
}
