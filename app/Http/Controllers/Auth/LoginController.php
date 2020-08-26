<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {
    /*
    |-----------------------------------------P---------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function attemptLogin( Request $request ) {
        $token = $this->guard()->attempt( $this->credentials( $request ) );

        if ( !$token ) {
            return false;
        }

        $user = $this->guard()->user();
        if ( $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail() ) {
            return false;
        }

        $this->guard()->setToken( $token );

        return true;
    }

    public function sendLoginResponse( Request $request ) {
        $this->clearLoginAttempts( $request );
        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get( 'exp' );

        return response()->json( [
            'token'      => $token,
            'token_type' => 'bearer',
            'ecpires_in' => $expiration,
        ] );
    }

    public function sendLoginFailedResponse( Request $request ) {
        $user = $this->guard()->user();

        if ( $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail() ) {
            return response()->json( ['errors' => ['verification', 'You must verify your email account']] );
        }

        throw ValidationException::withMessages( [$this->username() => 'Invalid Credentials'] );
    }
}
