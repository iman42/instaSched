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

Auth::routes();

Route::get('/manage', 'AccountsController@index');
Route::post('/manage', 'AccountsController@addAccount');
Route::get('/delete/account/{id}', 'AccountsController@deleteAccount');
Route::get('/activities', 'ActivitiesController@index');
Route::get('/activities/add', 'ActivitiesController@create');
Route::post('/activities/add', 'ActivitiesController@store');
Route::get('/delete/activity/{id}', 'ActivitiesController@delete');
Route::get('/admin/keys', 'AccountsController@keyindex');
Route::get('/admin/key/add', 'AccountsController@keycreate');
