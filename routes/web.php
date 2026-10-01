<?php

use App\Mail\TestMail;
use Illuminate\Support\Facades\Route;

Route::get('/test-email', function () {
    abort_unless(app()->environment('local'), 403);

    Mail::to('ianlutomiah@gmail.com')
        ->send(new TestMail());

    return 'Test email sent successfully!';
});
