<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as NotificationsVerifyEmail;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends NotificationsVerifyEmail {
    // use Queueable;

    protected function verificationUrl( $notifiable ) {
        $appUrl = config('app.client_url', config('app.url'));

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->minute(60),
            ['user' => $notifiable->id]
        );

        return str_replace(url('/api'), $appUrl, $url);
    }

}
