<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WeeklyVentAnswers extends Authenticatable
{
    use HasApiTokens, Notifiable;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'weekly_vent_answers';
    protected $fillable = [
        'question_id', 'user_id', 'answer',
    ];
    
}
