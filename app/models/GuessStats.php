<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class GuessStats extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'guess_stats';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	public static function getTopStatistic($period = array())
	{
		$query = self::select('name', 'answers', 'points')
            ->orderBy('points', 'desc')
            ->limit(10);
        if ($period) {
        	$from = date( 'Y-m-d H:i:s', $period[0]);
  			$to = date( 'Y-m-d  H:i:s', $period[1]);
        	$query = $query->whereBetween('created_at', array($from, $to));
        }    
        return $query->get();
	}

}
