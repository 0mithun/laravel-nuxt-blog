 <?php

use Illuminate\Support\Facades\Route;

//Public Routes
Route::get( '/me', 'User\MeController@getMe' );
Route::get('designs','Design\DesignController@index');
Route::get('designs/{id}','Design\DesignController@findDesign');
Route::get('users','User\UserController@index');


//Route Group  for authenticated user only
Route::group( ['middleware' => ['auth:api']], function () {
    Route::post( '/logout', 'Auth\LoginController@logout' );

    Route::put('settings/profile','User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    //Designs
    Route::post('designs','Design\UploadController@upload');
    Route::put('designs/{id}','Design\DesignController@update');
    Route::delete('designs/{id}','Design\DesignController@destroy');


    //Comments
    Route::post('designs/{id}/comments','Design\CommentController@store');
    Route::put('comment/{id}','Design\CommentController@update');
    Route::delete('comment/{id}','Design\CommentController@destroy');

    //Likes and unlike
    Route::post('designs/{id}/like','Design\DesignController@like');
    Route::get('designs/{id}/liked','Design\DesignController@checkIfUserHasLiked');

    Route::post('comments/{id}/like','Design\CommentController@like');
    Route::get('comments/{id}/liked','Design\CommentController@checkIfUserHasLiked');

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
