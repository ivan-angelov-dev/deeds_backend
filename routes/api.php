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

Route::prefix('auth')->group(function () {
    Route::post('/check-email', 'APIController@checkEmail');
    Route::post('/check-password', 'APIController@checkPassword');
});

Route::prefix('signup')->group(function () {

    Route::post('/step-1', 'APIController@signupStep1');

    Route::middleware('api_auth')->group(function () {

        Route::prefix('step-2')->group(function () {
            Route::post('', 'APIController@signupStep2');
            Route::post('/email/send-otp', 'APIController@signupStep2SendEmailOTP');
            Route::post('/mobile/send-otp', 'APIController@signupStep2SendMobileOTP');
        });
        Route::post('/step-3', 'APIController@signupStep3');
        Route::post('/step-4', 'APIController@signupStep4');
        Route::post('/finish', 'APIController@signupFinish');

    });


});


Route::middleware('api_auth')->group(function () {

    Route::prefix('browse')->group(function () {
        Route::post('nearby', 'APIController@getNearbyOffers');
        Route::post('category', 'APIController@getOffersByCategory');
        Route::post('offer-detail', 'APIController@getOfferDetail');
        Route::post('search', 'APIController@getSearchOffer');
        Route::post('user-detail', 'APIController@getUserDetail');
    });

    Route::prefix('offer')->group(function () {

        Route::post('create', 'APIController@createOffer');
        Route::post('edit', 'APIController@editOffer');
        Route::post('get-mine', 'APIController@getMyOffers');
        Route::post('show-interest', 'APIController@showInterest');

        Route::post('get-category', 'APIController@getCategory');


    });

    Route::prefix('profile')->group(function () {

        Route::post('upload-photo', 'APIController@profileUploadPhoto');
        Route::post('update', 'APIController@profileUpdate');

    });

    Route::prefix('settings')->group(function () {

        Route::prefix('email')->group(function () {

            Route::post('send-otp', 'APIController@sendEmailOTP');
            Route::post('update', 'APIController@updateEmail');

        });


        Route::prefix('mobile')->group(function () {

            Route::post('send-otp', 'APIController@sendMobileOTP');
            Route::post('update', 'APIController@updateMobile');

        });

        Route::post('interested-gender/update', 'APIController@updateInterestedGender');
        Route::post('max-distance/update', 'APIController@updateMaxDistance');
        Route::post('age-range/update', 'APIController@updateAgeRange');
        Route::post('all/update', 'APIController@updateAllSettings');

    });


});
