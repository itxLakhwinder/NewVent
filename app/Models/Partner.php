<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{

    protected $fillable = [
        'title', 'logo', 'short_description','decription','discount','category','topic','first_name','last_name','city','state','zip_code','service_type','name_on_card','card_number','exp_month','exp_year','count','url','lat','long','banner','visits','customer_id','plan','plan_amount','plan_date','plan_status'
    ];  
    
  
    public function user()
    {
		$lat_lng = $this->Lat_lng();
		return $this->hasOne('App\Models\PartnerUser', 'id', 'user_id');
    }

  //   public function user()
  //   {
		// $lat_lng = $this->Lat_lng();
		// // return $this->belongsTo('App\User', 'user_id');
		// return $this->hasOne('App\User', 'id', 'user_id');
		// // return $this->belongsTo('App\User', 'user_id')->select("6371 * acos(cos(radians(" . $lat_lng['lat'] . "))
		// // 		* cos(radians('lat')) 
		// // 		* cos(radians('long') - radians(" . $lat_lng['lng'] . ")) 
		// // 		+ sin(radians(" .$lat_lng['lat']. ")) 
		// // 		* sin(radians('lat'))) AS distance")
		// // 		->having('distance', '<', 20);
  //   }

    public function partnertypes(){
    	return $this->hasMany('App\Models\PartnerType', 'partner_id', 'id');
    }
	
	public function servicetype(){
    	return $this->hasMany('App\Models\ServiceType', 'partner_id', 'id');
    }
	
	/*public function getDistanceAttribute(){
		
	}*/
	
	public function Lat_lng(){
		$PublicIP = $this->get_client_ip();
		$json     = file_get_contents("http://ipinfo.io/$PublicIP/geo");
		$json     = json_decode($json, true);
		$country  = $json['country'];
		$region   = $json['region'];
		$city     = $json['city'];
		$postal   = $json['postal'];
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($postal)."&sensor=false&key=AIzaSyCJJtogRtzgsLgVwh169tUlytcXM_vHBis";

		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		$origLat='';
		$origLon='';
		if(!empty($result['results'])){
			$origLat = $result['results'][0]['geometry']['location']['lat'];
			$origLon = $result['results'][0]['geometry']['location']['lng'];
		}
		return ['lat' => $origLat, 'lng' => $origLon];
		
	}
	
	function get_client_ip()
	{
			$ipaddress = '';
			if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			} else if (isset($_SERVER['HTTP_FORWARDED'])) {
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			} else if (isset($_SERVER['REMOTE_ADDR'])) {
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			} else {
				$ipaddress = 'UNKNOWN';
			}

			return $ipaddress;
	}
    
}
