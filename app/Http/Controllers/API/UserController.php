<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Models\Question; 
use App\Models\UserAnswer; 
use App\Models\Message; 
use App\Models\Group; 
use App\Models\Topic;
use App\Models\CategoryGroup;
use App\Models\AvailableTopics;
use App\Models\MentalHealth;
use App\Models\FeedbackType;
use App\Models\Resource;
use App\Models\Notification;
use App\Models\UserLoginLog;
use App\Models\Report;
use App\Models\Faq;
use App\Models\Location;
use App\Models\JustMentalPodcastCategory;
use App\Models\UserDisinterestedGroup;
use App\Models\UserDisinterestedType;
use App\Mail\PasswordResetOtp;
use App\Mail\Reports;
use App\Mail\Deactivate;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;
use Mail;


class UserController extends Controller 
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request)
    { 
        $credentials = [];
        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if( $fieldType == "email") {
            $credentials["email"] = $request->email;
        } else {
            $credentials["phone_number"] = $request->email;
        }

        $user = User::where( $credentials )->select('email', 'status')->first();
        if($user && $user->status == 1) {
            return response()->json([                
                "ReturnCode" => 0,
                "ReturnMessage" => "Your account is deactivated please contact support",
                "data" => []                
            ], 200); 
        }
		
		if($user && $user->status == 2) {
            return response()->json([                
                "ReturnCode" => 0,
                "ReturnMessage" => "Your account is deleted please contact support",
                "data" => []                
            ], 200); 
        }


        $credentials["password"] = $request->password;
        $credentials["status"] = 0;

        if(Auth::attempt($credentials)) {

            $user = Auth::user()->toArray(); 
            $token = auth()->user()->createToken('MyApp')->accessToken;
            $user["token"] = $token;
			
			$result = "";
			if(@$request->lat && @$request->long) {
				$result = $this->getAddress($request->lat, $request->long);
			}

        	User::where('id', $user['id'])
                        ->update([ "device_type" => $request->device_type, 'device_token' => $request->device_token, 'address' => $result ]);
   
            $user["device_type"] = $request->device_type;
            $user["device_token"] = $request->device_token;
            
            return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Logged in successfully",
                "data" => $user
            ], 200); 
        } 
        else{ 

            return response()->json([                
                "ReturnCode" => 0,
                "ReturnMessage" => "Invalid email or password",
                "data" => []                
            ], 200); 
        } 
    }
	
	function getAddress($latitude, $longitude)
	{
        //google map api url
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key='.env('GMAP_KEY');

        // send http request
        $geocode = file_get_contents($url);
        $json = json_decode($geocode);	

        $address = @$json->results[0]->formatted_address;

        return $address;
	}
	
	public function getNotificationSetting()
	{
		$selectedKeys = [];
		
		if(!empty(auth()->user()->notification_setting)){
			$selectedKeys = explode(',', auth()->user()->notification_setting);
		}
		
		$notificationSettings = auth()->user()->getNotificationSetting();
		
		if(in_array('all_notification', $selectedKeys)){
			
			foreach ($notificationSettings as $key => $data) {
				$notificationSettings[$key] = false;
			}
		
		}else{
			foreach ($selectedKeys as $key) {
				if (array_key_exists($key, $notificationSettings)) {
					$notificationSettings[$key] = false;
				}
			}
		}
		
		
		return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => 'Get Notification Setting',
            "data" => $notificationSettings
        ], 200); 
		
	}
	
	public function notificationSetting(Request $request)
	{
		$data = preg_replace("/\s*,\s*/", ",", $request->is_selected);
			
		if(!empty($data)){
			$dataArray = explode(',', $data);
			
			if(in_array('all_notification',$dataArray)){
				auth()->user()->update(['notification_setting'=> 'all_notification']);
			}else{
				auth()->user()->update(['notification_setting'=> $data]);
			}
			
		}else{
			auth()->user()->update(['notification_setting'=> null]);
		}
		
		return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => 'Notification Setting Updated',
            "data" => null
        ], 200); 
		
	}

    public function deactivate() 
    { 
		$reason = request()->reason;
		$status = request()->status;
		
        User::where('id', Auth::user()->id)->update([ "status" => $status, "reason"=>request()->reason]);    

        Mail::to(['vent@ventspaceapp.com', Auth::user()->email])->send(new Deactivate(Auth::user(), $reason));
		
		if($status){
			$message = "Account is deactivated successfully";
		}else{
			$message = "Account is deleted successfully";
		}
            
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => $message,
            "data" => null
        ], 200); 

    }
	
	
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            // 'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json([
                "ReturnCode" => 0,
                "ReturnMessage" => $validator->errors()->first(),
                "data" => null
            ], 200);         
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        $success['email'] =  $user->email;
        
        $this->syncMailchimp($user->email);

        return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Registered successfully",
                "data" => $success
            ], 200); 
    }

    public function sendchimp() 
    { 
		
        $users = User::where('email', 'not like', '%yopmail.com%')->orderBy('id', 'DESC')->get();
		
        foreach ($users as $key => $user) {
            $this->syncMailchimp($user->email);
        }  
            
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Send chimp mail",
            "data" => null
        ], 200); 

    }

    public function syncMailchimp($email) {

        $apiKey = '026d996264c3d271bcbfa1284910feba-us4';
        $listId =  '215b4fa962';

        $memberId = md5(strtolower($email));
		
        $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;
		

        $json = json_encode([
            'email_address' => $email,
            'status'        => "subscribed" // "subscribed","unsubscribed","cleaned","pending"
            // 'merge_fields'  => [
            //     'FNAME'     => $data['firstname'],
            //     'LNAME'     => $data['lastname'],
            //     'PHONE'     => $data['phone']
            // ]
        ]);
		

        try {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                    

            $result = curl_exec($ch);
			
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
			
            return $httpCode;
        } catch (Exception $e) {
            return null;
        }  

    }
	
	/**
     * Password reset Request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    
    public function resetPasswordUpdate(Request $request)
    {
		 $validator = Validator::make($request->all(), [ 
			 	//'current_password' => 'required',
				'password' => 'required', 
				// 'c_password' => 'required|same:password', 
			]);
		
		
		if ($validator->fails()) { 
			return response()->json([
				"ReturnCode" => 0,
				"ReturnMessage" => $validator->errors()->first(),
				"data" => null
			], 200);         
		}
		
        if($request->filled('password')) {
            
             DB::table('users')->where('id', $request->user_id)->update([ "remember_token" => null, 'password' => bcrypt($request->password) ]);

            return response()->json([
                    "ReturnCode" => 1,
                    "ReturnMessage" => "Password reset successfully.",
                    "data" => []
                ], 200);
        }

        return response()->json([                
            "ReturnCode" => 0,
            "ReturnMessage" => "Invalid Parameters.",
            "data" => []                
        ], 200); 

    }

    /**
     * Password update Request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    
    public function resetPassword(Request $request)
    {
		 $validator = Validator::make($request->all(), [ 
			 	'current_password' => 'required',
				'password' => 'required', 
				// 'c_password' => 'required|same:password', 
			]);
		
		if ($validator->fails()) { 
			return response()->json([
				"ReturnCode" => 0,
				"ReturnMessage" => $validator->errors()->first(),
				"data" => null
			], 200);         
		}
		
        if($request->filled('password')) {
            
			
           $user = DB::table('users')->where('id', $request->user_id)->first();
			
			//if (Hash::check($request->current_password, $user->password)) {
			if (!(Hash::check($request->current_password, $user->password))) {
				return response()->json([                
					"ReturnCode" => 0,
					"ReturnMessage" => "Your current password does not matches with the password you provided.",
					"data" => []                
				], 200); 
			}
			
			if(strcmp($request->get('current_password'), $request->get('password')) == 0){
				return response()->json([                
						"ReturnCode" => 0,
						"ReturnMessage" => "New Password cannot be same as your current password.",
						"data" => []                
					], 200); 
			}
			
             DB::table('users')->where('id', $request->user_id)->update([ "remember_token" => null, 'password' => bcrypt($request->password) ]);

            return response()->json([
                    "ReturnCode" => 1,
                    "ReturnMessage" => "Password changed successfully.",
                    "data" => []
                ], 200);
        }

        return response()->json([                
            "ReturnCode" => 0,
            "ReturnMessage" => "Invalid Parameters.",
            "data" => []                
        ], 200); 

    }
    
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "User Details.",
            "data" => $user
        ], 200);
    }


    /*
    * Update profile 
    */
    public function profileUpdate(Request $request) 
    {
        
        $input = $request->all();
        $id = Auth::user()->id;
        $userDetail=[];

        if($request->filled("phone_number")) {            
            if(Auth::user()->phone_number != $request->phone_number) {
                $user = DB::table('users')->where('phone_number', $request->phone_number)->count();
                if($user) {
                    return response()->json([
                        "ReturnCode" => 0,
                        "ReturnMessage" => "This phone number already exists.",
                        "data" => []
                    ], 200); 
                }
            }
        }

        if($request->filled("email")) {            
            if(Auth::user()->email != $request->email) {
                $user = DB::table('users')->where('email', $request->email)->count();
                if($user) {
                    return response()->json([
                        "ReturnCode" => 0,
                        "ReturnMessage" => "This email id already exists.",
                        "data" => []
                    ], 200); 
                }
            }
        }
        
        $image = "";
       
        if($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/profiles/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $image = $fileName;      
            }
        }

  
        $data = [ 
            'name' => $input['name'],
            'email' => $input['email'],
            'username' => @$input['username'],
            'phone_number' => $input['phone_number'],
            'birth_year' => $input['birth_year'],
            'state' => $input['state'],
            'profile_pic' => $image,
            'favorites_notification' => $input['favorites_notification'],
            'relates_notification' => $input['relates_notification'],
            'comments_notification' => $input['comments_notification'],
            'bookmark_notification' => $input['bookmark_notification'],
            'following_notification' => $input['following_notification']
        ];
            
        if($request->filled("password")) {            
           $data["password"] = bcrypt($request->password);
        }
        if($request->has("disinterested_groups")) {        
            UserDisinterestedGroup::where('user_id',auth()->user()->id)->delete();    
            if(!empty($request->disinterested_groups)){
            $disinterested_groups=explode(",",$request->disinterested_groups);
                foreach($disinterested_groups as $group){
                    UserDisinterestedGroup::create(['user_id'=>auth()->user()->id,'group'=>$group]);
                }
            }
        }
        if($request->has("disinterested_type")) {        
            UserDisinterestedType::where('user_id',auth()->user()->id)->delete(); 
            if(!empty($request->disinterested_type)){ 
              $disinterested_type=explode(",",$request->disinterested_type);
                foreach($disinterested_type as $type){
                    UserDisinterestedType::create(['user_id'=>auth()->user()->id,'type'=>$type]);
                }
            }
        }


        try{

            DB::table('users')->where('id', $id)
                        ->update($data);
            $userDetail=$data; 
            return response()->json([
                        "ReturnCode" => 1,
                        "ReturnMessage" => "Profile updated successfully.",
                        "data" => $userDetail
                    ], 200); 

        } catch(\Illuminate\Database\QueryException $e) {
            return response()->json([
                "ReturnCode" => 0,
                "ReturnMessage" => "Please try again and check payload.",
                "data" => []
            ], 200); 
        }   

    }


    /**
     * Returns Send OTP for password reset
     *
     * @return \Illuminate\Http\Response
     */
    public function resetOtp(Request $request)
    {

        if($request->filled('email')) {

            $user = User::where('email', $request->email)->first();
            if($user) {
				
				if($user->status) {
					return response()->json([                
						"ReturnCode" => 0,
						"ReturnMessage" => "Your account is deactivated please contact support",
						"data" => []                
					], 200); 
				}
				
				
                $otp = rand(100000, 999999);
                $data = [
                    "user_id" => $user->id,
                    "otp" => $otp
                ];

                // Need to check
                Mail::to($request->email)->send(new PasswordResetOtp($otp, $user));
                // 
                // $to = $request->email;
                // $subject = "Password Reset OTP";
                // $txt = "OTP: ". $otp;
                // $headers = "From: info@ventspaceapp.com" . "\r\n";

                // mail($to,$subject,$txt,$headers);

                DB::table('users')->where('id', $user->id)
                        ->update([ "remember_token" => $otp ]);
              
                return response()->json([
                    "ReturnCode" => 1,
                    "ReturnMessage" => "EMAIL sent successfully.",
                    "data" => $data
                ], 200);
            } else {
                return response()->json([                
                    "ReturnCode" => 0,
                    "ReturnMessage" => "User not found!",
                    "data" => []                
                ], 200);

            }          
        }

        return response()->json([                
            "ReturnCode" => 0,
            "ReturnMessage" => "Please enter email or phone",
            "data" => []                
        ], 200); 

    }


    /**
     * Returns list of Questions with current user answer 
     *
     * @return \Illuminate\Http\Response
     */    
    public function questions() {

    	
		$questions = Question::whereStatus(0)->select('id','question', 'options', 'is_multiselect')->get()->toArray(); 
    	$questions_all = [];
    	foreach ($questions as $question) {
    		$answer = UserAnswer::where(['user_id' => Auth::user()->id, 'question_id' => $question["id"] ])->select('answer')->get()->toArray();
			//dd($answer);
    		$ansArr = [];
    		foreach ($answer as $ans) {
    			array_push($ansArr, $ans["answer"]);
    		}
    		$question["answer"] = $ansArr;
    		array_push($questions_all, $question);
    	}

        $topics = Topic::whereStatus(0)->select('id','title')->get();

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
		
		
        $ava_topics = AvailableTopics::where(["type" => $type])->orderBy('indexed','asc')->get();
		
		
        $feedback_type = FeedbackType::get();
		
        $category_group = CategoryGroup::orderBy('indexed','asc')->get();
		
		
		$mental_health = MentalHealth::where("status","Live")->get();
        $categories = DB::table('categories')->get();
        $justmenal_categories = JustMentalPodcastCategory::orderBy('indexed','asc')->get();
      
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "List of all questions.",
            "data" => [
                "questions" => $questions_all,
                "topics" => $topics,
                "ava_topics" => $ava_topics,
                "feedback_type" => $feedback_type,
                "category_group" => $category_group,
				"mental_health"=>$mental_health,
                "categories"=>$categories,
                "justmenal_categories"=>$justmenal_categories
            ]
        ], 200);
    }

    public function faqs() {

        $faqs = Faq::whereStatus(0)->select('id','question', 'options')->get()->toArray(); 
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "List of all faqs.",
            "data" => [
                "faqs" => $faqs
            ]
        ], 200);
    }

    /**
     * Store user answers for Questions
     *
     * @return \Illuminate\Http\Response
     */
    public function submitQuestions(Request $request) {
		
        $input = $request->all();
		
		if(count($input)) {
			
			foreach ($input as $key => $value) {
				
				$ans = @explode(',', $value);
				$imploadedAnswer = implode(',', $ans);
				
				if(count($ans)){
					UserAnswer::where('question_id', $key)
						->where('user_id', Auth::user()->id)
						->delete();
					
					UserAnswer::insert(
						['question_id' => $key, 'user_id' => Auth::user()->id, 'answer' => $imploadedAnswer],
						['answer' => $imploadedAnswer]
					);
				}
			}
		}

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Answers saved.",
            "data" => $input
        ], 200);
    }

    /**
     * Store user messages
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request) {

        $input = $request->all();

       	$message = new Message($input);
       	$message->save();
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Message sent.",
            "data" => $message
        ], 200);
    }
	
	public function logUser(Request $request) {

        $input = $request->all();
		$input['start_time'] = date('Y-m-d H:i:s', strtotime($input['startTime']));
		$input['end_time'] = date('Y-m-d H:i:s', strtotime($input['endTime']));
		
		$input['tim_diff'] = @round(abs(strtotime($input['startTime']) - strtotime($input['endTime'])) / 60, 2);
		
       	$log = new UserLoginLog($input);
       	$log->save();
		
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "user time logged.",
            "data" => $log
        ], 200);
    }

    /**
     * Store user notification
     *
     * @return \Illuminate\Http\Response
     */
    public function sendNotification(Request $request) {

        $input = $request->all();
        $input['from_user'] = Auth::user()->id;
        $input['created_at'] = $request->date;
        $input['updated_at'] = $request->date;
        $notification = new Notification($input);
        $notification->save();
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Notification sent.",
            "data" => $notification
        ], 200);
    }

    /**
     * list user notification
     *
     * @return \Illuminate\Http\Response
     */
    public function listNotification() {

        $notifications = Notification::where(["to_user" => Auth::user()->id])
                            ->join('users', 'notifications.to_user', '=', 'users.id')
                            ->select('notifications.*', 'users.username as sent_to')
                            ->get();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Notification list.",
            "data" => $notifications
        ], 200);

    }

    /**
     * list resources
     *
     * @return \Illuminate\Http\Response
     */
    public function resources() {

        $resources = Resource::get();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Resources list.",
            "data" => $resources
        ], 200);
        
    }

    /**
     * Store user messages
     *
     * @return \Illuminate\Http\Response
     */
    public function addGroup(Request $request) {

        $input = $request->all();

        $group = new Group($input);
        $group->save();
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Group added.",
            "data" => $group
        ], 200);
    }

    public function blockUser(Request $request) {
        
        $user = DB::table('topics')->where('id', '=', $request->post_id)->select('user_id')->first();

        DB::table('blocked_users')->insert(
            ['user_id' => auth()->user()->id, 'to_user' => @$user->user_id]
        );

         return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "User Blocked",
        ], 200);
    }

    /**
     * Store user messages
     *
     * @return \Illuminate\Http\Response
     */
    public function reportSubmit(Request $request) {
		 $validator = Validator::make($request->all(), [ 
			 	'subject' => 'required',
				'detail' => 'required', 
			]);
		
		if ($validator->fails()) { 
			return response()->json([
				"ReturnCode" => 0,
				"ReturnMessage" => $validator->errors()->first(),
				"data" => null
			], 200);         
		}

        $input = $request->all();
		
        if($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/reports/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $input['image'] = $fileName;      
            }
        }
		
		if (!$request->filled('group_id')) {
			$input['group_id'] = null;
		}
		
		if (!$request->filled('post_id')) {
			$input['post_id'] = null;
		}
		
        $report = new Report($input);
        $report->save();
		
      Mail::to(['vent@ventspaceapp.com'])->send(new Reports($report, Auth::user()));
      //Mail::to(['itxaarchiarora@gmail.com'])->send(new Reports($report, Auth::user()));

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Report submitted.",
            "data" => $report
        ], 200);
    }

    /**
     * get user messages
     *
     * @return \Illuminate\Http\Response
     */
    public function messages() {

        $messages = DB::table('messages')
                ->whereRaw('id IN ( SELECT MAX(id) FROM messages GROUP BY group_id)')
                ->orderBy('id', 'DESC')
                ->get();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Message list.",
            "data" => $messages
        ], 200);
    }
    /**
     * get user messages
     *
     * @return \Illuminate\Http\Response
     */
    public function message($id) {

       	$message = Message::find($id)->first();
       	
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Message",
            "data" => $message
        ], 200);
    }

    /**
     * get locations
     *
     * @return \Illuminate\Http\Response
     */
    public function locations() {

        $message = Location::orderBy('name', 'asc')->get();
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Locations",
            "data" => $message
        ], 200);
    }
	
	public function app_settings()
    {
		$data = AppSetting::first(['ios_version','android_version','is_update','is_force_update']);
		return response()->json([
			"ReturnCode" => 1,
			"ReturnMessage" => "App Settings",
			"data" => $data
         ], 200);
    }
}