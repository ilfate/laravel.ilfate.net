<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class Card extends Eloquent implements RemindableInterface {

    use RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cards';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    protected $appends = array();

    protected $guarded = array();

    public static function getMyCardsCount()
    {
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $myCardsCount = self::where('player_id', '=', $player->id)->count();
    }

    public static function getCardsInDeck($deckId)
    {
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $cards = self::where('player_id', '=', $player->id)
            ->where('deck_cards.deck_id', '=', $deckId)
            ->join('deck_cards', 'cards.id' , '=', 'deck_cards.card_id')
            ->get();
        return $cards;
    }

    public static function getMyKings()
    {
        $kingsIds = \Config::get('tcg.kingsIds');
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $cards = self::where('player_id', '=', $player->id)
            ->whereIn('card_id', $kingsIds)
            ->get();
        return $cards;
    }

    /**
     *
     */
    public static function prepareCardsForRender($cards)
    {
        $cardsResult = [];
        foreach ($cards as $card) {
            $config = \Config::get('tcg.cards.' . $card->card_id);
            $cardsResult[] = [
                'id' => $card->card_id,
                'config' => $config,
                'unit' => \Config::get('tcg.units.' . $config['unit']),
                'spell' => \Config::get('tcg.spells.' . $config['spell']),
            ];
        }
        return $cardsResult;
    }


}
