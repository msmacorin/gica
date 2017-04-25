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

Route::get('/posts/view', 'PostController@getView');
Route::get('/posts/nearby', 'PostController@getNearby');
Route::post('/posts/add', 'PostController@postAdd');
Route::get('/post-types/types', 'PostTypeController@getTypes');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/contact/send', 'ContactController@send');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/admin', function() {
        return view('admin');
    });
    Route::get('/admin/posts', 'PostController@getPosts');
    Route::post('/admin/posts', 'PostController@postPosts');
});
