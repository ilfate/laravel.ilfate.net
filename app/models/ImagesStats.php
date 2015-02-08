<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class ImagesStats extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'images_stats';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

    public static function getHardestImage($period = array())
    {
        $query = self::select('image_id', DB::raw('count(1) as sum'))
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

}
