<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Topic extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'title', 'details', 'sub_menu', 'user_id', 'status','image','feedback_type','available_topic','category_group','type','content_type','post_type','created_at','updated_at',
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\JournalComments');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	public function groups()
    {
		return $this->belongsToMany(\App\Models\PeerGroup::class, 'peer_group_topic', 'topic_id', 'peer_group_id')->withPivot(['topic_id', 'peer_group_id', 'type', 'status']);
    }
    
}
