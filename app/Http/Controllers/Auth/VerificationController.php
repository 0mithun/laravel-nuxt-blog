<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
     */

    use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'signed' )->only( 'verify' );
        $this->middleware( 'throttle:6,1' )->only( 'verify', 'resend' );
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify( Request $request, User $user ) {
        //Check if the url is a valid signed URL
        if ( !URL::hasValidSignature( $request ) ) {
            return response()->json( ['errors' => [
                'message' => 'Invalid Verification Link',
            ]], 422 );
        }

        //Check if the user has already verified account
        if ( $user->hasVerifiedEmail() ) {
            return response()->json( ['errors' => [
                'message' => 'Email Address Already Verified',
            ]], 422 );
        }

        $user->markEmailAsVerified();
        event( new Verified( $user ) );

        return response()->json( [
            'message' => 'Email Address Verify Successfully',
        ], 200 );

    }

    /**
     * The user has been verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function verified( Request $request ) {
        //
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend( Request $request ) {
        if ( $request->user()->hasVerifiedEmail() ) {
            return $request->wantsJson()
            ? new Response( '', 204 )
            : redirect( $this->redirectPath() );
        }

        $request->user()->sendEmailVerificationNotification();

        return $request->wantsJson()
        ? new Response( '', 202 )
        : back()->with( 'resent', true );
    }
}
