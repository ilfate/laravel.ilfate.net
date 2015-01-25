<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class SeriesImage extends Eloquent implements RemindableInterface {

	use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'images';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

    protected $appends = array();

    protected $guarded = array();

    public static function getPicture($difficulty, $seriesId = null)
    {
        $query = self::where('difficulty', '<=', $difficulty);
        if ($seriesId) {
            $query = $query->where('series_id', '=', $seriesId);
        }
        $image = $query->orderByRaw("RAND()")->first();
        return $image->url;
    }

}
