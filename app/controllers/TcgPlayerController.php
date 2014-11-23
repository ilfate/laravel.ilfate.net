<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Deck;
use Tcg\Card;
use Tcg\Game;
use Tcg\GameBuilder;

class TcgPlayerController extends \BaseController
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
        Session::forget(User::GUEST_USER_SESSION_KEY);
        $player = User::getUser();
        if ($player->id) {
            $player->touch();
        }

        View::share('player', [
            'id'   => $player->getId(),
            'name' => $player->getName(),
            'auth' => !! $player->id,
        ]);

        return View::make('games.tcg.player.index');
    }

    public function registerForm()
    {
        $player = User::getUser();

        $formDefaults = Session::get('formDefaults', null);
        if ($formDefaults) {
            View::share('formDefaults', $formDefaults);
            Session::forget('formDefaults');
        }

        View::share('player', [
            'id'   => $player->getId(),
            'name' => $player->getName(),
            'auth' => $player->id,
        ]);

        return View::make('games.tcg.player.register');
    }

    public function registerSubmit()
    {
        $email = Input::get('email');
        $password1 = Input::get('password1');
        $password2 = Input::get('password2');
        $name = Input::get('name');

        $player = User::getUser();

        $validator = Validator::make(
            array(
                'name' => $name,
                'password' => $password1,
                'email' => $email
            ),
            array(
                'name' => 'required|min:4|max:20|unique:users',
                'password' => 'required|min:6|max:60|in:' . $password2,
                'email' => 'required|email|unique:users|max:60'
            )
        );

        if ($player->id || $validator->fails())
        {
            Session::set('formDefaults' , [
                'name' => $name,
                'email' => $email
            ]);
            return Redirect::to('tcg/register')->withErrors($validator);
        }

        $player->email = $email;
        $player->password = $password1;
        $player->name = $name;

        $player->save();

        Auth::loginUsingId($player->getId(), true);

        return Redirect::to('tcg/me');
    }

    public function login()
    {
        $player = User::getUser();
        if ($player->id) {
            return Redirect::to('tcg/me');
        }
        $formErros = Session::get('formErrors', null);
        if ($formErros) {
            Session::forget('formErrors');
            View::share('formErrors', $formErros);
        }
        return View::make('games.tcg.player.login');
    }

    public function loginSubmit()
    {
        $email = Input::get('email');
        $password = Input::get('password');

        $player = User::getUser();

        if ($player->id)
        {
            // user is already logged in
            return Redirect::to('tcg/me');
        }

        if (Auth::attempt(array('email' => $email, 'password' => $password),
            true))
        {
            return Redirect::to('tcg/me');
        }
        Session::set('formErrors' , [
            ['message' => Lang::get('tcg.authFail'), 'type' => 'danger'],
            ['message' => Lang::get('tcg.tryToRegister', ['url' => '/tcg/register']), 'type' => 'info'],
        ]);
        return Redirect::to('tcg/login');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('tcg/me');
    }
}
