<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Partner;
use Illuminate\Foundation\Auth\User as Model;

class PartnerUser extends Model
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'email', 'username', 'phone_number', 'email_verified_at', 'password', 'remember_token', 'status', 'birth_year', 'state', 'profile_pic', 'favorites_notification', 'relates_notification', 'comments_notification', 'bookmark_notification', 'following_notification', 'device_type', 'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function partner()
    {
		return $this->hasOne(Partner::class, 'user_id', 'id');
    }

}
