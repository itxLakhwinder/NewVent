<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\CategoryGroup; 
use App\Models\AvailableTopics; 
use App\Models\WeeklyVent; 

class WeeklyVentTitle extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'weekly_vent_titles';
    public function questions()
    {
        return $this->hasMany(WeeklyVent::class,'title_id');
    }

}