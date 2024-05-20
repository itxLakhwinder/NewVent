<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserLoginLog extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;
    protected $table = 'user_login_logs';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'user_id', 
        'start_time',
        'end_time',
		'tim_diff',
    ];
    
}
