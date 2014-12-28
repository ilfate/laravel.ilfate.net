<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class Battle extends Eloquent implements RemindableInterface
{

    const BATTLE_STATUS_NONE = 0;
    const BATTLE_STATUS_INIT = 1;
    const BATTLE_STATUS_GAME_READY = 2;


    const BATTLE_STATUS_GAME_LOST = 91; // real problems happen
    use RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'battles';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    protected $appends = array();

    protected $guarded = array();


    public function getPlayerWithDecks()
    {
        $battlePlayers = BattlePlayer::where('battle_id', '=', $this->id)->get();
        foreach ($battlePlayers as &$battlePlayer) {
            $deck = Deck::find($battlePlayer->deck_id);
            $deck->cards = Card::getCardsInDeck($battlePlayer->deck_id, $battlePlayer->player_id);
            $battlePlayer->deck = $deck;
            $deck->king = Card::where('player_id', '=', $battlePlayer->player_id)->where('card_id', '=', $deck->king_id)->first();
        }
        return $battlePlayers;
    }

    public static function findMyBattle()
    {
        return Battle::select('battles.*')->where('battle_players.player_id', '=', Auth::user()->id)
            ->join('battle_players', 'battles.id' , '=', 'battle_players.battle_id')
            ->where('battles.status_id', '<', 10)
            ->first();
    }
}
