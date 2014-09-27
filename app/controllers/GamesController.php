<?php

class GamesController extends \BaseController {

	/**
	 * Display a listing of the games.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('games.index');
	}


	/**
	 * RobotRock game
	 *
	 * @return Response
	 */
	public function robotRock()
	{
		return View::make('games.robotRock.index');
	}
	/**
	 * js game template
	 *
	 * @return Response
	 */
	public function gameTemplate()
	{
		return View::make('games.gameTemplate');
	}
}
