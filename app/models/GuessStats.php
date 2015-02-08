<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

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
        	$from = date('Y-m-d H:i:s', $period[0]);
  			$to   = date('Y-m-d H:i:s', $period[1]);
        	$query = $query->whereBetween('created_at', array($from, $to));
        }    
        return $query->get()->toArray();
	}

	public static function getTotalStatistic()
	{
		$results = [];
		$results['totalGames'] = self::count();
        $results['avrPoints'] = round(self::avg('points'), 1);
        $results['answersTotal'] = self::sum('answers');
        $results['users'] = self::select(DB::raw('count(DISTINCT CONCAT(COALESCE(name,\'empty\'),ip)) as count'))
            ->pluck('count');

        return $results;    
	}

    public static function getHardestImage($period = array())
    {
        $query = self::select('image_id', DB::raw('count(1) as sum'))
            ->where('image_id', '!=', 0)
            ->orderBy('sum', 'desc')
            ->groupBy('image_id')
            ->orderBy('image_id')
            ->limit(1)
        ;
        if ($period) {
            $from = date( 'Y-m-d H:i:s', $period[0]);
            $to = date( 'Y-m-d H:i:s', $period[1]);
            $query = $query->whereBetween('created_at', array($from, $to));
        }
        $first = $query->first();
        if (!$first) {
            return self::getHardestImage();
        }
        return $first;
    }

    public static function getLastGames()
    {
        $query = self::select('images.url', 'answers', 'points')
            ->join('images', 'guess_stats.image_id', '=', 'images.id');
        $from = Carbon::now()->addMinutes(-15)->format('Y-m-d H:i:s');
        $to = Carbon::now()->addHours(2)->format('Y-m-d H:i:s');
        $query = $query->whereBetween('guess_stats.created_at', array($from, $to));
        return $query->get();
    }



}
