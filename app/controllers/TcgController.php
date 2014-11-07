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

        $currentPlayerId = 1;
        $this->play($currentPlayerId);
        $game = $this->render($currentPlayerId);
        View::share('game', $game);

        return View::make('games.tcg.index');//, array('game' => $game)
    }

    public function bot()
    {
        $this->breadcrumbs->addLink(action('GamesController' . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Math Effect');

        $name = Session::get('userName', null);

        $currentPlayerId = 2;
        $this->play($currentPlayerId);
        $game = $this->render($currentPlayerId);
        View::share('game', $game);

        return View::make('games.tcg.index');//, array('game' => $game)
    }


    public function play($currentPlayerId)
    {
        
        $gameData = Session::get('tcg.userGame', null);
        if (!$gameData) {
            $game = Game::create($currentPlayerId);
        } else {
            
            $game = Game::import($gameData, $currentPlayerId);
        }
        $this->game = $game;
        
    }

    protected function render($currentPlayerId)
    {
        $result = $this->game->render($currentPlayerId);
        
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
        $currentPlayerId = 1;
        $this->play($currentPlayerId);
        
        $action = Input::get('action');

        switch ($action) {
            case Game::GAME_ACTION_DEPLOY:
                $cardId = (int) Input::get('cardId');
                $x = (int) Input::get('x');
                $y = (int) Input::get('y');
                $this->game->action(Game::GAME_ACTION_DEPLOY, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
            break;
            case Game::GAME_ACTION_SKIP:
                $this->game->action(Game::GAME_ACTION_SKIP);
            break;
            case Game::GAME_ACTION_MOVE:
                $cardId = (int) Input::get('cardId');
                $x = (int) Input::get('x');
                $y = (int) Input::get('y');
                $this->game->action(Game::GAME_ACTION_MOVE, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
            break;
            case Game::GAME_ACTION_CAST:
                $cardId = (int) Input::get('cardId');
                $data = Input::get('data');
                $this->validateCastData($data);
                $this->game->action(Game::GAME_ACTION_CAST, ['cardId' => $cardId, 'data' => $data]);
            break;
        }
        $this->save();

        return Redirect::to('tcg');   
    }

    
    private function validateCastData(&$data) {
        if (isset($data['x']) && isset($data['y'])) {
            $data = [
                'x' => (int) $data['x'],
                'y' => (int) $data['y']
            ];
        } else if (isset($data['targetId'])) {
            $data = ['targetId' => (int) $data['targetId']];
        } else {
            $data = [];
        }
    }
}
