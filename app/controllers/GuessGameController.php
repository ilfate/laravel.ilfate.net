<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class GuessGameController extends \BaseController
{
    const SESSION_DATA = 'guess.game';
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

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
        // $currentPlayerId = 1;
        // $this->play($currentPlayerId);
        // $game = $this->render($currentPlayerId);
        // $this->save();
        // View::share('game', $game);
        $game = $this->getGame();
        if (!$game['currentQuestion']) {
            $game['currentQuestion'] = $this->getNewQuestion($game['turn']);
            $this->saveGame($game);
        }
        if ($game['turn'] == 1) {
            $firstQuestion = $this->exportQuestion($game['currentQuestion']);
        } else {
            $firstQuestion = '{}';
        }
        View::share('firstQuestion', $firstQuestion);

        View::share('page_title', 'Guess series game');


        return View::make('games.guess.index');//, array('game' => $game)
    }

    public function getQuestion()
    {
        $game = $this->getGame();
        if ($game['turn'] == 1) {
            return [];
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
                break;
        }
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
        }
        return json_encode($toExport);
    }

    protected function getGame() 
    {
        $game = Session::get(self::SESSION_DATA, null);
        if (!$game) {
            $game = [
                'turn' => 1,
                'bonuses' => [],
                'currentQuestion' => false,
            ];
        }
        return $game;
    }

    protected function saveGame($game)
    {
        Session::put(self::SESSION_DATA, $game);
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
