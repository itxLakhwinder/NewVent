<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\WeeklyVentTitle;
use App\Models\WeeklyVentAnswers; 

class WeeklyVent extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'weekly_vent_questions';

    protected $fillable = [
        'question', 'options', 'is_multiselect', 'status',
    ];

    public function getOptionsAttribute($value)
    {
        return unserialize($value);
    }
    public function title()
    {
        return $this->belongsTo(WeeklyVentTitle::class);
    }
    public function answers()
    {
        return $this->hasMany(WeeklyVentAnswers::class,'question_id');
    }

}