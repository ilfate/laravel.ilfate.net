<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Game;
use Tcg\GameBuilder;

class TcgTutorialController extends \BaseController
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

        return View::make('games.tcg.player.index');
    }

   

    public function battle()
    {
        $battleId = Session::get('myBattleId', null);
        if (!$battleId) {
            $battle = Battle::findMyBattle();
        } else {
            $battle = Battle::find($battleId);
        }
        if (!$battle) {
            return Redirect::to('tcg/me');
        }
        if (!$battleId) {
            Session::put('myBattleId', $battle->id);
        }
        // ok battle is here
        switch ($battle->status_id) {
            case 0:
                // we have to init battle
                $battle->status_id = 1;
                $battle->save();
                if ($this->getGame($battle->id)) {
                    return Redirect::to('tcg/battle');
                }
                $game = $this->createNewBattle($battle);
                $this->saveGame($game, $battle->id);
                $battle->status_id = 2;
                $battle->save();
                return Redirect::to('tcg/battle');
            break;
            case 1:
                // ok game is in creating process
                sleep(1); // let`s wait a little to prevent redirect loop.
                return Redirect::to('tcg/battle');
                break;
            case 2:
                $game = $this->getGame($battle->id);
                if (!$game) {
                    // some real shit just happen
                    // may be we lost memcache
                    // or server was restarted (also memcache loss)
                    // we need to do some reporting here
                    $battle->status_id = 91;
                    $battle->save();
                    return Redirect::to('tcg/me');
                }
                $render = $game->render(Auth::user()->id);
                $this->saveGame($game, $battle->id);
                View::share('game', $render);
                return View::make('games.tcg.testIndex');
                break;

        }
    }

    public function battleAction()
    {
        if(!Request::ajax()) {
            throw new \Exception('It is not an a ajax action!');
        }

        $action = Input::get('action', false);

        $battleId = Session::get('myBattleId', null);
        if (!$battleId) {
            return json_encode(['error' => true, 'message' => 'battle not found']);
        }

        $game = $this->getGame($battleId);

        if ($action) {
            $error = $this->doAction($game, $action);
        }
        if (!empty($error)) {
            return json_encode(['error' => true, 'message' => $error]);
        }

        $data = $game->renderUpdate();
        $game->pushActions();
        $this->saveGame($game, $battleId);
        return json_encode($data);
    }

    protected function createNewBattle($battle)
    {
        $game = GameBuilder::buildGameForBattle($battle);
        return $game;
    }

    protected function saveGame($game, $battleId)
    {
        $data = $game->export();
        Cache::put('tcg.battle.' . $battleId, $data, 300);
    }

    protected function getGame($battleId) {
        $gameData = Cache::get('tcg.battle.' . $battleId, null);
        if ($gameData) {
            $game = Game::import(Game::IMPORT_TYPE_NORMAL, $gameData, Auth::user()->id);
            return $game;
        }
        return $gameData;
    }

    private function doAction(Game $game, $action)
    {
        $cardId = (int) Input::get('cardId');
        try {
            switch ($action) {
                case Game::GAME_ACTION_DEPLOY:
                    $x = (int) Input::get('x');
                    $y = (int) Input::get('y');
                    $game->action(Game::GAME_ACTION_DEPLOY, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
                    break;
                case Game::GAME_ACTION_SKIP:
                    $game->action(Game::GAME_ACTION_SKIP, ['cardId' => $cardId]);
                    break;
                case Game::GAME_ACTION_MOVE:
                    $x = (int) Input::get('x');
                    $y = (int) Input::get('y');
                    $game->action(Game::GAME_ACTION_MOVE, ['cardId' => $cardId, 'x' => $x, 'y' =>$y]);
                    break;
                case Game::GAME_ACTION_CAST:
                    $data = Input::get('data');
                    $this->validateCastData($data);
                    $game->action(Game::GAME_ACTION_CAST, ['cardId' => $cardId, 'data' => $data]);
                    break;
            }
        } catch (\Tcg\Exception $e) {
            return $e->getMessage();
        }
    }

}
