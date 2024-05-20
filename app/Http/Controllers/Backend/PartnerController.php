<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Resource;
use App\Models\Partner;
use App\Models\ServiceType;
use App\Models\AvailableTopics;

use App\Models\CategoryGroup;
use App\Models\PartnerUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Stripe\Stripe;
use App\Mail\Approve;
use App\Mail\DisApprove;
use App\Mail\PartnerWelcomeMail;
use App\Mail\PartnerWelcomeMailAdmin;
use Mail;

class PartnerController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $topics = AvailableTopics::where(['type'=>'adults'])->orderBy('indexed', 'ASC')->get();
		$categoryGroups = CategoryGroup::orderBy('indexed', 'ASC')->get();
		
        $partners = PartnerUser::with(['partner','partner.partnertypes', 'partner.servicetype'])->orderBy('created_at', 'DESC')
        ->get();
		
		//dd($topics, $partners);
		
        return view('partners.list')->with([ "partners" => $partners,"topics"=>$topics, 'categoryGroups' => $categoryGroups]);

    }


    public function store(Request $request)
    {   

        $validated = $request->validate([
            'email' => 'required|unique:partner_users|max:50',
        ]);
		
		
		
        $user = new PartnerUser;
        $user->name = $request->title;
        $user->email = $request->email;
        // $user->phone_number=$request->phone;
        $user->password = Hash::make($request->password);
        $user->save();

        $partner = new Partner;
        $partner->user_id = $user->id;
       
        if($request->has('logo')) {
            $file = $request->file('logo');
			
			
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileNameLogo = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameLogo = 'logo'.time().'.'. $fileExtension;
                $file->move($path, $fileNameLogo); 
                $partner->logo =  env('APP_URL').'uploads/partner_files/'.$fileNameLogo;  
            }
        }

        if($request->has('banner')) {
            $file = $request->file('banner');
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileNameBanner = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameBanner = 'banner'.time().'.'. $fileExtension;
                $file->move($path, $fileNameBanner); 
                $partner->banner =  env('APP_URL').'uploads/partner_files/'.$fileNameBanner;  
            }
        }
		
		//dd($partner);

        //$partner->first_name=$request->first_name;
        //$partner->last_name=$request->last_name;
        //$partner->city=$request->city;
        //$partner->state=$request->state;
        //$partner->zip_code=$request->zip_code;
        // $partner->service_type=$request->service_type;
        //$partner->name_on_card=$request->name_on_card;
        //$partner->card_number=$request->card_number;
        //$partner->exp_month=$request->exp_month;
        //$partner->exp_year=$request->exp_year;
        $partner->url = $request->url;

        $partner->short_description=$request->short_description;
        $partner->description=$request->description;
        // $partner->category=$catgry;
        // $partner->topic=$topic;
        $partner->discount=$request->discount;

        //$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($request->zip_code)."&sensor=false&key=AIzaSyAQKT-baFr1GJfDvhCjhN2HLMxmyNbTcys";

        //$result_string = file_get_contents($url);
        //$result = json_decode($result_string, true);
        //$zipLat = "";
        //$ziplng = "";
        //if(!empty(@$result['results'])){
          //  $zipLat = $result['results'][0]['geometry']['location']['lat'];
           // $ziplng = $result['results'][0]['geometry']['location']['lng'];
        //}
        //$partner->lat=@$zipLat;
        //$partner->long=@$ziplng;
		
        $partner->save();

        if($request->category && count($request->category)) {
            foreach ($request->category as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'category']
                    );
                }
            }  
        }

        if($request->topic && count($request->topic)) {
            foreach ($request->topic as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'topic']
                    );
                }
            }            
        }

        if($request->service_type && count($request->service_type)) {
            foreach ($request->service_type as $key => $val) {
                if($val) {
                    DB::table('servicetypes')->insert(
                        ['partner_id' => $partner->id, 'service' => $val]
                    );
                }
            }  
        }

        return redirect('/partners')->with('success', 'Partner added successfully.');

    }

    public function approve(Request $request)
    {  
        $user = PartnerUser::find($request->id);
        $partner = Partner::where(['user_id' => $request->id])->first();
        if($user) {
            $message = "";

            $user->status = 1;
            $user->save();

            Mail::to([$user->email])->send(new Approve());

            return response()->json([
                "status" => true,
                "message" => 'Partner approved successfully.',
            ], 200);

        } else {
            return response()->json([
                "status" => false,
                "message" => 'Partner not found.',
            ], 200);
        }
    }

    public function disapprove(Request $request)
    {  
        $user = PartnerUser::find($request->id);
        $partner = Partner::where(['user_id' => $request->id])->first();
        if($user) {
            $message = "";

            $user->status = 0;
            $user->save();

            Mail::to([$user->email])->send(new DisApprove());

            return response()->json([
                "status" => true,
                "message" => 'Partner approved successfully.',
            ], 200);

        } else {
            return response()->json([
                "status" => false,
                "message" => 'Partner not found.',
            ], 200);
        }
    }

    // public function approve(Request $request)
    // {  
    //     $user = PartnerUser::find($request->id);
    //     $partner = Partner::where(['user_id' => $request->id])->first();
    //     if($user && $partner && $partner->customer_id) {
    //         $message = "";

    //         try {

    //             $charge  = \Stripe\Charge::create(array(
    //               "amount" => ((int) $partner->plan_amount) * 100,
    //               "currency" => "usd",
    //               "customer" => $partner->customer_id
    //             ));

    //             $user->status = 1;
    //             $user->save();

    //             Partner::where(['user_id' => Auth::guard('partner')->user()->id])->update([
    //                 "plan_date" => date("Y-m-d"),
    //                 "plan_status" => $charge->status,
    //             ]);

    //             DB::table('payments')->insert([
    //                 'user_id' => $partner->user_id, 
    //                 'date' => date("Y-m-d"), 
    //                 'status' => @$charge->status,
    //                 'tid' => @$charge->id,
    //             ]);

    //             Mail::to([$user->email])->send(new Approve());

    //             return response()->json([
    //                 "status" => true,
    //                 "message" => 'Partner approved successfully.',
    //             ], 200);

    //         } catch (\Stripe\Exception\RateLimitException $e) {
    //             $message = $e->getMessage();
    //         } catch (\Stripe\Exception\InvalidRequestException $e) {
    //             $message = $e->getMessage();
    //         } catch (\Stripe\Exception\AuthenticationException $e) {
    //             $message = $e->getMessage();
    //         } catch (\Stripe\Exception\ApiConnectionException $e) {
    //             $message = $e->getMessage();
    //         } catch (\Stripe\Exception\ApiErrorException $e) {
    //             $message = $e->getMessage();
    //         } catch (Exception $e) {
    //             $message = $e->getMessage();
    //         }
          
    //         return response()->json([
    //             "status" => false,
    //             "message" => $message,
    //         ], 200);
            
    //     } else {
    //         return response()->json([
    //             "status" => false,
    //             "message" => 'Try again later.',
    //         ], 200);
    //     }   

    // }

    public function update(Request $request)
    {
      $user = PartnerUser::find($request->id);
      if($request->password) {
        $user->password = Hash::make($request->password);
      }
      $partner = Partner::where('user_id',$user->id)->first();
      
        $catgry = implode(',', $request->category);
        $topic = implode(',', $request->topic);

        $user->name = $request->title;
        $user->email = $request->email;
        //$user->user_type='Partner';
        $user->save();
        
        if($request->has('logo')) {
            $file = $request->file('logo');
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $partner->logo = env('APP_URL').'uploads/partner_files/'.$fileName;      
            }
        }

        if($request->has('banner')) {
            $file = $request->file('banner');
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $partner->banner =  env('APP_URL').'uploads/partner_files/'.$fileName;  
            }
        }

        //$partner->first_name=$request->first_name;
        //$partner->last_name=$request->last_name;
        //$partner->city=$request->city;
        //$partner->state=$request->state;
        //$partner->zip_code=$request->zip_code;
        //$partner->name_on_card=$request->name_on_card;
        //$partner->card_number=$request->card_number;
        //$partner->exp_month=$request->exp_month;
        //$partner->exp_year=$request->exp_year;

        $partner->short_description=$request->short_description;
        $partner->description=$request->description;

        $partner->discount=$request->discount;
        $partner->url=$request->url;

        if($request->category && count($request->category)) {
            DB::table('partnertypes')->where(['partner_id' => $partner->id, 'type' => 'category'])->delete();
            foreach ($request->category as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'category']
                    );
                }
            }  
        }
        if($request->topic && count($request->topic)) {
            DB::table('partnertypes')->where(['partner_id' => $partner->id, 'type' => 'topic'])->delete();
            foreach ($request->topic as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'topic']
                    );
                }
            }            
        }
        
        if($request->service_type && count($request->service_type)) {
            DB::table('servicetypes')->where(['partner_id' => $partner->id])->delete();
            foreach ($request->service_type as $key => $val) {
                if($val) {
                    DB::table('servicetypes')->insert(
                        ['partner_id' => $partner->id, 'service' => $val]
                    );
                }
            }            
        }


        $partner->save();
        return redirect('/partners')->with('success', 'Partner Updated successfully.');
    }

    
    public function delete($id)
    {   
        PartnerUser::where(['id' => $id])->delete();
        Partner::where(['user_id' => $id])->delete();
        return redirect('/partners')->with('success', 'Partner deleted successfully.');
    }


    public function register(Request $request)
    {   

        $validated = $request->validate([
            'company_name' => 'required',
            'company_website' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'service_type' => 'required',
            // 'recaptcha' => 'required',
            'email' => 'required|email|unique:partner_users',
            'password' => 'required|confirmed|min:6',            
        ]);

        // $url = 'https://www.google.com/recaptcha/api/siteverify';
        // $remoteip = $_SERVER['REMOTE_ADDR'];
        
        // $data = [
        //     'secret' => env('RECAPTCHA_SECRET'),
        //     'response' => $request->get('recaptcha'),
        //     'remoteip' => $remoteip
        // ];

        // $options = [
        //     'http' => [
        //       'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        //       'method' => 'POST',
        //       'content' => http_build_query($data)
        //     ]
        // ];

        // $context = stream_context_create($options);
        // $result = file_get_contents($url, false, $context);
        // $resultJson = json_decode($result);

        // if ($resultJson->success != true) {
        //     return redirect()->back()->with('error', 'Error while getting google recaptcha score.');
        // }
        // if ($resultJson->score >= 0.3) {
                
            $specializes = "";
        
            if($request->has('specialize')) {
                $specializes = implode(",", $request->specialize);
            } 

            $user = new PartnerUser;
            $user->name = $request->company_name;
            $user->email = $request->email;
            $user->status = 0;
            $user->password = Hash::make($request->password);
            $user->save();

            $partner = new Partner;
            $partner->user_id = $user->id;
            $partner->first_name=$request->first_name;
            $partner->last_name=$request->last_name;
            $partner->city=$request->city;
            $partner->state=$request->state;
            $partner->zip_code=$request->zip_code;
            $partner->company_website=$request->company_website;
            $partner->in_person=$request->InPerson;
            $partner->specialize=$specializes;
            $partner->plan=$request->plan;
            $partner->plan_amount = ($request->plan == 'anually') ? env('PLAN_ANUALLY') : env('PLAN_MONTHLY');
            $partner->plan_date = date("Y-m-d");
            $partner->plan_status = "pending";

            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($request->zip_code)."&sensor=false&key=AIzaSyAQKT-baFr1GJfDvhCjhN2HLMxmyNbTcys";

            $result_string = file_get_contents($url);
            $result = @json_decode($result_string, true);
            $zipLat = "";
            $ziplng = "";
            if($result) {
                if(!empty(@$result['results'])){
                    $zipLat = $result['results'][0]['geometry']['location']['lat'];
                    $ziplng = $result['results'][0]['geometry']['location']['lng'];
                }
            }
            $partner->lat = $zipLat;
            $partner->long = $ziplng;
            $partner->save();

            Mail::to([$request->email])->send(new PartnerWelcomeMail($partner));
            Mail::to(['vent@ventspaceapp.com'])->send(new PartnerWelcomeMailAdmin($user));

            if($request->service_type && count($request->service_type)) {
                foreach ($request->service_type as $key => $val) {
                    if($val) {
                        DB::table('servicetypes')->insert(
                            ['partner_id' => $partner->id, 'service' => $val]
                        );
                    }
                }  
            }

            if(@$user->id) {
                Auth::guard('partner')->loginUsingId($user->id);
                return redirect()->intended('/partner-profile');
            } else {
                return redirect('/partner-login')->with('success', 'You are registered successfully.');
            }

        // } else {
        //     // return back()->withErrors(['captcha' => 'ReCaptcha Error']);
        //     return redirect()->back()->with('error', 'Error while getting google recaptcha score.');
        // }

    }

    public function login(Request $request)
    {   

        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
 
        $credentials = $request->only('email', 'password');
        // $credentials['status'] = 1;
        if (Auth::guard('partner')->attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('/partner-analytics');
        }
        return redirect('/partner-login')->with('success', 'You have entered invalid credentials.');
    }

    public function getBilling()
    {   
        $partner = Partner::where(['user_id' => Auth::guard('partner')->user()->id])->first();
        $user = PartnerUser::find(Auth::guard('partner')->user()->id);
        return view('partner-billing', [ "partner" => $partner,"user" => $user]);
    }

    public function postBilling(Request $request)
    {   
        $validated = $request->validate([
            'name_on_card' => 'required',
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required',
        ]);

        $message = "";

        try {

            $token = \Stripe\Token::create([
                "card" => array(
                    'name' => $request->name_on_card,
                    "number" => $request->card_number,
                    "exp_month" => $request->exp_month,
                    "exp_year" => $request->exp_year,
                    "cvc" => $request->cvv
                ),
            ]);

            $customer = \Stripe\Customer::create(
                [
                    'source' => $token['id'],
                    'email' =>  Auth::guard('partner')->user()->email,
                    'description' => 'My name is '. $request->card_number. '',
                ]
            );

            if(@$customer->id) {
                $input = [
                    "name_on_card" => $request->name_on_card,
                    "card_number" => $request->card_number,
                    "exp_month" => $request->exp_month,
                    "exp_year" => $request->exp_year,
                    "customer_id" => $customer->id
                ];

                Partner::where(['user_id' => Auth::guard('partner')->user()->id])->update($input);
                return redirect()->back()->with('success', 'Card details saved successfully.');
            } else {
                return redirect()->back()->with('error', "Please try again later.");
            }

        } catch (\Stripe\Exception\RateLimitException $e) {
            $message = $e->getMessage();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $message = $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $message = $e->getMessage();
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $message = $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $message = $e->getMessage();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return redirect()->back()->with('error', $message);

    }


    public function getAccount()
    {   
        $partner = Partner::where(['user_id' => Auth::guard('partner')->user()->id])->first();
        $user = PartnerUser::find(Auth::guard('partner')->user()->id);

        return view('partner-account', [ "partner" => $partner,"user" => $user]);
    }

    public function postAccount(Request $request)
    {   
        $validated = $request->validate([
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        $input = [
            "first_name" => $request->first_name,
            "last_name" => $request->last_name
        ];

        Partner::where(['user_id' => Auth::guard('partner')->user()->id])->update($input);

        if($request->filled('password') && $request->filled('new_password') ) {
            // The passwords matches
            if (!(Hash::check($request->get('password'), Auth::guard('partner')->user()->password))) {
                return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
            }

            if(strcmp($request->get('password'), $request->get('new_password')) == 0) {
                //Current password and new password are same
                return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
            }
            //Change Password
            $user = Auth::guard('partner')->user();
            $user->password = bcrypt($request->get('new_password'));
            $user->save();
        }

        return redirect()->back()->with('success', 'Your Account details are updated succesfully.');

    }

    public function getProfile()
    {   
        $partner = Partner::with(['partnertypes', 'servicetype'])->where(['user_id' => Auth::guard('partner')->user()->id])->first();


        $category = [];
        $topic = [];
        if ($partner->partnertypes) {
            foreach ($partner->partnertypes as $key => $value) {
                if($value->type == "category") {
                  array_push($category, $value->category);
                }
                if($value->type == "topic") {
                  array_push($topic, $value->category);
                }
            }
        }
        $service = [];
        if ($partner->servicetype) {
            foreach ($partner->servicetype as $key => $value) {
                array_push($service, $value->service);          
            }
        }
       
     

        $user = PartnerUser::find(Auth::guard('partner')->user()->id);

        $topics= AvailableTopics::where(['type'=>'adults'])->orderBy('indexed', 'ASC')->get();
		$categoryGroups = CategoryGroup::orderBy('indexed', 'ASC')->get();

        return view('partner-profile', [ "partner" => $partner, "user" => $user, "topics" => $topics, 'categoryGroups' => $categoryGroups, 'service' => implode(',', $service), 'category' => implode(',', $category), 'topic' => implode(',', $topic)]);

    }


    public function postProfile(Request $request)
    {   

        $user = PartnerUser::find(Auth::guard('partner')->user()->id);
        $user->name = $request->company_name;
        $user->save();

        $partner = Partner::where('user_id',$user->id)->first();
        
        if($request->has('logo')) {
            $file = $request->file('logo');
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $partner->logo = env('APP_URL').'uploads/partner_files/'.$fileName;      
            }
        }

        if($request->has('banner')) {
            $file = $request->file('banner');
            $path = public_path('uploads/partner_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $partner->banner =  env('APP_URL').'uploads/partner_files/'.$fileName;  
            }
        }
        
        $specializes = "";
        
        if($request->has('specialize')) {
            $specializes = implode(",", $request->specialize);
        } 

        $partner->city=$request->City;
        $partner->state=$request->state;
        $partner->zip_code=$request->zip_code;
        $partner->in_person=$request->InPerson;
        $partner->short_description = $request->short_description;
        $partner->description = $request->description;
        $partner->discount = $request->discount;
        $partner->url = $request->url;
        $partner->specialize=$specializes;

        if($request->category && count($request->category)) {
            DB::table('partnertypes')->where(['partner_id' => $partner->id, 'type' => 'category'])->delete();
            foreach ($request->category as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'category']
                    );
                }
            }  
        }
        if($request->topic && count($request->topic)) {
            DB::table('partnertypes')->where(['partner_id' => $partner->id, 'type' => 'topic'])->delete();
            foreach ($request->topic as $key => $val) {
                if($val) {
                    DB::table('partnertypes')->insert(
                        ['partner_id' => $partner->id, 'category' => $val, 'type' => 'topic']
                    );
                }
            }            
        }
        
        DB::table('servicetypes')->where(['partner_id' => $partner->id])->delete();

        if($request->service_type && count($request->service_type)) {
            foreach ($request->service_type as $key => $val) {
                if($val) {
                    DB::table('servicetypes')->insert(
                        ['partner_id' => $partner->id, 'service' => $val]
                    );
                }
            }            
        }

        $partner->save();
        
        return redirect()->back()->with('success', 'Your profile information updated successfully.');
    }

    public function getAnalytics()
    {   
        $partner = Partner::where(['user_id' => Auth::guard('partner')->user()->id])->first();
        $user = PartnerUser::find(Auth::guard('partner')->user()->id);

        return view('partner-analytics', [ "partner" => $partner, "user" => $user]);
    }

    public function logout () {
        //logout user
        Auth::guard('partner')->logout();
        // redirect to homepage
        return redirect('/partner-login');
    }

    public function cron(Request $request)
    {  

        $partners = Partner::whereNotNull('customer_id')->get();
        if(count($partners)) {
            foreach ($partners as $key => $partner) {
                $message = "";
                try {

                    $date = date("Y-m-d");

                    $payments = DB::table('payments')->where(['user_id' => $partner->user_id, 'date' => $date, 'status' => 'succeeded' ])->get();

                    if(count($payments) == 0) {

                        if($partner->plan_date == $date) {

                            $charge = \Stripe\Charge::create(array(
                              "amount" => ((int) $partner->plan_amount) * 100,
                              "currency" => "usd",
                              "customer" => $partner->customer_id
                            ));

                            Partner::where(['user_id' => $partner->user_id])->update([
                                "plan_date" => $date,
                                "plan_status" => @$charge->status,
                            ]);

                            DB::table('payments')->insert([
                                'user_id' => $partner->user_id, 
                                'date' => $date, 
                                'status' => @$charge->status,
                                'tid' => @$charge->id,
                            ]);
                            // Mail::to([$user->email])->send(new Approve());
                            echo "cron success ".$partner->user_id;
                        }

                    }
 

                } catch (\Stripe\Exception\RateLimitException $e) {
                    $message = $e->getMessage();
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $message = $e->getMessage();
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $message = $e->getMessage();
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $message = $e->getMessage();
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $message = $e->getMessage();
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }

                if(!empty($message)) {
                    $date = date("Y-m-d");
                    Partner::where(['user_id' => $partner->user_id])->update([
                        "plan_date" => $date,
                        "plan_status" => 'failed',
                    ]);

                }
                echo "cron error".$message;
            }
        }
    }
}
