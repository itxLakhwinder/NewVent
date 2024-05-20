<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BlockedTopic extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;
    
    protected $table = 'blocked_topics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    
}
