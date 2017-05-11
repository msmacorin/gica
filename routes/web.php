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
    return view('home');
});

Route::get('/posts/nearby', 'PostController@getNearby');
Route::get('/post-types/types', 'PostTypeController@getTypes');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/contact/send', 'ContactController@send');

// facebook
Route::get('/facebook/redirect', 'SocialAuthController@facebookRedirect');
Route::get('/facebook/callback', 'SocialAuthController@facebookCallback');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/posts/view', 'PostController@getView');
    Route::post('/posts/add', 'PostController@postAdd');

    Route::get('/admin', function() {
        if (auth()->user()->administrator) {
            return view('admin-dashboard');
        }
        return redirect('/');
    });
    Route::get('/admin/posts', function() {
        if (auth()->user()->administrator) {
            return view('admin-posts');
        }
        return redirect('/');
    });
    Route::get('/admin/users', function() {
        if (auth()->user()->administrator) {
            return view('admin-users');
        }
        return redirect('/');
    });

    Route::get('/admin/get-posts', 'PostController@getPosts');
    Route::post('/admin/update-posts', 'PostController@postPosts');
    Route::get('/admin/get-users', 'UserController@getUsers');
    Route::post('/admin/update-users', 'UserController@postUsers');
});
