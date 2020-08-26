<?php

use Illuminate\Support\Facades\Route;

//Public Routes
Route::get( '/me', 'User\MeController@getMe' );

//Route Group  for authenticated user only
Route::group( ['middleware' => ['auth:api']], function () {
    Route::post( '/logout', 'Auth\LoginController@logout' );
} );

//Route Group for guest only
Route::group( ['middleware' => ['guest:api'], 'namespace' => 'Auth'], function () {
    Route::post( 'register', 'RegisterController@register' );
    Route::post( 'verification/verify', 'VerificationController@verify' )->name( 'verification.verify' );
    Route::post( 'verification/resend', 'VerificationController@resend' );
    Route::post( '/login', 'LoginController@login' );

    Route::post( '/password/email', 'ForgotPasswordController@sendResetLinkEmail' );
    Route::post( '/password/reset', 'ResetPasswordController@reset' );

} );
