<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAnswer; 

class Question extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question', 'options', 'is_multiselect', 'status',
    ];


    public function getOptionsAttribute($value)
    {
        return unserialize($value);
    }

    public function answer()
    {
        return $this->hasMany('App\Models\UserAnswer');
    }
    
}
