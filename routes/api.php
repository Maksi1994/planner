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
  Route::post('/regist', 'TasksController@create');
  Route::post('/login', 'TasksController@edit');
  Route::get('/logout', 'TasksController@delete');
});

Route::group([
//  'middleware' => ['auth:api'],
  'prefix' => 'tasks'
], function() {
  Route::post('/save-daily-tasks', 'TasksController@saveDailyTasks');
  Route::post('/get-tasks', 'TasksController@getTasks');
  Route::get('/delete/{id}', 'TasksController@delete');
});
