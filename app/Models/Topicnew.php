<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Topicnew extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;

    protected $table = "topicsnew";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'title', 'details', 'sub_menu', 'user_id', 'status','image','feedback_type','available_topic','type','created_at','updated_at',
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\JournalComments');
    }
    
}
