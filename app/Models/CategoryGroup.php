<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CategoryGroup extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false;
    
    protected $table = 'category_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name', 'image', 'indexed'
    ];
	
	//public function users()
    //{
		//return $this->belongsToMany(\App\User::class, 'category_group_user', 'category_group_id', 'user_id')->withPivot(['user_id', 'category_group_id', 'status']);
    //}
    
}
