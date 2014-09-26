<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/', function()
//{
//	return View::make('hello');
//});

//Route::get('/', 'UserController@showProfile');

Route::resource('/', 'IndexController',
    array('only' => array('index')));


Route::get('/', 'PageController@index');
Route::get('Cv', 'PageController@cv');
Route::get('Code', 'PageController@code');
Route::get('Photo', 'PageController@photo');
Route::get('Cv/Skills', 'PageController@skills');

Route::get('Games', 'GamesController@index');