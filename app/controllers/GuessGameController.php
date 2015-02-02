<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class GuessGameController extends \BaseController
{
    const SESSION_DATA = 'guess.game';

    const CACHE_KEY_STATS_MONTH = 'guess.stats.month';
    const CACHE_KEY_STATS_DAY   = 'guess.stats.day';

    const GAME_TURN = 'turn';
    const GAME_STARTED = 'started';
    const GAME_START_TIME = 'start_time';
    const GAME_TURN_START_TIME = 'turn_start_time';
    const GAME_CURRENT_QUESTION = 'current_question';
    const GAME_POINTS = 'points';
    const GAME_ABILITIES = 'abilities';
    const GAME_FINISHED = 'finished';
    const GAME_PREV_QUESTIONS = 'prev_questions';

    /**
     * Display a listing of the games.
     *
     * @return Response
     */
    public function index()
    {
        $game = $this->createGame();
        $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game['turn']);
        $this->saveGame($game);

        if ($game['turn'] == 1) {
            $firstQuestion = json_encode($this->exportQuestion($game[self::GAME_CURRENT_QUESTION]));
        } else {
            $firstQuestion = '{}';
        }
        View::share('firstQuestion', $firstQuestion);

        View::share('page_title', 'Guess series game');

        return View::make('games.guess.index');//, array('game' => $game)
    }

    public function stats()
    {
        $imageStats = ImagesStats::getHardestImage([time() - 24 * 60 * 60, time() + 2 * 60 * 60]);
        $url = SeriesImage::where('id', $imageStats->image_id)->pluck('url');
        View::share('hardestPicture', $url);
        View::share('today', $this->getStatsToday());
        View::share('month', $this->getStatsMonth());
        return View::make('games.guess.stats');
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
        return [];
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
            $prevQuestions = [$game[self::GAME_CURRENT_QUESTION]['seriseId']];
            if (!empty($game[self::GAME_PREV_QUESTIONS])) {
                $prevQuestions[] = $game[self::GAME_PREV_QUESTIONS][0];
                if (!empty($game[self::GAME_PREV_QUESTIONS][1])) {
                    $prevQuestions[] = $game[self::GAME_PREV_QUESTIONS][1];
                }
            }
            $game[self::GAME_PREV_QUESTIONS] = $prevQuestions;
            $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game[self::GAME_TURN], $prevQuestions);
            $game[self::GAME_TURN_START_TIME] = time();

            $this->saveGame($game);
            return json_encode([
                'question' => $this->exportQuestion($game[self::GAME_CURRENT_QUESTION]), 
                'result' => $result
                ]);
        } else {
            $name = Session::get('userName', false);
            $game[self::GAME_FINISHED] = true;
            $this->saveResults($game[self::GAME_CURRENT_QUESTION]);
            $return = [
                'finish' => true,
                'correctAnswer' => $game[self::GAME_CURRENT_QUESTION]['correct'],
                'correctAnswersNumber' => $game[self::GAME_TURN] - 1,
                'points' => $game[self::GAME_POINTS],
                'name' => $name,
                'stats' => $this->getStatsToday($game[self::GAME_POINTS])
            ];
            
            $this->saveGame($game);
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
            'name' => $name,
            'stats' => $this->getStatsToday()
        ];
        $game[self::GAME_FINISHED] = true;
        $this->saveGame($game);
        $this->saveResults($game[self::GAME_CURRENT_QUESTION]);
        return json_encode($return);
    }

    public function ability()
    {
        $id = (int) Input::get('id');
        $game = $this->getGame();
        if (in_array($id, $game[self::GAME_ABILITIES])) {
            return '[]';
        }
        $game[self::GAME_ABILITIES][] = $id;
        $game[self::GAME_TURN_START_TIME] = time();
        $result = [
            'id' => $id
        ];
        switch ($id) {
            case 1: // 50/50
                $keysToRemove = [];
                $options = $game[self::GAME_CURRENT_QUESTION]['options'];
                for($i = 0; $i < 3; $i++) {
                    $randKey = array_rand($options);
                    unset($options[$randKey]);
                    if ($randKey == $game[self::GAME_CURRENT_QUESTION]['correct']) {
                        continue;
                    }
                    $keysToRemove[] = $randKey;
                    if (count($keysToRemove) == 2) {
                        break;
                    }
                }
                $result['wrong'] = $keysToRemove;
                break;
            case 2:
                $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game[self::GAME_TURN]);
                $result['question'] = $this->exportQuestion($game[self::GAME_CURRENT_QUESTION]);
                break;
            case 3:
                $currentLevel = $this->getCurrentLevelConfig($game[self::GAME_TURN]);
                $levelConfig  = \Config::get('guess.game.levels.' . $currentLevel);
                $question     = $game[self::GAME_CURRENT_QUESTION];
                switch($game[self::GAME_CURRENT_QUESTION]['type']) {
                    // find new image for every type of question
                    case 1:// here we need to change image for question
                        $currentImageId = $question['picture']['id'];
                        $seriesId = $question['picture']['series_id'];
                        $imageDifficulty = $levelConfig[3][array_rand($levelConfig[3])];

                        $question['picture'] = $this->getPicture($imageDifficulty, $seriesId, [$currentImageId]);
                        break;
                    case 2:
                        foreach ($question['options'] as &$image) {
                            $imageDifficulty = $levelConfig[3][array_rand($levelConfig[3])];
                            $image = $this->getPicture($imageDifficulty, $image['series_id'], [$image['id']]);
                        }
                        break;
                }
                $game[self::GAME_CURRENT_QUESTION] = $question;
                $result['question'] = $this->exportQuestion($game[self::GAME_CURRENT_QUESTION]);
                break;
        }

        $this->saveGame($game);
        return json_encode($result);
    }

    public function saveName()
    {
        $name            = Input::get('name');
        $laravel_session = md5(Cookie::get('laravel_session'));
        if (!$name) {
            return '[]';
        }

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

    protected function saveResults($question)
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

        switch($question['type']) {
            case 1:
                $imageId = $question['picture']['id'];
                break;
            case 2:
                $imageId = $question['options'][$question['correct']]['id'];
                break;
        }
        
        $imageStats = new ImagesStats();
        $imageStats->image_id = $imageId;
        $imageStats->type = 1;
        $imageStats->save();
    }

    protected function getCurrentLevelConfig($turn)
    {
        $difficulty = \Config::get('guess.game.difficulty');
        $currentLevel = 0;
        foreach ($difficulty as $turns => $level) {
            if ($turn <= $turns) {
                $currentLevel = $level - 1;
                break;
            }
        }
        return $currentLevel;
    }

    protected function getNewQuestion($turn, $excludeSeriesIds = array()) 
    {
        $currentLevel = $this->getCurrentLevelConfig($turn);
        $levelConfig = \Config::get('guess.game.levels.' . $currentLevel);

        $typeId           = $this->getArrayRandomValue($levelConfig[4]);
        $seriesDifficulty = $this->getArrayRandomValue($levelConfig[2]);

        $question = [
            'sec' => $levelConfig[0],
            'level' => $currentLevel,
            'type' => $typeId,
        ];

        $answerSeries = $this->getRandomSeries(false, $excludeSeriesIds);
        $question['seriesId'] = $answerSeries['id'];
        switch ($typeId) {
            case 1:
                $imageDifficulty = $levelConfig[3][array_rand($levelConfig[3])];
                $question['picture'] = $this->getPicture($imageDifficulty, $answerSeries['id']);
                //$this->getArrayRandomValue($levelConfig[2])
                $wrong1 = $this->getRandomSeries(false, [$answerSeries['id']]);
                $wrong2 = $this->getRandomSeries(false, [$answerSeries['id'], $wrong1['id']]);
                $wrong3 = $this->getRandomSeries(false, [$answerSeries['id'], $wrong1['id'], $wrong2['id']]);
                $question['all'] = [
                    $answerSeries['name'], 
                    $wrong1['name'],
                    $wrong2['name'],
                    $wrong3['name'],
                ];
                break;
            case 2:
                $question['name'] = $answerSeries['name'];
                //$this->getArrayRandomValue($levelConfig[2])
                $wrong1 = $this->getRandomSeries(false, [$answerSeries['id']]);
                $wrong2 = $this->getRandomSeries(false, [$answerSeries['id'], $wrong1['id']]);
                $wrong3 = $this->getRandomSeries(false, [$answerSeries['id'], $wrong1['id'], $wrong2['id']]);
                $question['all'] = [
                    $this->getPicture($this->getArrayRandomValue($levelConfig[3]), $answerSeries['id']),
                    $this->getPicture($this->getArrayRandomValue($levelConfig[3]), $wrong1['id']),
                    $this->getPicture($this->getArrayRandomValue($levelConfig[3]), $wrong2['id']),
                    $this->getPicture($this->getArrayRandomValue($levelConfig[3]), $wrong3['id']),
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
        return $question;
    }

    protected function getStatsToday($currentResult = null)
    {
        $cachedStats = Cache::get(self::CACHE_KEY_STATS_DAY, null);
        if ($cachedStats) {
            $lastElement = array_slice($cachedStats, -1);
        }
        if (!$cachedStats || $lastElement[9]['points'] < $currentResult) {
            $stats = GuessStats::getTopStatistic([time() - (24 * 60 * 60), time() + (4 * 60 * 60)]);
            foreach ($stats as $key => &$stat) {
                $stat['key'] = $key + 1;
            }
            $expiresAt = Carbon::now()->addMinutes(15);
            Cache::put(self::CACHE_KEY_STATS_MONTH, $stats, $expiresAt);
        } else {
            $stats = $cachedStats;
        }
        return $stats;
    }

    protected function getStatsMonth()
    {
        $cachedStats = Cache::get(self::CACHE_KEY_STATS_MONTH, null);
        if (!$cachedStats) {
            $stats = GuessStats::getTopStatistic([time() - 30 * 24 * 60 * 60, time() + 4 * 60 * 60]);
            foreach ($stats as $key => &$stat) {
                $stat['key'] = $key + 1;
            }
            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::put(self::CACHE_KEY_STATS_MONTH, $stats, $expiresAt);
        } else {
            $stats = $cachedStats;
        }
        return $stats;
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
                $toExport['picture'] = $question['picture']['url'];
                break;
            case 2:
                $toExport['name'] = $question['name'];
                foreach ($toExport['options'] as $key => &$value) {
                    $value = $value['url'];
                }
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
            self::GAME_ABILITIES => [],
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
            $seconds = (int) ($question['sec'] * 0.25);
        }
        $phpSeconds = $question['sec'] - (time() - $game[self::GAME_TURN_START_TIME]);
        if ($phpSeconds + 3 < $seconds) {
            $seconds = $seconds * 0.25;
        }
        $k = \Config::get('guess.game.levels.' . $question['level'])[1];
        $points = round($k * $seconds, 1);
        $game[self::GAME_POINTS] += $points;
        return ['k' => $k, 'seconds' => $seconds];
    }

    protected function getRandomSeries($difficulty = false, $excludeIds = array())
    {
        return Series::getRandomSeries($difficulty, $excludeIds);
    }

    protected function getPicture($difficulty, $seriesId = null, $excludeIds = array())
    {
        return SeriesImage::getPicture($difficulty, $seriesId, $excludeIds);
    }

    protected function getArrayRandomValue($array)
    {
        return $array[array_rand($array)];
    }

    public function admin()
    {
        return View::make('games.guess.admin.index');
    }
}
