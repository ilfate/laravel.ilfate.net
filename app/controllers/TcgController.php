<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Deck;
use Tcg\Card;
use Tcg\Game;

class TcgController extends \BaseController
{
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var Game
     */
    protected $game;

    /**
     * @param Breadcrumbs $breadcrumbs
     */
    public function __construct(Breadcrumbs $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Display a listing of the games.
     *
     * @return Response
     */
    public function index()
    {
        $this->breadcrumbs->addLink(action('GamesController' . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Math Effect');

        $name = Session::get('userName', null);

        $this->play();
        $game = $this->render();
        View::share('game', $game);

        return View::make('games.tcg.index');//, array('game' => $game)
    }


    public function play()
    {
        $gameData = Session::get('tcg.userGame', null);
        if (!$gameData) {
            echo 'NEW GAME';
            $game = Game::create();
        } else {
            echo 'GAME LOADED';
            $game = Game::import($gameData);
        }
        $game->gameAutoActions();
        $this->game = $game;
        
    }

    protected function render()
    {
        $result = $this->game->render(1);
        
        $this->save();

        return $result;
    }

    protected function save()
    {
        $data = $this->game->export();
        Session::put('tcg.userGame', $data);        
    }

    public function dropGame()
    {
        Session::put('tcg.userGame', null);   
        return Redirect::to('tcg');
    }

    public function deploy()
    {
        $this->play();

        $cardId = Input::get('cardId');
        $x = Input::get('x');
        $y = Input::get('y');
        var_dump($cardId);
        var_dump($x);
        var_dump($y);

        $this->game->deploy($cardId, $x, $y);
        $this->save();

        return Redirect::to('tcg');
    }

    
}
