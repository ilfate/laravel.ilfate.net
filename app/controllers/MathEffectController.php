<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class MathEffectController extends \BaseController
{
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
        $this->breadcrumbs->addLink(action('GamesController' . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Math Effect');

        $name = Session::get('userName', null);

        $MEcheckKey = md5(rand(0,99999) . time());
        Session::put('MEcheckKey', $MEcheckKey);

        return View::make('games.mathEffect', array('userName' => $name, 'checkKey' => $MEcheckKey));
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
