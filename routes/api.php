<?php

use Illuminate\Support\Facades\Route;

//Public Routes

//Route Group  for authenticated user only
Route::group( ['middleware' => ['auth:api']], function () {
} );

//Route Group for guest only
Route::group( ['middleware' => ['guest:api']], function () {
    Route::group( ['namespace' => 'Auth'], function () {
        Route::post( 'register', 'RegisterController@register' );
        Route::post( 'verification/verify', 'VerificationController@verify' )->name( 'verification.verify' );
        Route::post( 'verification/resend', 'VerificationController@resend' );
    } );

} );