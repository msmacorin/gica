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
Route::get('/post/{latlon?}', function ($latlon=null) {
    $ar = explode(',', $latlon);
    return view('post', ['latitude' => $ar[0], 'longitude' => $ar[1],]);
});

Route::get('/posts/nearby', 'PostController@getNearby');
Route::post('/posts/add', 'PostController@postAdd');
Route::get('/post-types/types', 'PostTypeController@getTypes');