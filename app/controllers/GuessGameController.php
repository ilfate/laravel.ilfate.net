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
    const GAME_FINISHED = 'finished';

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
        if ($game[self::GAME_FINISHED]) {
            return '[]';
        }
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
            $name = Session::get('userName', false);
            $return = [
                'finish' => true,
                'correctAnswer' => $game[self::GAME_CURRENT_QUESTION]['correct'],
                'correctAnswersNumber' => $game[self::GAME_TURN] - 1,
                'points' => $game[self::GAME_POINTS],
                'name' => $name
            ];
            $game[self::GAME_FINISHED] = true;
            $this->saveGame($game);
            $this->saveResults();
            return json_encode($return);
        }
    }

    public function timeIsOut()
    {
        $name = Session::get('userName', false);
        $game = $this->getGame();
        if ($game[self::GAME_FINISHED]) {
            return '[]';
        }
        $return = [
            'correctAnswer' => $game[self::GAME_CURRENT_QUESTION]['correct'],
            'points' => $game[self::GAME_POINTS],
            'correctAnswersNumber' => $game[self::GAME_TURN] - 1,
            'name' => $name
        ];
        $game[self::GAME_FINISHED] = true;
        $this->saveGame($game);
        $this->saveResults();
        return json_encode($return);
    }

    public function saveName()
    {
        $name            = Input::get('name');
        $laravel_session = md5(Cookie::get('laravel_session'));

        Session::put('userName', $name);

        $stats = GuessStats::where('laravel_session', '=', $laravel_session)->orderBy('created_at', 'desc')->firstOrFail();
        if (!$stats) {
            Log::warning('No user found to update name. (name=' . $name . ')');
            App::abort(404);
        }
        $stats->name = $name;
        $stats->save();
        return '{"actions": ["Guess.Game.hideNameForm"]}';
    }

    protected function saveResults()
    {
        $name     = Session::get('userName', null);
        $game = $this->getGame();

        $stats = new GuessStats();
        $stats->points = $game[self::GAME_POINTS];
        $stats->answers = $game[self::GAME_TURN] - 1;
        $stats->ip      = $_SERVER['REMOTE_ADDR'];
        $stats->laravel_session = md5(Cookie::get('laravel_session'));
        $stats->name = $name;
        $stats->save();
    }

    protected function getNewQuestion($turn) 
    {
        $difficulty = \Config::get('guess.game.difficulty');
        $currentLevel = 0;
        foreach ($difficulty as $turns => $level) {
            if ($turn <= $turns) {
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
                $imageDifficulty = $levelConfig[2][array_rand($levelConfig[2])];
                $question['picture'] = $this->getPicture($imageDifficulty, $answerSeries['id']);
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
                    $this->getPicture($levelConfig[2][array_rand($levelConfig[2])], $answerSeries['id']),
                    $this->getPicture($levelConfig[2][array_rand($levelConfig[2])], $wrong1['id']),
                    $this->getPicture($levelConfig[2][array_rand($levelConfig[2])], $wrong2['id']),
                    $this->getPicture($levelConfig[2][array_rand($levelConfig[2])], $wrong3['id']),
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
            self::GAME_FINISHED => false,
        ];
    }

    protected function saveGame($game)
    {
        Session::forget(self::SESSION_DATA);
        Session::put(self::SESSION_DATA, $game);
    }

    protected function addPointsToGame(&$game, $seconds)
    {
        $question = $game[self::GAME_CURRENT_QUESTION];
        if ($seconds > $question['sec']) {
            // user tried to fake the data
            $seconds = (int) ($question['sec'] * 0.5);
        }
        $phpSeconds = $question['sec'] - (time() - $game[self::GAME_TURN_START_TIME]);
        if ($phpSeconds + 3 < $seconds) {
            $seconds = $seconds * 0.75;
        }
        $k = \Config::get('guess.game.levels.' . $question['level'])[1];
        $points = round($k * $seconds, 1);
        $game[self::GAME_POINTS] += $points;
        return ['k' => $k, 'seconds' => $seconds];
    }

    protected function getRandomSeries($excludeIds = array())
    {
        return Series::getRandomSeries(1, $excludeIds);
    }

    protected function getPicture($difficulty, $seriesId = null)
    {
        return SeriesImage::getPicture($difficulty, $seriesId);
    }

    public function admin()
    {
        return View::make('games.guess.admin.index');
    }
}
