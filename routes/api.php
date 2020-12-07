<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix("v1")->namespace('ApiController')->group(function () {
    Route::post('signup','AuthController@signup');
    Route::post('signin','AuthController@signin');
    Route::post('forget-email','AuthController@forgetemail');
    Route::post('forget-token','AuthController@forgettoken');
    Route::post('reset','AuthController@restpassword');

    Route::post('uploadfile','FileUploading@fileuploading');

    Route::group(['middleware' => ['authuser']], function () {

        Route::post('api-logout','AuthController@apiLogout')->name('api-logout');

        Route::post('delete-account','AuthController@deleteAccount');
        Route::post('send-feedback','AuthController@sendFeedback');

        Route::post('addpost','PostController@createpost');
        Route::post('addlike','PostController@addlike');

        Route::post('wallposts','PostController@wallposts')->name('wallposts');


        Route::post('viewpost','PostController@viewpost');
        Route::post('addcomment','PostController@addcomment');
        Route::post('aboutme','AboutController@aboutme');
        Route::post('aboutpet','AboutController@aboutpet');
        Route::post('pets-listing','AboutController@petsListing')->name('pet-listing');
        Route::post('delete-pet','AboutController@deletePet');
        Route::post('edit-pet','AboutController@editPet');
        Route::post('questionnaire','QuestionnaireController@adddata');

        Route::post('user-wall-posts','PostController@getUserWallPosts');
        Route::post('get-user-profile','ProfileController@getUserProfile')->name('get-user-profile');
        Route::post('userprofile-edit','ProfileController@userProfileEdit');
        Route::post('user-by-id','ProfileController@userById');

        Route::post('users/search','ProfileController@searchUsers')->name('search-users');

        Route::post('report','PostController@reportPost');

        //new routes below not included into docs

        Route::post('listing','TabListingController@getList');
        Route::post('home','ProfileController@allProfile')->name('home');


        Route::post('notifications', 'NotificationController@getNotification')->name('notifications');
        Route::post('unseen-single-notifi', 'NotificationController@unseenSingleNotification')->name('unseen-single-notification');
        Route::post('clear-notification', 'NotificationController@clearNotification');
        Route::post('count-notification', 'NotificationController@countNotification');

        // Actions on Profile by users
        Route::post('action','ActionOnProfileController@like_action');
        Route::post('create-alert','AlertController@create_alert');
        Route::post('alerts','AlertController@alerts');
        Route::post('delete-alert','AlertController@delete_alert');
        Route::post('edit-alert','AlertController@edit_alert');
        Route::post('edit-alert-image','AlertController@delete_alert_image');


        Route::post('firebase-notify','NotificationController@firebaseNotify');


        Route::post('conversations','ChatController@getConversations')->name('conversations');
        Route::post('get-chat','ChatController@getChat')->name('get-chat');

        Route::post('delete-image','AuthController@deleteImage')->name('delete-image');



    });
});
