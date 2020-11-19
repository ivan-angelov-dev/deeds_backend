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


Route::get('/', 'PageController@showFirstpage');

Route::name('auth.')->prefix('auth')->group(function () {

    Route::get('login', 'AuthController@showAdminLoginPage')->name('login');
    Route::post('login', 'AuthController@doAdminLogin')->name('login');

});

Route::middleware(['admin_auth'])->group(function () {

    Route::get('/dashboard', 'AdminController@showDashboardPage');
    Route::get('/user', 'AdminController@showUserlistPage');
    Route::get('/user/{id}', 'AdminController@showUserdetailPage');
    Route::get('/offer', 'AdminController@showOfferlistPage');
    Route::get('/offer/{id}', 'AdminController@showOfferdetailPage');
    Route::get('/category', 'AdminController@showCategoryPage');
    Route::get('/category/{id}', 'AdminController@showCategorydetailPage');
    Route::get('/settings', 'AdminController@showSettingsPage');
    Route::get('/logout', 'AuthController@logout');
    Route::post('/editProfile', 'AdminController@editProfile');
    Route::get('user/profile', function () {
        // Uses first & second Middleware
    });
});
