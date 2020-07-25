<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Register
Route::post('/register', 'Auth\RegisterController@register')->name('register');

// Login
Route::post('/login', 'Auth\LoginController@login')->name('login');

// Logout
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Logged in user
Route::get('/user', fn() => Auth::user())->name('user');

// Upload image
Route::post('/photos', 'PhotoController@create')->name('photo.create');

// List images
Route::get('/photos', 'PhotoController@index')->name('photo.index');

//  Download image
Route::get('/photos/{photo}/download', 'PhotoController@download');

// Photo detail
Route::get('/photos/{id}', 'PhotoController@show')->name('photo.show');

// Comment
Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');


// Like
Route::put('/photos/{id}/like', 'PhotoController@like')->name('photo.like');

// Remove like
Route::delete('/photos/{id}/like', 'PhotoController@unlike');

// Refresh token
Route::get('/refresh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});