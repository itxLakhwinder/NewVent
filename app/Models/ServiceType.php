<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{

	protected $table="servicetypes";

    public $timestamps = false;

    protected $fillable = [
        'partner_id', 
        'service'
    ];  
    
    
}
