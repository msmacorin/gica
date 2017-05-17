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

Route::get('/public/', function () {
    return view('home');
});

Route::get('/public/posts/nearby', 'PostController@getNearby');
Route::get('/public/post-types/types', 'PostTypeController@getTypes');

Route::get('/public/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/public/login', 'Auth\LoginController@login');
Route::get('/public/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/public/contact/send', 'ContactController@send');

// facebook
Route::get('/public/facebook/redirect', 'SocialAuthController@facebookRedirect');
Route::get('/public/facebook/callback', 'SocialAuthController@facebookCallback');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/public/posts/view', 'PostController@getView');
    Route::post('/public/posts/add', 'PostController@postAdd');

    Route::get('/public/admin', function() {
        if (auth()->user()->administrator) {
            return view('admin-dashboard');
        }
        return redirect('/public/');
    });
    Route::get('/public/admin/posts', function() {
        if (auth()->user()->administrator) {
            return view('admin-posts');
        }
        return redirect('/public/');
    });
    Route::get('/public/admin/users', function() {
        if (auth()->user()->administrator) {
            return view('admin-users');
        }
        return redirect('/public/');
    });

    Route::get('/public/admin/get-posts', 'PostController@getPosts');
    Route::post('/public/admin/update-posts', 'PostController@postPosts');
    Route::get('/public/admin/get-users', 'UserController@getUsers');
    Route::post('/public/admin/update-users', 'UserController@postUsers');
});
