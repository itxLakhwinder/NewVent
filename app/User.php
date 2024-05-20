<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Partner;
use App\Models\WeeklyVentAnswers;
use App\Models\UserAnswer;
use App\Models\Group;
use App\Models\Topic;
use App\Models\PeerGroup;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'email', 'username', 'phone_number', 'email_verified_at', 'password', 'remember_token', 'status', 'birth_year', 'state', 'profile_pic', 'favorites_notification', 'relates_notification', 'comments_notification', 'bookmark_notification', 'following_notification', 'device_type', 'device_token','user_type','address', 'reason', 'notification_setting',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	protected $appends = ['notification_check'];
    
    public function partner()
    {
		return $this->hasOne(Partner::class, 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(UserAnswer::class, 'user_id', 'id');
    }
    public function weekly_vent_answers()
    {
        return $this->hasMany(WeeklyVentAnswers::class, 'user_id', 'id');
    }
    public function story()
    {
        return $this->hasMany(Topic::class, 'user_id', 'id');
    }
	
	public function getNotificationSetting()
	{
		return ['all_notification' => true, 'rooms' => true, 'feed' => true, 'story' => true, 'user_reply' => true];
	}
	
	public function groups()
    {
		return $this->belongsToMany(PeerGroup::class, 'peer_group_user', 'user_id', 'peer_group_id')->withPivot(['user_id', 'peer_group_id', 'status']);
    }
	
	public function getNotificationCheckAttribute()
    {
        if (!empty($this->notification_setting)) {
			$data = explode(',', $this->notification_setting);
        } else {
			$data = [];
        }

        return $data;
    }



}
