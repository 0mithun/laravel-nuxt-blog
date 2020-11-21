 <?php

use Illuminate\Support\Facades\Route;

//Public Routes
Route::get( '/me', 'User\MeController@getMe' );
Route::get('designs','Design\DesignController@index');
Route::get('designs/{id}','Design\DesignController@findDesign');
Route::get('designs/slug/{slug}','Design\DesignController@findDesignBySlug');

Route::get('users','User\UserController@index');
Route::get('user/{username}','User\UserController@findByUserName');
Route::get('users/{id}/designs','Design\DesignController@findDesignByUserId');

//Team
 Route::get('teams/slug/{slug}','Teams\TeamsController@findBySlug');
 Route::get('teams/{id}/designs','Design\DesignController@getForTeam');


 //Search Designs
 Route::get('search/designs','Design\DesignController@search');
 Route::get('search/designers','User\UserController@search');

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

    //Teams
    Route::post('teams','Teams\TeamsController@store');
    Route::put('teams/{id}','Teams\TeamsController@update');
    Route::delete('teams/{id}','Teams\TeamsController@destroy');
    Route::delete('teams/{id}/user/{user_id}','Teams\TeamsController@removeFromTeam');

    Route::get('teams/{id}','Teams\TeamsController@findById');
    Route::get('teams','Teams\TeamsController@index');
    Route::get('users/teams','Teams\TeamsController@fetchUserTeams');


    //Invitations
    Route::post('invitations/{teamId}','Teams\InvitationsController@invite');
    Route::post('invitations/{id}/resend','Teams\InvitationsController@resend');
    Route::post('invitations/{id}/respond','Teams\InvitationsController@respond');
    Route::delete('invitations/{id}','Teams\InvitationsController@destroy');

    //Chats
    Route::post('chats','Chats\ChatController@sendMessage');
    Route::get('chats','Chats\ChatController@getUserChat');
    Route::get('chats/{id}/messages','Chats\ChatController@getChatMesages');
    Route::put('chats/{id}/mark-as-read','Chats\ChatController@markAsRead');
    Route::delete('messages/{id}','Chats\ChatController@destroyMessages');


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
