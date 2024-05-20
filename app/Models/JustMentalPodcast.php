<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class JustMentalPodcast extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;
    protected $table = 'justmental_podcast';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'category_id', 
        'video_title',
        'status',
        'hyperlink',
		'count',
		'visits',
    ];
    
}
