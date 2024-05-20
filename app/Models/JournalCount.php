<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class JournalCount extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;

    protected $table = 'journal_counts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'user_id','journal_id','topic_id','health_id','podcast_id','type','created_at'
    ];
    
}
