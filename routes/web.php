<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});






//Route::group(["namespace"=>"Admin"],function() {
//
//
//
//});

Route::group(['namespace' => 'Admin'], function () {
    Route::get('/login', 'LoginController@loginView')->name('login-view');
    Route::get('/logout', 'LoginController@logout')->name('logout');
    Route::post('/login-post', 'LoginController@loginPost')->name('login-post');


Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

    Route::get('/admin-dashboard', 'DashboardController@index')->name('admin-dashboard');
    Route::get('/edit-profile/{userId}', 'DashboardController@editProfile')->name('edit-profile');
    Route::post('/edit-profile-post', 'DashboardController@editProfile_post')->name('edit-profile-post');

    Route::get('/user-detail/{userId}', 'UserDetailController@userDetails')->name('user-detail');

    Route::get('/user-detail/{userid}/{option}', 'UserDetailController@showUserData')->name('show-user-data');

    Route::get('/chat', 'DashboardController@chat')->name('chat');

    Route::get('/feedbacks', 'DashboardController@feedbacks')->name('feedbacks');



    });
});



