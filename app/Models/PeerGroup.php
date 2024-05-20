<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PeerGroup extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;
    
    protected $table = 'peer_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name', 'image', 'indexed'
    ];
	
	public function users()
    {
		return $this->belongsToMany(\App\User::class, 'peer_group_user', 'peer_group_id', 'user_id')->withPivot(['user_id', 'peer_group_id', 'status']);
    }
	
	public function topics()
    {
		return $this->belongsToMany(\App\Models\Topic::class, 'peer_group_topic', 'peer_group_id', 'topic_id')->withPivot(['topic_id', 'peer_group_id', 'type', 'status']);
    }
    
}
