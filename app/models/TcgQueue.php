<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class TcgQueue extends Eloquent implements RemindableInterface
{

    use RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'queue';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    protected $appends = array();

    protected $guarded = array();

    public static function add($deckId, $gameTypeId = 0)
    {
        $queue               = new TcgQueue();
        $queue->player_id    = Auth::user()->id;
        $queue->deck_id      = $deckId;
        $queue->game_type_id = $gameTypeId;

        $queue->save();
    }

    public static function getMyQueue()
    {
        return TcgQueue::where('player_id', '=', Auth::user()->id)->first();
    }
}
