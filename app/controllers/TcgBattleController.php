<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Game;
use Tcg\GameBuilder;

class TcgBattleController extends \BaseController
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

    public function findBattlePage()
    {
        $player = User::getUser();
        if ($player->id) {
            $decks = Deck::getMyDecks();
        } else {
            return Redirect::to('tcg/me');
        }

        $battle = Battle::findMyBattle();
        if ($battle) {
            return Redirect::to('tcg/battle');
        }

        $queue = TcgQueue::getMyQueue();
        if ($queue) {
            View::share('queue', $queue);
        } else {
            View::share('queue', false);
        }

        View::share('decks', $decks);
        return View::make('games.tcg.battle.find');
    }


    public function joinQueue($deckId)
    {
        $deck = Deck::find($deckId);
        if ($deck->player_id != Auth::user()->id) {
            return Redirect::to('tcg/me');
        }
        if (TcgQueue::getMyQueue()) {
            return Redirect::to('tcg/findBattle');
        }

        TcgQueue::add($deckId);
        return Redirect::to('tcg/findBattle');
    }

    public function leaveQueue()
    {
        $queue = TcgQueue::getMyQueue();
        if (!$queue) {
            return Redirect::to('tcg/findBattle');
        }

        $queue->delete();
        return Redirect::to('tcg/findBattle');
    }

    public function checkQueue()
    {
        $queue = TcgQueue::getMyQueue();
        if (!$queue) {
            return json_encode(['error' => 'not_in_queue']);
        }

        if ($this->shouldProcess($queue)) {
            $this->matchBattles($queue);
        }

        return json_encode(['message' => 'In queue']);
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

    protected function matchBattles($myQueue)
    {
        $peopleInQueue = TcgQueue::where('game_type_id', '=', $myQueue->game_type_id)->orderBy('created_at')->get();
        $playersInBattle = 2;
        $battles = [];
        $currentBattle = 0;
        $processedPlayers = 0;
        $countPlayersInQueue = count($peopleInQueue);
        foreach ($peopleInQueue as $queueObject) {

            if (!isset($battles[$currentBattle])) {
                $battles[$currentBattle] = [];
            }
            if (count($battles[$currentBattle]) < $playersInBattle) {
                $battles[$currentBattle][] = $queueObject;
            }
            if (count($battles[$currentBattle]) == $playersInBattle) {
                if ($processedPlayers > ceil($countPlayersInQueue * 0.6)) {
                    break;
                }
                $currentBattle ++;
            }
        }

        if ($battles) {
            foreach ($battles as $battle) {
                if (count($battle) == $playersInBattle) {
                    $battleObject = new Battle();
                    $battleObject->type_id = $myQueue->game_type_id;

                    $battleObject->save();

                    foreach ($battle as $playerQueue) {

                        $battlePlayer = new BattlePlayer();
                        $battlePlayer->battle_id = $battleObject->id;
                        $battlePlayer->player_id = $playerQueue->player_id;
                        $battlePlayer->deck_id = $playerQueue->deck_id;
                        $battlePlayer->save();

                        $playerQueue->delete();
                    }
                }
            }
        }
    }

    protected function shouldProcess($myQueue)
    {
        $peopleInQueue = TcgQueue::where('game_type_id', '=', $myQueue->game_type_id)->count();
        if (mt_rand(1, $peopleInQueue) === 1) {
            return true;
        }
        return false;
    }

}
