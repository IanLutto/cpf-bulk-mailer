<?php

namespace App\Jobs;

use App\Mail\TestMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendBulkEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $email,
        public string $name
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new TestMail($this->name));
    }
}
