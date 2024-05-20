<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\PartnerUser;
use App\User;
use DB;
use Illuminate\Database\Eloquent\Builder;

class PartnerController extends Controller
{

    public function index(Request $request)
    {
        $dist = 20;
        $near = 0;
        if($request->sort_by) {
            if (strpos($request->sort_by, 'Near') !== false) {
                $near = 1;
            }
        }

        $partners = Partner::select('id','user_id','short_description','description','logo','banner','url','discount','first_name','last_name','last_name','count','lat','long')->where('user_id', '!=', 0)->with(['user' => function ($query) {
            $query->select('id', 'name', 'phone_number');
        }])->with(['partnertypes', 'servicetype']);
		

		
        $sort_ser = 0;
        $arr = [];
        $arr_cat = [];
        if($request->filled('sort_by')) {
			$trimData = str_replace(' ', '', $request->sort_by);
            $mul = explode(',', $request->sort_by);
			
			
			
            foreach ($mul as $key => $value) {
                $value = trim($value);
				
                if($value == 'Online' || $value == 'Physical location/local') {
                    array_push($arr, $value);
                } else {
                    if($value != 'Near me') {
                        array_push($arr_cat, $value);
                    }
                }
            }
			
				
			
			
			
            if(count($arr)) {
                $sort_ser = 1;   
                $partners->whereHas('servicetype', function (Builder $query) use($arr) {
                    $query->whereIn('service', $arr);          
                });
            }  
			

            if(count($arr_cat)) {
                $partners->whereHas('partnertypes', function (Builder $query) use($arr_cat) {
                    $query->where('type', '=', 'category')->whereIn('category', $arr_cat);
                });
            }    
        }

        $partners->whereHas('user', function (Builder $query) {
            // $query->where('status', '=', 1)->where('user_type', '=', 'Partner');          
            $query->where('status', '=', 1);          
        });

        $filter = 0;
        if($request->filled('filter_by')) {
			
			$tm = str_replace(' ', '', $request->filter_by);
			
            $mul = explode(',', $tm);
			
            $filter = 1;
            $partners->whereHas('partnertypes', function (Builder $query) use($mul) {
				
                $query->where('type', '=', 'topic')->whereIn('category', $mul);
            });
        }

        $data = $partners->orderBy('created_at', 'DESC')->get();
		
		//dd( $data);
		
		
		//dd($data);
        if($near > 0) {
            $temp = [];
            foreach ($data as $key => $value) {
                if($value->lat && $value->long) {
                    $dis = $this->distance(@$request->lat, @$request->long, $value->lat,  $value->long, "M");    
                    if($dis >= 0 && $dis <= $dist) {
                        array_push($temp, $value);
                    }
                }
            }
            $data = $temp;
        } 

        // if($sort_ser) {
        //     $temp = [];
        //     $mul = $arr;
        //     foreach ($data as $key => $value) {
        //         if(count($value->servicetype) == count($mul)) {
        //             array_push($temp, $value);
        //         }
        //     }
        //     $data = $temp;
        // }
		
		

        if($filter) {
            $temp = [];
            $mul = explode(',', $request->filter_by);
			
            foreach ($data as $key => $value) {
                $cc = 0;
                if($value->partnertypes) {
                    foreach ($value->partnertypes as $k => $val) {
                        if($val->type == 'topic') {
                            $cc++;
                        }
                    }
                }
                if($cc == count($mul)) {
                    array_push($temp, $value);
                }                
            }
        }
		
		
		//dd($mul, $data, $near, $filter);
			

        // $partners = Partner::with('partnertypes');
        // $partners = User::where('user_type', 'Partner')->with('partner');
        //return $partners= User::where('user_type', 'Partner')->with('partner');
                    
        // return $partners= User::with(['partner' => function($query) {
        //     $query->where('id', 7);
            
        // }])->get();
            /*($partners->wherehas(['partner' => function($q) use ($dist){
                $q->where('id', '=', 7);
            }])->get();*/
        // echo "<pre>";
        // print_r($partners);
        // die;    
            
        // if(isset($request->sort_by)){      
        //     $partners->where('category', $request->sort_by);      
        // } elseif (isset($request->filter_by)) {       
        //      $filters = explode(',', $request->filter_by);
                
        //      foreach($filters as $key => $val){
        //      $partners->orWhere('topic', 'like', '%' . $val . '%');
        //     }            
        // }
        
        // if(isset($request->sort_by)){      
        //     $partners->wherehas(['partner' => function($query) use ($request){
        //         $query->where('category', $request->sort_by);            
        //     }])->get()->toArray();        
        // } elseif (isset($request->filter_by)) {       
        //     $filter=explode(',', $request->filter_by);
        //     $partners->with(['partner' => function($q)  use ($filter){
        //         $q->whereIn('topic', $filter);
        //     }]);
        // }
        
        // $data = $partners->select('users.id','users.name','users.phone_number')
        //             ->where('users.user_type','Partner')->get();
            

        return response()->json(["ReturnCode" => 1,
            "ReturnMessage" => "Partners List.",            
            "data" => $data
        ], 200);
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
          return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
      } else {
          return $miles;
      }
    }
        
  //   public function index(Request $request)
  //   {
        
		// $dist = 10;
		// //$partners= Partner::with('user');
		// //return $partners= User::where('user_type', 'Partner')->with('partner');
					
		// return $partners= User::with(['partner' => function($query) {
		// 	$query->where('id', 7);
			
		// }])->get();
		// 	/*($partners->wherehas(['partner' => function($q) use ($dist){
		// 		$q->where('id', '=', 7);
		// 	}])->get();*/
			
		  
		
  //     if(isset($request->sort_by)){		 
  //       $partners->wherehas(['partner' => function($query) use ($request){
  //           $query->where('category', $request->sort_by);            
  //       }])->get()->toArray();		  
	 //  }
		   
  //       elseif (isset($request->filter_by)) {       
  //           $filter=explode(',',$request->filter_by);
  //           $partners->with(['partner' => function($q)  use ($filter){
  //               $q->whereIn('topic', $filter);
  //           }]);
		// }
		
		// $data = $partners->select('users.id','users.name','users.phone_number')
		// 	->where('users.user_type','Partner')->get();
			

  //       return response()->json(["ReturnCode" => 1,
  //           "ReturnMessage" => "Partners List.",
  //           "data" => $data
  //       ], 200);
  //   }
		
	
	
    public function count(Request $request)
    {
        
        $partner = Partner::find($request->id);
        
        if($partner) {
            if($request->count){
                $partner->count = $partner->count + 1;    
            }

            if($request->visits){
				$partner->visits = $partner->visits + 1;       
            }			
			$partner->save();
        }

   
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Partner count updated.",
            "data" => $partner
        ], 200);
    }


    public function view(Request $request)
    {
        $partner=PartnerUser::with('partner')->find($request->id);
        return response()->json(["ReturnCode" => 1,
        "ReturnMessage" => "Partner Details.",
        "data" => $partner
    ], 200);
    }
}
