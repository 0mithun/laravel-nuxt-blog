<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
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

    //use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware( 'signed' )->only( 'verify' );
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
        if ( !$user ) {
            return response()->json( ['errors' => [
                'message' => 'Invalid Verification Link ',
            ]], 422 );
        }

        //Check if the url is a valid signed URL
        if ( !URL::hasValidSignature( $request ) ) {
            return response()->json( ['errors' => [
                'message' => 'Invalid Verification Link or signature',
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
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend( Request $request ) {
        $request->validate( ['email' => 'email|required'] );

        $user = User::where( 'email', $request->email )->first();

        //Check if email exists
        if ( !$user ) {
            return response()->json( ['errors' => [
                'message' => 'No user could be found with this email address.',
            ]], 404 );
        }

        //Check if the user has already verified account
        if ( $user->hasVerifiedEmail() ) {
            return response()->json( ['errors' => [
                'message' => 'Email Address Already Verified',
            ]], 422 );
        }

        $user->sendEmailVerificationNotification();

        return response()->json( [
            'status' => 'New Verification Email Send To Your Email.',
        ], 200 );
    }
}
