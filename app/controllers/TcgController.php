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
        $currentPlayerId = 1;
        $gameData = Session::get('tcg.userGame', null);
        if (!$gameData) {
            $game = Game::create($currentPlayerId);
        } else {
            
            $game = Game::import($gameData, $currentPlayerId);
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

    public function action()
    {
        $this->play();
        
        $action = Input::get('action');

        switch ($action) {
            case Game::GAME_ACTION_DEPLOY:
                $cardId = Input::get('cardId');
                $x = Input::get('x');
                $y = Input::get('y');
                $this->game->action(Game::GAME_ACTION_DEPLOY, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
            break;
            case Game::GAME_ACTION_SKIP:
                $this->game->action(Game::GAME_ACTION_SKIP);
            break;
        }
        $this->save();

        return Redirect::to('tcg');   
    }

    
}
