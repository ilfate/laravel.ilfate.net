<?php

use Helper\Breadcrumbs;

class GamesController extends \BaseController
{
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @param Breadcrumbs $breadcrumbs
     */
    public function __construct(Breadcrumbs $breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }

	/**
	 * Display a listing of the games.
	 *
	 * @return Response
	 */
	public function index()
	{
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Games');
        return View::make('games.index');
	}


	/**
	 * RobotRock game
	 *
	 * @return Response
	 */
	public function robotRock()
	{
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'RobotRock');
		return View::make('games.robotRock.index');
	}

	/**
	 * js game template
	 *
	 * @return Response
	 */
	public function gameTemplate()
	{
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Game Template');
		return View::make('games.gameTemplate');
	}

	/**
	 * js game template
	 *
	 * @return Response
	 */
	public function mathEffect()
	{
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Math Effect');
		return View::make('games.mathEffect');
	}
}
