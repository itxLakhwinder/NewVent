<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Topic;
use App\Models\PeerGroup;

class Report extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'user_id', 'post_id', 'group_id', 'subject','image','detail','created_at', 'type',
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
	
	public function topic()
    {
        return $this->hasOne(Topic::class, 'id', 'post_id');
    }
	
	public function group()
    {
        return $this->hasOne(PeerGroup::class, 'id', 'group_id');
    }
}
