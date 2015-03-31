<?php

use Helper\Breadcrumbs;
use Vortex\Vortex;

class VortexController extends \BaseController
{
    const SESSION_DATA = 'vortex.game';

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
     * Main page
     *
     * @return Response
     */
    public function index()
    {
        Session::forget(self::SESSION_DATA);
        View::share('mapSize', Vortex::MAP_SIZE);
        return View::make('games.vortex.index');
    }

    /**
     * Ajax page
     *
     * @return Response
     */
    public function action()
    {
        $x = (int) Input::get('x');
        $y = (int) Input::get('y');
        $game = $this->getGame($x, $y);

        $game->cellActivate($x, $y);

        $results = $game->getChangesToRender();

        $gameData = $game->export();
        $this->saveGame($gameData);

        return json_encode(['map' => $results]);
    }

    protected function getGame($x = null, $y = null) 
    {
        $gameData = Session::get(self::SESSION_DATA, null);
        if ($gameData) {
            $game = Vortex::createFromArray($gameData);
        } else {
            $game = new Vortex();
            $game->init($x, $y);
            $game->createGame();
        }
        return $game;
    }

    protected function saveGame($gameData)
    {
        Session::forget(self::SESSION_DATA);
        Session::put(self::SESSION_DATA, $gameData);
    }
}