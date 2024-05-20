<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Models\Journal;
use App\Models\JournalCount;
use App\Models\JournalComments;
use App\Models\JournalCommentsReply;
use App\Models\MentalHealth;
use App\Models\JustMentalPodcast;
use App\Models\Topic;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Support\Facades\Log;
use App\Classes\Jwt;
use DB;

class JournalController extends Controller 
{

    /** 
     * Store Journals  
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function submit(Request $request)
    { 
        $validator = Validator::make($request->all(), [ 
            'is_public' => 'required|in:yes,no', 
            'date' => 'required', 
            'title' => 'required', 
            'details' => 'required', 
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
            $path = public_path('uploads/journal_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $input['image'] = $fileName;      
            }
        }

        
        $input['user_id'] = Auth::user()->id;

        if(Auth::user()->birth_year) {
            $year = date("Y");
            $diff = $year - Auth::user()->birth_year;
            $type = ($diff < 18) ? "teens" : "adults"; 
            $input['type'] = $type;
        }
        
        $date = $input['date'];
        $input['date'] = date('Y-m-d', strtotime($date));
        if($request->filled("id")) {
            $input['updated_at'] = $date;
            $journal = Journal::find($input["id"])->update($input);
        } else {    
            $input['created_at'] = $date;
            $journal = new Journal($input);
            $journal->save();
        }

        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Journal saved.",
            "data" => $journal
        ], 200);
        

    }
    
    public function journals() {

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 

        $query = Journal::where('status', '=', 0)->where(["type" => $type]); 

        $data = [];
        $journals = $query->get()->toArray(); 
        if(count($journals)) {
            foreach ($journals as $journal) {
                $journal["following_count"] = JournalCount::where(["type" => "following", "journal_id" => $journal['id']])->count();
                $journal["related_count"] = JournalCount::where(["type" => "related", "journal_id" => $journal['id']])->count();
                $journal["comments_count"] = JournalComments::where([ "journal_id" => $journal['id']])->count();
                array_push($data, $journal);
            }
        }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "All journals.",
            "data" => $data
        ], 200);
    }



    public function submitQuestions(Request $request) {

        $input = $request->all();
		
		
        if(count($input)) {
            foreach ($input as $key => $value) {
                UserAnswer::updateOrInsert(
                    ['question_id' => $key, 'user_id' => Auth::user()->id],
                    ['answer' => $value]
                );
            }
        }
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Answers saved.",
            "data" => $input
        ], 200);
    }

    public function addCount(Request $request)
	{
	
        $input = $request->all();
		
    	if($input["type"] == "unrelated" || $input["type"] == "unfollowing") {
    		$type = ($input["type"] == "unrelated") ? "related" : "following";
            $qry = ["journal_id" => $input["journal_id"], "type" => $type];

            if($request->filled("topic_id")) {  
                $qry = ["topic_id" => $input["topic_id"], "type" => $type];
            } 

            if($request->filled("health_id")) {  
                $qry = ["health_id" => $input["health_id"], "type" => $type];
            } 
            
            if($request->filled("podcast_id")) {  
                $qry = ["podcast_id" => $input["podcast_id"], "type" => $type];
            } 

    		JournalCount::where($qry)->delete();
    		return response()->json([
            	"ReturnCode" => 1,
	            "ReturnMessage" => "Count removed.",
	            "data" => null
	        ], 200);
    	}

        $input['user_id'] = Auth::user()->id;
        $input['created_at'] = $input['date'];
        $journal = new JournalCount($input);
        $journal->save();

        // send push notification
        // if($request->filled("journal_id")) {    

        //     $get = Journal::join('users', 'journals.user_id', '=', 'users.id')
        //                     ->where(["journals.id" => $input['journal_id']])
        //                     ->whereNotNull('users.device_token')
        //                     ->select('journals.title', 'users.device_type', 'users.id', 'users.device_token', 'users.username')
        //                     ->first();

        //     if($get) {
        //         $msg = [
        //             "title" => "Ventspace",
        //             "text" => "Your journal ".$get->title." is ". $input['type']." by #". Auth::user()->username
        //         ];
        //         // Sending a request to APNS
        //         // $stat = Jwt::sendPushNotification($msg, $get->device_token);
        //         // if($get->device_type == 'Android') {
        //             $this->sendPush($get->device_token, $get->device_type, $msg['text'], @$input['type'], @$request->filled("journal_id"));
        //         // }

        //         $resource = new Notification;
        //         $resource->title = "Ventspace";
        //         $resource->message = "Your journal ".$get->title." is ". $input['type']." by #". Auth::user()->username;
        //         $resource->from_user = Auth::user()->id;
        //         $resource->to_user = $get->id;
        //         $resource->created_at = $input['date'];
        //         $resource->save();
        //     }

        // } elseif($request->filled("topic_id")) {
            
             $get = Topic::with('user')
					 ->join('users', 'topics.user_id', '=', 'users.id')
					 ->where(["topics.id" => $input['topic_id']])
					 ->whereNotNull('users.device_token')
					 ->select('topics.title', 'topics.post_type', 'users.device_type', 'users.device_token', 'users.id', 'users.username')
					 ->first();

            if($get) {
				
				$user = User::find($get->id);
				
				if($get->post_type == 'story'){
					$typeCheck = 'story';
				}else{
					$typeCheck = 'feed';
				}
				
                $msg = [
                    "title" => "Ventspace",
                    "text" => Auth::user()->username." has ".( $input['type'] == 'following' ? 'followed' : $input['type'] )." your topic, ".$get->title
                ];
				
				
				if(!in_array('all_notification', $user->notification_check)){
					if(!in_array($typeCheck, $user->notification_check)){
						$this->sendPush($get->device_token, $get->device_type, $msg['text'], @$input['type'], $input['topic_id']);
						$resource = new Notification;
						$resource->title = "Ventspace";
						$resource->message = "Your topic ".$get->title." is ".($input['type'] == 'following' ? 'followed' : $input['type'] )." by #". Auth::user()->username;
						$resource->from_user = Auth::user()->id;
						$resource->to_user = $get->id;
						$resource->type = strtoupper($input['type']);
						$resource->pid = $input['topic_id'];
						$resource->created_at = $input['date'];
						$resource->save();
					}
				}
				
				
					
                
            }
        // } else {
        //     // TODO
        // }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Count saved.",
            "data" => $journal
        ], 200);
    }

    public function submitComment(Request $request) {

        $input = $request->all();
		

        $input['user_id'] = Auth::user()->id;
        $input['created_at'] = $input['date'];
        $journal = new JournalComments($input);
        $journal->save();
        $journal['comment_by'] = Auth::user()->username;

        // send push notification
        // if($request->filled("journal_id")) {    

        //     $get = Journal::join('users', 'journals.user_id', '=', 'users.id')
        //                     ->where(["journals.id" => $input['journal_id']])
        //                     ->whereNotNull('users.device_token')
        //                     ->select('journals.title', 'users.device_type', 'users.device_token', 'users.id', 'users.username')
        //                     ->first();

        //     if($get) {
        //         $msg = [
        //             "title" => "Ventspace",
        //             "text" => "Your journal ".$get->title." is commented by #". Auth::user()->username
        //         ];
        //         // Sending a request to APNS
        //         // $stat = Jwt::sendPushNotification($msg, $get->device_token);
        //         // if($get->device_type == 'Android') {
        //         // }
        //         if($get->id != Auth::user()->id) {
        //             $this->sendPush($get->device_token, $get->device_type, $msg['text'], "comment", @$request->filled("journal_id"));
        //             $resource = new Notification;
        //             $resource->title = "Ventspace";
        //             $resource->message = "Your journal ".$get->title." is commented by #". Auth::user()->username;
        //             $resource->from_user = Auth::user()->id;
        //             $resource->to_user = $get->id;
        //             $resource->created_at = $input['date'];
        //             $resource->save();
        //         }
        //     }
            
        // } elseif ($request->filled("topic_id")) {
       
            $get = Topic::join('users', 'topics.user_id', '=', 'users.id')
                            ->where(["topics.id" => $input['topic_id']])
                            ->whereNotNull('users.device_token')
                            ->select('topics.title', 'topics.post_type', 'topics.user_id', 'users.device_type', 'users.device_token', 'users.id', 'users.username')
                            ->first();
          
            if($get) {
				
                $msg = [
                    "title" => "Ventspace",
                    "text" => Auth::user()->username." has commented on your topic, ".$get->title
                ];
				
				$user = User::find($get->id);
				
				if($get->post_type == 'story'){
					$typeCheck = 'story';
				}else{
					$typeCheck = 'feed';
				}
				
                // Sending a request to APNS
                // $stat = Jwt::sendPushNotification($msg, $get->device_token);
                // if($get->device_type == 'Android') {
                if($get->id != Auth::user()->id) {
					
					if(!in_array('all_notification', $user->notification_check)){
						//if(!in_array($typeCheck, $user->notification_check)){
							
							if(!in_array('user_reply', $user->notification_check)){
								$rep = $this->sendPush($get->device_token, $get->device_type, $msg['text'], "comment", @$input['topic_id']);
								$resource = new Notification;
								$resource->title = "Ventspace";
								$resource->message = "Your topic ".$get->title." is commented by #". Auth::user()->username;
								$resource->from_user = Auth::user()->id;
								$resource->to_user = $get->id;
								$resource->type = "COMMENT";
								$resource->pid = $input['topic_id'];
								$resource->created_at = $input['date'];
								$resource->save();

							}
								 
						
						//}
					}
                   
                }
                // }
            }
        // } else {
        //     // TODO
        // }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Comment saved.",
            "data" => $journal
        ], 200);

    }

    public function sendJournalPush() {

    } 

    public function commentReply(Request $request) {

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['created_at'] = $input['date'];
        $journal = new JournalCommentsReply($input);
        $journal->save();
        $journal['reply_by'] = Auth::user()->username;


        $get = JournalComments::join('users', 'journal_comments.user_id', '=', 'users.id')
                            ->where(["journal_comments.id" => $input['comment_id']])
                            ->whereNotNull('users.device_token')
                            ->select('journal_comments.comment', 'users.device_type', 'users.device_token', 'users.id', 'users.username')
                            ->first();

        if($get) {
            $msg = [
                "title" => "Ventspace",
                "text" => "You got reply on comment by #". Auth::user()->username
            ];
            // Sending a request to APNS
            // $stat = Jwt::sendPushNotification($msg, $get->device_token);
            // if($get->device_type == 'Android') {
            // }
            if($get->id != Auth::user()->id) {
                
                $this->sendPush($get->device_token, $get->device_type, $msg['text'], "comment_reply", $input['comment_id'] );

                $resource = new Notification;
                $resource->title = "Ventspace";
                $resource->message = "You got reply on comment by #". Auth::user()->username;
                $resource->from_user = Auth::user()->id;
                $resource->to_user = $get->id;
                $resource->type = "COMMENT_REPLY";
                $resource->pid = $input['comment_id'];
                $resource->created_at = $input['date'];
                $resource->save();
            }
        }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Comment saved.",
            "data" => $journal
        ], 200);

    }

    public function commentDelete(Request $request) 
    {

        $input = $request->all();
        JournalComments::where(['topic_id' => $input['topic_id'], 'id' => $input['id'] ])->delete();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Comment deleted."
        ], 200);
    }
    public function podcastcommentDelete(Request $request) 
    {

        $input = $request->all();
        JournalComments::where('id',$input['id'] )->delete();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Comment deleted."
        ], 200);
    }

    public function commentEdit(Request $request) 
    {

        $input = $request->all();
        JournalComments::where(['id' => $input['id'] ])->update([
            'comment' => $input['comment'],
			'created_at' => $input['date']
        ]);

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Comment updated."
        ], 200);
    }

    public function replyDelete(Request $request) 
    {

        $input = $request->all();        
        JournalCommentsReply::where(['comment_id' => $input['comment_id'], 'id' => $input['id'] ])->delete();

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Reply deleted.",
        ], 200);

    }

    public function replyEdit(Request $request) 
    {

        $input = $request->all();        
        JournalCommentsReply::where([ 'id' => $input['id'] ])->update([
            'comment' => $input['comment'],
			'created_at' => $input['date']
        ]);

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Reply updated.",
        ], 200);

    }

    public function commentList(Request $request) {

        $qArr = ['journal_id' => $request->journal_id];
        if($request->filled("topic_id")) { 
            $qArr = ['topic_id' => $request->topic_id];
        }
        if($request->filled("health_id")) { 
            $qArr = ['health_id' => $request->health_id];
        } 
        if($request->filled("podcast_id")) { 
            $qArr = ['podcast_id' => $request->podcast_id];
        } 

        $query = JournalComments::where($qArr)
                    ->join('users', 'journal_comments.user_id', '=', 'users.id')
                    ->select('journal_comments.*', 'users.username as comment_by'); 

        $data = [];
        $comments = $query->get()->toArray(); 
        if(count($comments)) {
            foreach ($comments as $comment) {
                $comment["replies_count"] = JournalCommentsReply::where(["comment_id" => $comment['id']])->count();
                array_push($data, $comment);
            }
        }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "All comments.",
            "data" => $data
        ], 200);
        
    }

    public function repliesList(Request $request) {


        $comment = JournalComments::where(['journal_comments.id' => $request->comment_id])
                    ->join('users', 'journal_comments.user_id', '=', 'users.id')
                    ->select('journal_comments.*', 'users.username as comment_by')->first(); 

        $query = JournalCommentsReply::where(['comment_id' => $request->comment_id])
                    ->join('users', 'journal_comments_reply.user_id', '=', 'users.id')
                    ->select('journal_comments_reply.*', 'users.username as reply_by'); 

        $replies = $query->get()->toArray();


        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "All replies.",
            "comment" => $comment,
            "data" => $replies
        ], 200);
    }

    public function browse(Request $request) {

        $query = Journal::whereStatus(0);


        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 

        // $query->where('user_id', '<>', Auth::user()->id);
        $query->where('type', '=', $type);

        if($request->filled('filter_journal')) {
            $title = $request->filter_journal;
            // $query->where('title', 'LIKE', "%{$title}%");

            $query->where(function($qr) use ($title) {
                $qr->where('title', 'LIKE', "%{$title}%")
                    ->orWhere('topic', 'LIKE', "%{$title}%");
            }); 
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('created_at', '=', $filter_date);
        }

        $data = [];
        $journals = $query->get()->toArray(); 
        if(count($journals)) {
            foreach ($journals as $journal) {
                $journal["isFollow"] = JournalCount::where(["type" => "following", "journal_id" => $journal['id']])->count();
                $journal["isRelated"] = JournalCount::where(["type" => "related", "journal_id" => $journal['id']])->count();
                $journal["comments_count"] = JournalComments::where(["journal_id" => $journal['id']])->count();
                array_push($data, $journal);
            }
        }
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of journal.",
            "data" => $data
        ], 200);
    }

    public function delete(Request $request) {

        $input = $request->all();
        $comments = [];
        if($request->filled("journal_id")) {
            $comments = JournalComments::where(['user_id' => Auth::user()->id, "journal_id" => $input['journal_id']])->get();
            Journal::where("id", $input['journal_id'])->delete();
        } else {
            $comments = JournalComments::where(['user_id' => Auth::user()->id, "topic_id" => $input['topic_id']])->get();
            Topic::where("id", $input['topic_id'])->delete();
        }
        if(count($comments)) {
            foreach ($comments as $comment) {
                JournalCommentsReply::where("comment_id", $comment->id)->delete();
                JournalComments::where("id", $comment->id)->delete();
            }
        }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "deleted.",
            "data" => null
        ], 200);
    }
	
	public function count(Request $request)
    {
        
        $partner = MentalHealth::find($request->id);
        
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
            "ReturnMessage" => "Mental Health count updated.",
            "data" => $partner
        ], 200);
    }

	public function justMentalCount(Request $request)
    {
        
        $partner =JustMentalPodcast::where('id',$request->id)->first();
        
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
            "ReturnMessage" => "Just Mental Health count updated.",
            "data" => $partner
        ], 200);
    }


    public function test() {
        $msg = [
            "title" => "Welcome to Ventspace",
            "text" => "Ventspace"
        ];
        // Sending a request to APNS
        $stat = Jwt::sendPushNotification($msg, "6da3fffb4db752cb6eaa86da13b1578d6b4461673bf0b3d474a0b84a8f96aa31");
        var_dump($stat);       

    }
	
	 public function ptest() {
        $msg = [
            "title" => "Ventspace",
            "text" => "Welcome to Ventspace"
        ];
		$user = User::find(4829);
		print_r($user->device_token);
        // Sending a request to APNS
        $stat = $this->sendPush($user->device_token, $user->device_type, $msg['text'], "comment", 1);
        print_r($stat);       
    }
	
	 

    public function sendPush($deviceToken, $deviceType, $message, $type = "", $id = 0) {

        try {

            Log::channel('pushnotifications')->info($id.'>>>>'.$type.'>>>>'.$message);
            // $API_ACCESS_KEY = 'AIzaSyASUxi2SS8SNzBk_Yu68BUhI1w0jZcRLdA';
            $API_ACCESS_KEY = env('FCM_KEY');

            $registrationIds = $deviceToken;
            #prep the bundle
            $msg = array
            (
                'body'  => $message,
                'title' => 'Ventspace',
               // 'icon'  => 'myicon',
              //  'sound' => 'mySound',
				'id' => $id,
             	'type' => $type
            );

            $fields = array
            (
                'to' => $registrationIds,
                'notification' => $msg,
				'data' => $msg,
            );

            /*if(strtolower($deviceType) == "android"){
                $fields = array(
                    'to' => $registrationIds,
                    'data' => $msg
                );
            } else {
                $fields = array(
                    'to' => $registrationIds,
                    'notification' => $msg
                );
            }*/
			//print_r($fields);

            $headers = array
            (
                'Authorization: key='.$API_ACCESS_KEY,
                'Content-Type: application/json'
            );
            #Send Reponse To FireBase Server  
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result_log = curl_exec($ch);
            if ($result_log === FALSE) {
                return curl_error($ch);
            }
            curl_close($ch);
            Log::channel('pushnotifications')->info($result_log);
            return $result_log;
            
        }
        //catch exception
        catch(Exception $e) {
            return null;
        }
        
    }
}