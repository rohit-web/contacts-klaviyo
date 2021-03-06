<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('contacts', ['uses'=>'ContactController@contacts', 'as'=>'contact.index']);
Route::get('contacts', ['uses'=>'ContactController@contacts', 'as'=>'contact.index']);
Route::get('contacts/{id}', ['uses'=>'ContactController@show', 'as'=>'contact.show']);
Route::post('contacts', ['uses'=>'ContactController@store', 'as'=>'contacts.create']);
Route::put('contacts', ['uses'=>'ContactController@update', 'as'=>'contacts.update']);
Route::post('upload', ['uses'=>'ContactController@upload', 'as'=>'contacts.upload']);


Route::get('track', ['uses'=>'ContactController@trackEvent', 'as'=>'track.event']);