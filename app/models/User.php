<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class User extends Eloquent implements UserInterface, RemindableInterface {

    const GUEST_USER_SESSION_KEY = 'guestUser';

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    protected $appends = array('guest_id', 'guest_name');

    protected $guarded = array('guest_id', 'guest_name');



    public static function getUser()
    {
        if (Auth::check())
        {
            $user = Auth::user();
        } else {
            $user = User::getGuest();
        }
        return $user;
    }

    public static function getGuest()
    {
        $userData = Session::get(self::GUEST_USER_SESSION_KEY, null);
        if (!$userData) {
            // user is first time here
            $user = new User;
            self::saveUser($user);
        } else {
            $user = unserialize($userData);
        }
        return $user;
    }

    public static function saveUser(User $user)
    {
        if ($user->id) {
            $user->save();
        } else {
            Session::set(self::GUEST_USER_SESSION_KEY, serialize($user));
        }
    }

    public function getId()
    {
        if ($this->id) {
            return $this->id;
        } else {
            if ($this->guest_id === false) {
                $this->guest_id = mt_rand(100000, 999999) . '2';
                User::saveUser($this);
            }
            return $this->guest_id;
        }
    }

    public function getName()
    {
        if ($this->id) {
            return $this->name;
        } else {
            if ($this->guest_name === false) {
                $names = ['viking', 'dwarf', 'ranger', 'man', 'smith'];
                $who = $names[array_rand($names)];
                $types = ['wild', 'calm', 'brave', 'strange', 'fast', 'enraged', 'smart'];
                $type = $types[array_rand($types)];
                $this->guest_name = 'The ' . $type . ' ' . $who;
                User::saveUser($this);
            }
            return $this->guest_name;
        }
    }

    public function getGuestIdAttribute()
    {
        if (isset($this->attributes['guest_id'])) {
            return $this->attributes['guest_id'];
        }
        return false;
    }
    public function setGuestIdAttribute($guestId)
    {
        $this->attributes['guest_id'] = $guestId;
    }

    public function getGuestNameAttribute()
    {
        if (isset($this->attributes['guest_name'])) {
            return $this->attributes['guest_name'];
        }
        return false;
    }
    public function setGuestNameAttribute($guestName)
    {
        $this->attributes['guest_name'] = $guestName;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        if (!$this->id) {
            // we are creating user for the first time
            unset($this->attributes['guest_id']);
            unset($this->attributes['guest_name']);
            $this->attributes['password'] = Hash::make($this->attributes['password']);

            Session::forget(self::GUEST_USER_SESSION_KEY);
        }
        return parent::save($options);
    }

}
