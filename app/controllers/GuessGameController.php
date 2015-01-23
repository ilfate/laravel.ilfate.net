<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class GuessGameController extends \BaseController
{
    const SESSION_DATA = 'guess.game';

    const GAME_TURN = 'turn';
    const GAME_STARTED = 'started';
    const GAME_START_TIME = 'start_time';
    const GAME_TURN_START_TIME = 'turn_start_time';
    const GAME_CURRENT_QUESTION = 'current_question';
    const GAME_POINTS = 'points';

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the games.
     *
     * @return Response
     */
    public function index()
    {
        // $game = ['asdasd', 'awdawdwddddd'];
        // $this->saveGame($game);
        // $game = $this->getGame();
        // return json_encode($game);

        // $game = $this->getGame();
        // if (!empty($game[self::GAME_STARTED])) {
        $game = $this->createGame();
        // } 
        //if (!$game[self::GAME_CURRENT_QUESTION]) {
        $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game['turn']);
        $this->saveGame($game);
        //}

        if ($game['turn'] == 1) {
            $firstQuestion = json_encode($this->exportQuestion($game[self::GAME_CURRENT_QUESTION]));
        } else {
            $firstQuestion = '{}';
        }
        View::share('firstQuestion', $firstQuestion);

        View::share('page_title', 'Guess series game');

        return View::make('games.guess.index');//, array('game' => $game)
    }

    public function gameStarted()
    {
        $game = $this->getGame();
        if (!empty($game[self::GAME_STARTED])) {
            return [];
        }
        $game[self::GAME_STARTED] = true;
        $game[self::GAME_START_TIME] = time();
        $game[self::GAME_TURN_START_TIME] = time();
        $this->saveGame($game);
        $game = $this->getGame();
        return json_encode($game);
        // return [];
    }

    public function answer()
    {
        $game = $this->getGame();
        //return json_encode($game);
        $id = (int) Input::get('id');
        $seconds = (int) Input::get('seconds');

        if ($game[self::GAME_CURRENT_QUESTION]['correct'] === $id) {
            $result = $this->addPointsToGame($game, $seconds);
            $game[self::GAME_TURN]++;
            $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game[self::GAME_TURN]);
            $game[self::GAME_TURN_START_TIME] = time();

            $this->saveGame($game);
            return json_encode([
                'question' => $this->exportQuestion($game[self::GAME_CURRENT_QUESTION]), 
                'result' => $result
                ]);
        } else {
            $game = $this->createGame();
            $this->saveGame($game);
            return json_encode(['finish' => true]);
        }
    }

    protected function getNewQuestion($turn) 
    {
        $difficulty = \Config::get('guess.game.difficulty');
        $currentLevel = 0;
        foreach ($difficulty as $turns => $level) {
            if ($turn < $turns) {
                $currentLevel = $level - 1;
                break;
            }
        }
        $levelConfig = \Config::get('guess.game.levels.' . $currentLevel);
            
        $typeId = $levelConfig[3][array_rand($levelConfig[3])];

        $question = [
            'sec' => $levelConfig[0],
            'level' => $currentLevel,
            'type' => $typeId,
        ];

        $answerSeries = $this->getRandomSeries();
        switch ($typeId) {
            case 1:
                $question['picture'] = $this->getPicture($levelConfig[2], $answerSeries['id']);
                $wrong1 = $this->getRandomSeries([$answerSeries['id']]);
                $wrong2 = $this->getRandomSeries([$answerSeries['id'], $wrong1['id']]); 
                $wrong3 = $this->getRandomSeries([$answerSeries['id'], $wrong1['id'], $wrong2['id']]);
                $question['all'] = [
                    $answerSeries['name'], 
                    $wrong1['name'],
                    $wrong2['name'],
                    $wrong3['name'],
                ];
                break;
            case 2:
                $question['name'] = $answerSeries['name'];
                $wrong1 = $this->getRandomSeries([$answerSeries['id']]);
                $wrong2 = $this->getRandomSeries([$answerSeries['id'], $wrong1['id']]);
                $wrong3 = $this->getRandomSeries([$answerSeries['id'], $wrong1['id'], $wrong2['id']]);
                $question['all'] = [
                    $this->getPicture($levelConfig[2], $answerSeries['id']),
                    $this->getPicture($levelConfig[2], $wrong1['id']),
                    $this->getPicture($levelConfig[2], $wrong2['id']),
                    $this->getPicture($levelConfig[2], $wrong3['id']),
                ];
                break;
            default:
                throw new \Exception('this question type is not implemented');
                break;
        }
        $question['options'] = [];
        for ($i = 0; $i < 4; $i++) {
            $randKey = array_rand($question['all']);
            $question['options'][] = $question['all'][$randKey];
            unset($question['all'][$randKey]);
            if ($randKey === 0) {
                $question['correct'] = $i;
            }
        }
        unset($question['all']);
        //shuffle($question['all']);
        return $question;
    }

    protected function exportQuestion($question)
    {

        $toExport = [
            'sec' => $question['sec'],
            'type' => $question['type'],
            'options' => $question['options'],
        ];
        switch($question['type']) {
            case 1:
                $toExport['picture'] = $question['picture'];
                break;
            case 2:
                $toExport['name'] = $question['name'];
                break;
        }
        return $toExport;
    }

    protected function getGame() 
    {
        $game = Session::get(self::SESSION_DATA, null);
        if (!$game) {
            $game = $this->createGame();
        }
        return $game;
    }

    protected function createGame()
    {
        return [
            self::GAME_TURN => 1,
            self::GAME_STARTED => 0,
            'bonuses' => [],
            self::GAME_CURRENT_QUESTION => false,
            self::GAME_POINTS => 0,
        ];
    }

    protected function saveGame($game)
    {
        Session::forget(self::SESSION_DATA);
        Session::put(self::SESSION_DATA, $game);
    }

    protected function addPointsToGame($game, $seconds)
    {
        $question = $game[self::GAME_CURRENT_QUESTION];
        if ($seconds > $question['sec']) {
            // user tried to fake the data
            $seconds = (int) ($question['sec'] * 0.5);
        }
        $phpSeconds = $question['sec'] - (time() - $game[self::GAME_TURN_START_TIME]); 
        if ($phpSeconds + 3 < $seconds) {
            $seconds = $phpSeconds + 1;
        }
        $k = \Config::get('guess.game.levels.' . $question['level'])[1];
        $points = $k * $seconds;
        $game[self::GAME_POINTS] += $points;
        return ['k' => $k, 'seconds' => $seconds];
    }

    protected function getRandomSeries($excludeIds = array()) {
        $series =  [
            1 => [
                'id' => 1,
                'name' => 'House',
            ],
            2 => [
                'id' => 2,
                'name' => 'Game of Thrones',
            ],
            3 => [
                'id' => 3,
                'name' => 'Lost',
            ],
            4 => [
                'id' => 4,
                'name' => 'Heroes',
            ],
            5 => [
                'id' => 5,
                'name' => 'Dexter',
            ],
        ];
        if ($excludeIds) {
            foreach ($excludeIds as $excludeId) {
                unset($series[$excludeId]);
            }
            
        }
        $result = $series[array_rand($series)];
        
        return $result;
    }

    protected function getPicture($difficulty, $seriesId = null)
    {
        $pictures = [
            1 => [
                '/House1.jpg',
                '/House2.jpg', 
            ],
            2 => [
                '/Game_of_Thrones1.jpg',
                '/Game_of_Thrones2.jpg',
            ],
            3 => [
                '/Lost1.jpg',
                '/Lost2.jpg',
            ],
            4 => [
                '/Heroes1.jpg',
                '/Heroes2.jpg',
            ],
            5 => [
                '/Dexter1.jpg',
                '/Dexter2.jpg',
            ],
        ];
        if ($seriesId) {
            $series = $pictures[$seriesId];
        } else {
            $series = $pictures[array_rand($pictures)];
        }
        return $series[array_rand($series)];
    }

     /**
     * js game template
     *
     * @return Response
     */
    public function save()
    {
        if (Request::isMethod('get')) {
            Log::warning('MathEffect save is not Post.');
            App::abort(404);
        }
        if (!Request::ajax()) {
            Log::warning('MathEffect save is not Ajax.');
            App::abort(404);
        }
        $name     = Session::get('userName', null);
        $checkKey = Session::get('MEcheckKey', null);
        Session::put('MEcheckKey', null);

        if (Input::get('checkKey') != $checkKey) {
            Log::warning('Some one tryed to hack');
            Log::warning('pointsEarned=' . Input::get('pointsEarned'));
            Log::warning('turnsSurvived=' . Input::get('turnsSurvived'));
            Log::warning('unitsKilled=' . Input::get('unitsKilled'));
            Log::warning('ip=' . $_SERVER['REMOTE_ADDR']);
            Log::warning('name=' . $name);
            return '{}';
        }


        $tdStatistics                  = new TdStatistics();
        $tdStatistics->pointsEarned    = Input::get('pointsEarned');
        $tdStatistics->turnsSurvived   = Input::get('turnsSurvived');
        $tdStatistics->unitsKilled     = Input::get('unitsKilled');
        $tdStatistics->ip              = $_SERVER['REMOTE_ADDR'];
        $tdStatistics->laravel_session = md5(Cookie::get('laravel_session'));
        $tdStatistics->name            = $name;

        $tdStatistics->save();
        //return 'turns = ' . $turnsSurvived;
        return '{}';
    }

    public function saveName()
    {
        $name            = Input::get('name');
        $laravel_session = md5(Cookie::get('laravel_session'));

        Session::put('userName', $name);


        $stats = TdStatistics::where('laravel_session', '=', $laravel_session)->orderBy('created_at', 'desc')->firstOrFail();
        if (!$stats) {
            Log::warning('No user found to update name. (name=' . $name . ')');
            App::abort(404);
        } 
        $stats->name = $name;
        $stats->save();
        return '{"actions": ["Page.hideMENameForm"]}';
    }

    public function statistic()
    {
        $this->breadcrumbs->addLink(action('GamesController' . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Math Effect');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Statistic');
        //$yesterday = time() - (24 * 60 * 60);
        // $logs = DB::table('td_statistic')
        //     ->select(DB::raw('name, ip, max(turnsSurvived) as turnsSurvived, pointsEarned, unitsKilled'))
        //     ->where('created_at', '>', date('Y-m-d H:i:s', $yesterday))
        //     ->groupBy('name', 'ip')
        //     ->orderBy('turnsSurvived', 'desc')
        //     ->get();
        $topLogs = DB::table('td_statistic')
            ->select(DB::raw('name, ip, turnsSurvived, pointsEarned, unitsKilled'))
            ->orderBy('turnsSurvived', 'desc')
            ->limit(10)
            ->get();
        $totalGames = DB::table('td_statistic')
            ->count();
        $avrTurns = DB::table('td_statistic')
            ->avg('turnsSurvived');
        $users = DB::table('td_statistic')
            ->select(DB::raw('count(DISTINCT CONCAT(COALESCE(name,\'empty\'),ip)) as count'))
            ->pluck('count');

        $name     = Session::get('userName', null);
        $userLogs = false;
        if ($name) {
            $userLogs = DB::table('td_statistic')
                ->select(DB::raw('name, ip, turnsSurvived, pointsEarned, unitsKilled'))
                ->where('name', '=', $name)
                ->orderBy('turnsSurvived', 'desc')
                ->limit(10)
                ->get();
        }

        return View::make('games.mathEffect.stats', array(
            'topLogs'    => $topLogs, 
            'totalGames' => $totalGames,
            'avrTurns'   => $avrTurns,
            'users'      => $users,
            'userLogs'   => $userLogs
            ));
    }
}
