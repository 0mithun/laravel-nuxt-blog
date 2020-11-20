<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as NotificationsResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends NotificationsResetPassword {
    // use Queueable;

    public function toMail( $notifiable ) {
        $url = url( config( 'app.client_url' ) . '/password/reset/' . $this->token ) . '?email=' . urlencode( $notifiable->email );

        return ( new MailMessage )
            ->line( 'You are receiving this email because we receive a password reset request for your account' )
            ->action( 'Reset Password', $url )
            ->line( 'If you did not request a password reset, no further action required.' );
    }

}
