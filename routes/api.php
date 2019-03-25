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

Route::group([
  'prefix' => 'users'
], function() {
  Route::post('/regist', 'UsersController@regist');
  Route::post('/login', 'UsersController@login');
  Route::get('/accept-registration/{token}', 'UsersController@acceptRegistration');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/get-user', 'UsersController@getUser');
        Route::get('/logout', 'UsersController@logout');
    });

    Route::post('/create-reser-password', 'UsersController@createResetPassword');
    Route::get('/accept-reset-password/{token}', 'UsersController@acceptResetPassword');
    Route::post('/reset-password', 'UsersController@resetPassword');
});

Route::group([
//  'middleware' => ['auth:api'],
  'prefix' => 'tasks'
], function() {
  Route::post('/save-daily-tasks', 'TasksController@saveDailyTasks');
  Route::post('/get-tasks', 'TasksController@getTasks');
  Route::get('/delete/{id}', 'TasksController@delete');
});
