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

Route::get('MathEffect-2', 'MathEffectTwoController@index');

Route::get('GuessSeries', 'GuessGameController@index');
Route::get('GuessSeries/stats', 'GuessGameController@stats');
Route::post('GuessSeries/gameStarted', 'GuessGameController@gameStarted');
Route::post('GuessSeries/answer', 'GuessGameController@answer');
Route::post('GuessSeries/ability', 'GuessGameController@ability');
Route::post('GuessSeries/timeIsOut', 'GuessGameController@timeIsOut');
Route::post('GuessSeries/saveName', 'GuessGameController@saveName');

Route::get('GuessSeries/admin', 'GuessGameAdminController@index');
Route::any('GuessSeries/admin/addSeries', 'GuessGameAdminController@addSeries');
Route::any('GuessSeries/admin/liveStream', 'GuessGameAdminController@liveStream');
Route::any('GuessSeries/admin/addImage', 'GuessGameAdminController@addImage');
Route::any('GuessSeries/admin/generateImages', 'GuessGameAdminController@generateImages');
Route::any('GuessSeries/admin/series/{id}', 'GuessGameAdminController@seriesInfo');
Route::any('GuessSeries/admin/series/toggle/{id}', 'GuessGameAdminController@toggleActive');
Route::any('GuessSeries/admin/deleteImage/{id}', 'GuessGameAdminController@deleteImage');

Route::get('test', 'TestController@index');

Route::get('Vortex', 'VortexController@index');
Route::post('Vortex/action', 'VortexController@action');





Route::get('tcg/me', 'TcgPlayerController@index');
Route::get('tcg/register', 'TcgPlayerController@registerForm');
Route::post('tcg/register/submit', 'TcgPlayerController@registerSubmit');
Route::get('tcg/login', 'TcgPlayerController@login');
Route::post('tcg/login/submit', 'TcgPlayerController@loginSubmit');
Route::get('tcg/logout', 'TcgPlayerController@logout');

Route::get('tcg/createDeck', 'TcgCardController@createDeckForm');
Route::post('tcg/createDeck/submit', 'TcgCardController@createDeck');
Route::get('tcg/changeDeck', 'TcgCardController@changeDeckForm');
Route::post('tcg/changeDeck/submit', 'TcgCardController@changeDeck');
Route::get('tcg/deck/{deckId}', 'TcgCardController@deck');
Route::post('tcg/saveDeck/{deckId}', 'TcgCardController@deckSaveCards');

Route::get('tcg/findBattle', 'TcgBattleController@findBattlePage');
Route::get('tcg/joinQueue/{deckId}', 'TcgBattleController@joinQueue');
Route::get('tcg/leaveQueue', 'TcgBattleController@leaveQueue');
Route::post('tcg/checkQueue', 'TcgBattleController@checkQueue');

Route::get('tcg/battle', 'TcgBattleController@battle');
Route::post('tcg/battle/action', 'TcgBattleController@battleAction');

Route::get('tcg/addBooster', 'TcgCardController@openBooster');

Route::get('tcg/test', 'TcgController@index');
Route::get('tcg/test/player2', 'TcgController@bot');
Route::get('tcg/test/clear', 'TcgController@dropGame');
Route::get('tcg/test/action', 'TcgController@action');
Route::post('tcg/test/action', 'TcgController@actionAjax');

