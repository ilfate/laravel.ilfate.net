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

//Route::resource('/', 'IndexController',
 //   array('only' => array('index')));


Route::get('/', 'PageController@index');
Route::get('Photo', 'PageController@photo');
Route::get('Cv', 'PageController@cv');
Route::get('Cv/Skills', 'PageController@skills');

Route::get('Code', 'CodeController@index');
Route::get('Code/Engine', 'CodeController@engine');
Route::get('Code/Stars', 'CodeController@stars');

Route::get('Games', 'GamesController@index');
Route::get('RobotRock', 'GamesController@robotRock');
Route::get('GameTemplate', 'GamesController@gameTemplate');

Route::get('MathEffect', 'MathEffectController@index');
Route::post('MathEffect/save', array('before' => 'csrf', 'uses' => 'MathEffectController@save'));
Route::post('MathEffect/saveName', array('before' => 'csrf', 'uses' => 'MathEffectController@saveName'));
Route::get('MathEffect/stats', 'MathEffectController@statistic');

Route::get('tcg', 'TcgController@index');
Route::get('tcg/clear', 'TcgController@dropGame');
Route::get('tcg/action', 'TcgController@action');
Route::post('tcg/action', 'TcgController@actionAjax');

Route::get('tcgb', 'TcgController@bot');
