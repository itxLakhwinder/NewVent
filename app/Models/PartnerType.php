<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerType extends Model
{

	protected $table="partnertypes";

    protected $fillable = [
        'partner_id', 'category', 'type'
    ];  
    
    
}
