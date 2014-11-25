<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Deck;
use Tcg\Card;
use Tcg\Game;
use Tcg\GameBuilder;

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
        $currentPlayerId = 1;
        $this->play($currentPlayerId);
        $game = $this->render($currentPlayerId);
        $this->save();
        View::share('game', $game);

        return View::make('games.tcg.testIndex');//, array('game' => $game)
    }

    public function bot()
    {
        $currentPlayerId = 2;
        $this->play($currentPlayerId);
        $game = $this->render($currentPlayerId);
        $this->save();
        View::share('game', $game);

        return View::make('games.tcg.testIndex');//, array('game' => $game)
    }


    public function play($currentPlayerId, $type = Game::IMPORT_TYPE_NORMAL)
    {

        //$gameData = Session::get('tcg.userGame', null);
        $gameData = Cache::get('tcg.userGame', null);
        if (!$gameData) {
            $game = GameBuilder::buildTest($currentPlayerId, ['isBot' => false]);
        } else {

            $game = Game::import($type, $gameData, $currentPlayerId);
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
        $debug = Input::get('debug', false);
        $bot = Input::get('bot', false);
        $situation = Input::get('situation', false);
        if ($debug || $bot || $situation) {
            if ($situation) {
                $this->game = GameBuilder::buildSituation(1, [], ['isBot' => $bot]);
            } else {
                $this->game = GameBuilder::buildTest(1, ['isBot' => $bot, 'debug' => $debug]);
            }
            $this->save();
        }
        return Redirect::to('tcg/test');
    }

    public function actionAjax()
    {
        if(!Request::ajax()) {
            throw new \Exception('It is not an a ajax action!');
        }
        $currentPlayerId = Input::get('playerId', 1);
        $action          = Input::get('action', false);

        $type = Game::IMPORT_TYPE_NORMAL;
        if (!$action) {
            $type = Game::IMPORT_TYPE_UPDATE;
        }

        $this->play($currentPlayerId, $type);

        if ($action) {
            $this->doAction($action);
        }

        $data = $this->game->renderUpdate();
        $this->game->pushActions();
        $this->save();
        return json_encode($data);
    }

    private function doAction($action)
    {
        $cardId = (int) Input::get('cardId');
        switch ($action) {
            case Game::GAME_ACTION_DEPLOY:
                $x = (int) Input::get('x');
                $y = (int) Input::get('y');
                $this->game->action(Game::GAME_ACTION_DEPLOY, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
                break;
            case Game::GAME_ACTION_SKIP:
                $this->game->action(Game::GAME_ACTION_SKIP, ['cardId' => $cardId]);
                break;
            case Game::GAME_ACTION_MOVE:
                $x = (int) Input::get('x');
                $y = (int) Input::get('y');
                $this->game->action(Game::GAME_ACTION_MOVE, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
                break;
            case Game::GAME_ACTION_CAST:
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
