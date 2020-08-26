<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as NotificationsVerifyEmail;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends NotificationsVerifyEmail {
    // use Queueable;

    protected function verificationUrl( $notifiable ) {
        $appUrl = config( 'app.client_url', conig( 'app.app_url' ) );

        $url = URL::temporarySignedRoute( 'verification.verify', now()->addMinutes( 60 ), ['user' => $notifiable->id] );

        return str_replace( url( '/api' ), $appUrl, $url );
    }

}
