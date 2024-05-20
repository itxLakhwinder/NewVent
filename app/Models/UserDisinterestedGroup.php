<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserDisinterestedGroup extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $guarded=[];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

}