<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;
use Tcg\Game;
use Tcg\GameBuilder;

class TcgCardController extends \BaseController
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

    public function createDeckForm()
    {
        $formDefaults = Session::get('formDefaults', null);
        if ($formDefaults) {
            View::share('formDefaults', $formDefaults['name']);
            Session::forget('formDefaults');
        } else {
            View::share('deck', false);
        }
        View::share('kings', Card::prepareCardsForRender(Card::getMyKings()));
        return View::make('games.tcg.player.manageDeck');
    }

    public function createDeck()
    {
        $name   = Input::get('name');
        $kingId = Input::get('cardId');

        $player = User::getUser();

        $validator = $this->validateDeck($name, $kingId);

        if (!$player->id || $validator !== true) {
            Session::set('formDefaults' , [
                'name' => $name,
                'kingId' => $kingId
            ]);
            return Redirect::to('tcg/createDeck')->withErrors($validator);
        }
        $deck = new Deck();

        $deck->name = $name;
        $deck->player_id = $player->id;
        $deck->king_id = $kingId;

        $deck->save();

        return Redirect::to('tcg/deck/' . $deck->id);
    }

    public function changeDeckForm()
    {
        $deckId = Input::get('deckId');
        if (!$deckId) {
            return Redirect::to('tcg/me');
        }
        $deck = Deck::find($deckId);
        if ($deck->player_id != Auth::user()->id) {
            return Redirect::to('tcg/me');
        }
        View::share('deckId', $deckId);
        View::share('deck', [
            'name' => $deck->name,
            'kingId' => $deck->king_id
        ]);
        View::share('kings', Card::prepareCardsForRender(Card::getMyKings()));
        return View::make('games.tcg.player.manageDeck');
    }

    public function changeDeck()
    {
        $deckId = Input::get('deckId');
        $name   = Input::get('name');
        $kingId = Input::get('cardId');

        $player = User::getUser();

        $validator = $this->validateDeck($name, $kingId);

        if (!$player->id || $validator !== true) {
            Session::set('formDefaults' , [
                'name' => $name,
                'kingId' => $kingId
            ]);
            return Redirect::to('tcg/changeDeck')->withErrors($validator);
        }

        $deck = Deck::find($deckId);

        if ($deck->player_id != Auth::user()->id) {
            return Redirect::to('tcg/me');
        }

        $deck->name = $name;
        $deck->king_id = $kingId;

        $deck->save();
        return Redirect::to('tcg/deck/' . $deck->id);
    }

    protected function validateDeck($name, $kingId)
    {
        $validator = Validator::make(
            array(
                'name' => $name,
                'kingId' => $kingId,
            ),
            array(
                'name' => 'required|min:4|max:30',
                'kingId' => 'required|integer',
            )
        );
        $kingCardConfig = \Config::get('tcg.cards.' . $kingId);
        if ($validator->fails() || empty($kingCardConfig['isKing'])) {
            return $validator;
        }
        return true;
    }

    public function deck($deckId)
    {
        $deck = Deck::find($deckId);

        if ($deck->player_id != Auth::user()->id) {
            return Redirect::to('tcg/me');
        }

        $inDeck = Card::getCardsInDeck($deck->id);

        $myCardsForKing = Card::getMyCardsForKing($deck->king_id);
        $cardsForRender = Card::prepareCardsForRender($myCardsForKing, ['playable' => true]);
        View::share('myCards', $cardsForRender);

        View::share('deck', $deck);
        return View::make('games.tcg.player.deck');
    }



    /* TEMP STUFF */
    public function openBooster()
    {
        $allCards = \Config::get('tcg.cards');

        foreach ($allCards as $key => $card) {
            if (!empty($card['isKing'])) {
                unset($allCards[$key]);
            }
        }
        $playerId = Auth::user()->id;

        for($i =0; $i < 5; $i++) {
            $cardId = array_rand($allCards);
            $card = new Card();
            $card->card_id = $cardId;
            $card->player_id = $playerId;
            $card->fraction = $allCards[$cardId]['fraction'];

            $card->save();
        }
        return Redirect::to('tcg/me');


    }

}
