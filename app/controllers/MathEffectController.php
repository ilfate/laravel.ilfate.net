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
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Games');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Math Effect');
        return View::make('games.mathEffect');
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

        $tdStatistics                  = new TdStatistics();
        $tdStatistics->pointsEarned    = Input::get('pointsEarned');
        $tdStatistics->turnsSurvived   = Input::get('turnsSurvived');
        $tdStatistics->unitsKilled     = Input::get('unitsKilled');
        $tdStatistics->ip              = $_SERVER['REMOTE_ADDR'];
        $tdStatistics->laravel_session = md5(Cookie::get('laravel_session'));

        $tdStatistics->save();
        //return 'turns = ' . $turnsSurvived;
        return '{}';
    }

    public function updateName()
    {
        $name            = Input::get('Name');
        $laravel_session = md5(Cookie::get('laravel_session'));

        $stats = TdStatistics::where('laravel_session', '=', $laravel_session)->firstOrFail();
        if (!stats) {
            Log::warning('No user found to update name. (name=' . $name . ')');
            App::abort(404);
        } 
        $stats->name = $name;
        $stats->save();
    }
}
