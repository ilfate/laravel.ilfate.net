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
        $this->save();
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
        $this->save();
        View::share('game', $game);

        return View::make('games.tcg.index');//, array('game' => $game)
    }


    public function play($currentPlayerId)
    {
        
        //$gameData = Session::get('tcg.userGame', null);
        $gameData = Cache::get('tcg.userGame', null);
        if (!$gameData) {
            $game = Game::create($currentPlayerId, 'NOT_bot');
        } else {
            
            $game = Game::import($gameData, $currentPlayerId);
        }
        $this->game = $game;
        
    }

    protected function render($currentPlayerId)
    {
        $result = $this->game->render($currentPlayerId);

        return $result;
    }

    protected function save()
    {
        $data = $this->game->export();
        //Session::put('tcg.userGame', $data);
        Cache::put('tcg.userGame', $data, 300);
    }

    public function dropGame()
    {
        //Session::put('tcg.userGame', null);   
        Cache::forget('tcg.userGame');   
        return Redirect::to('tcg');
    }

    public function action()
    {
        $currentPlayerId = Input::get('playerId', 1);
        $this->play($currentPlayerId);
        
        $action = Input::get('action');

        $this->doAction($action);
        $this->save();

        if ($currentPlayerId == 2) {
            return Redirect::to('tcgb');
        }
        return Redirect::to('tcg');   
    }

    public function actionAjax()
    {
        if(!Request::ajax()) {
            throw new \Exception('It is not an a ajax action!');
        }
        $currentPlayerId = Input::get('playerId', 1);
        $this->play($currentPlayerId);

        $action = Input::get('action', false);
        if ($action) {
            $this->doAction($action);
        }

        $data = $this->game->renderUpdate();
        $this->save();
        return json_encode($data);
    }

    private function doAction($action)
    {
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
