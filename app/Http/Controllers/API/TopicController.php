<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Models\Topic;
use App\Models\Support;
use App\Models\Topicnew;
use App\Models\BlockedTopic;
use App\Models\JournalComments;
use App\Models\MentalHealth;
use App\Models\CategoryGroup;
use App\Models\PeerGroup;
use App\Models\JournalCount;
use App\Models\UserDisinterestedGroup;
use App\Models\UserDisinterestedType;
use App\Models\AvailableTopics;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;
use Validator;
use DB;
use App\Models\Advertisement;
use Carbon\Carbon;


class TopicController extends Controller 
{


     /** 
     * return list of all topics
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function addTopic(Request $request) 
    {
        try{

            $validator = Validator::make($request->all(), [ 
                'date' => 'required', 
                'title' => 'required', 
                'details' => 'required'
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
                $path = public_path('uploads/topic_files/');
                if(@$file) {
                    $fileType = $file->getMimeType();
                    $fileName = $file->getClientOriginalName();
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileName = time().'.'. $fileExtension;
                    $file->move($path, $fileName); 
                    $input['image'] = $fileName;      
                }
            }

            // if($request->has('image')) {
            //     $file = $request->file('image');
            //     $path = public_path('uploads/topic_files/');
            //     if(@$file) {
            //         $fileType = $file->getMimeType();
            //         $fileName = $file->getClientOriginalName();
            //         $fileExtension = $file->getClientOriginalExtension();
            //         $fileName = time().'.'. $fileExtension;
            //         $file->move($path, $fileName); 
            //         $input['image'] = $fileName;      
            //     }
            // }

            if(Auth::user()->birth_year) {
                $year = date("Y");
                $diff = $year - Auth::user()->birth_year;
                $type = ($diff < 18) ? "teens" : "adults"; 
                $input['type'] = $type;
            }

            $input['user_id'] = Auth::user()->id;
            $date = $input['date'];
            if($request->filled("id")) {
                $input['updated_at'] = $date;
                $topic = Topic::find($input["id"])->update($input);
            } else {    
                $input['created_at'] = $date;
                $topic = new Topic($input);
                $topic->save();
            }


            // $topic->image = $images;

            return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Topic saved.",
                "data" => $topic
            ], 200);

        } catch(\Exception $e) {
          return response()->json([
                "ReturnCode" => 0,
                "ReturnMessage" => $e->getMessage(),
                "data" => null
            ], 200);
        }
        
    }

    /** 
     * return list of all topics
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function topics(Request $request) {
        $disinterested_groups=UserDisinterestedGroup::where('user_id',auth()->user()->id)->pluck('group');
        $disinterested_types=UserDisinterestedType::where('user_id',auth()->user()->id)->pluck('type');

        //$blocked = DB::table('blocked_users')->where('user_id', '=', auth()->user()->id)->select('to_user')->pluck('to_user')->toArray();

        $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                    ->where(['topics.status' => 0, 'users.status' => 0])
                    //->whereNotIn('topics.user_id', $blocked )
                    ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 

        if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                    ->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                    ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                    ->select('topics.*', 'users.username as user_name', 'users.user_type as user_type','journal_counts.type as jtype')
                    ->groupBy("topics.id"); 
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if(isset($request->timezone) && $request->timezone !=null){
				$localTime= Carbon::now($request->timezone);
				//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
				 $query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
			}else{
               $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
			}
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
            $query->where('topics.user_id', '=', Auth::user()->id);
        }

        if($request->filled('category_group')) {
            $mul = explode(',', $request->category_group);
            // $query->where('topics.category_group', '=', $request->category_group);
            $query->where(function($qr) use ($mul) {
                foreach ($mul as $title) {
                    $title  = trim($title);
                    // $title = str_replace(', ', ',', $title);
                    $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
                }
            }); 
        }
        if(isset($disinterested_groups[0])){
            $query->where(function($qr) use ($disinterested_groups) {
                foreach ($disinterested_groups as $group) {
                    $group  = trim($group);
                    // $title = str_replace(', ', ',', $title);
                    $qr->Where('topics.category_group', 'NOT LIKE', "%{$group}%");
                }
            }); 
        }
        if(isset($disinterested_types[0])){
            $query->where(function($qr) use ($disinterested_types) {
                foreach ($disinterested_types as $type) {
                    $type  = trim($type);
                    // $title = str_replace(', ', ',', $title);
                    $qr->Where('topics.available_topic', 'NOT LIKE', "%{$type}%");
                }
            }); 
        }
        if($request->filled('filter_topic')) {           
            $mul = explode(',', $request->filter_topic);
            if(count($mul) > 1) {
                $query->where(function($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $title  = trim($title);
                        $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    }
                }); 
            } else {
                $title = @$mul[0];
                $query->where(function($qr) use ($title) {
                    $title  = trim($title);
                    $qr->where('topics.title', 'LIKE', "%{$title}%")
                        ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                }); 
            }
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('topics.created_at', '=', $filter_date);
        }

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
 
        $query->where('topics.type', '=', $type);

        $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
        // $topics = $query->get()->toArray(); 
        $topics = $query->orderBy('created_at','DESC')->paginate(120)->toArray(); 
     
        $data = [];

        if(@$topics['data'] && count($topics['data'])) {           

            foreach ($topics['data'] as $topic) {     

                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                // if($topic['created_at'] != "0000-00-00 00:00:00") {
                array_push($data, $topic);
                // }
                // array_push($data, $topic);           
            }
        }
       
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of topics.",
            "total" => @$topics['total'],
            "last_page" => @$topics['last_page'],
            "data" => $data
        ], 200);
    }
    
    /** 
     * return list of all topics
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function addTopicNew(Request $request) 
    {
        try{

            $validator = Validator::make($request->all(), [ 
                'date' => 'required', 
                'title' => 'required', 
                'details' => 'required'
            ]);

            if ($validator->fails()) { 
                return response()->json([
                    "ReturnCode" => 0,
                    "ReturnMessage" => $validator->errors()->first(),
                    "data" => null
                ], 200);         
            }

            $input = $request->all();
			
			//$peerGroup = $request->input('peer_group');

			//$peerGroupsArray = [];
			//if ($peerGroup) {
				//$peerGroupsArray = explode(',', $peerGroup);
			//	$peerGroupsArray = array_map('trim', $peerGroupsArray);
			//}
			
			//if($request->input('post_type') == 'story'){
			//	$postType = 'story';
			//}else{
			//	$postType = 'post';
			//}
			

            // if (strpos($input['image'], "http") !== 0) {
                if($request->has('image')) {
                    $file = @$request->file('image');
                    if($file) {
                        $path = Storage::disk('s3')->put('topics', $file);
                        $input['image'] = $path;  
                    }
                }
            // }

            if(Auth::user()->birth_year) {
                $year = date("Y");
                $diff = $year - Auth::user()->birth_year;
                $type = ($diff < 18) ? "teens" : "adults"; 
                $input['type'] = $type;
            }

            $input['user_id'] = Auth::user()->id;
            $date = $input['date'];
            if($request->filled("id")) {
				$input['updated_at'] = $date;
				$topic = Topic::find($input["id"]);
				$topic->update($input);
				//$topic->groups()->sync($peerGroupsArray, ['type' => $postType, 'status' => 1]);
				
            } else {    
                $input['created_at'] = $date;
                $topic = new Topic($input);
                $topic->save();
				//$topic->groups()->attach($peerGroupsArray, ['type' => $postType, 'status' => 1]);
            }


            // $topic->image = $images;

            return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Topic saved.",
                "data" => $input
            ], 200);

        } catch(\Exception $e) {
          return response()->json([
                "ReturnCode" => 0,
                "ReturnMessage" => $e->getMessage(),
                "data" => null
            ], 200);
        }
        
    }

    /** 
     * return list of all topics
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function topicsNew(Request $request) {
		
        $disinterested_groups=UserDisinterestedGroup::where('user_id',auth()->user()->id)->pluck('group');
        $disinterested_types=UserDisinterestedType::where('user_id',auth()->user()->id)->pluck('type');
        $user_id=auth()->user()->id;
		
		//$peerGroup = PeerGroup::withCount(['users', 'topics' => function ($query) use ($request) {
			//$query->where('post_type', '!=', 'story');
			//$query->where('peer_group_id', $request->filter_peer_group);
		//}])->find($request->filter_peer_group);

	
       // $blocked = DB::table('blocked_users')->where('user_id', '=', auth()->user()->id)->select('to_user')->pluck('to_user')->toArray();
		  $blockedTopics = BlockedTopic::where('user_id', '=', auth()->user()->id)->select('topic_id')->pluck('topic_id')->toArray();
		//echo(Carbon::now('UTC')->toDateString());

        $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                    ->where(['topics.status' => 0, 'users.status' => 0])
                    //->whereNotIn('topics.user_id', $blocked )
			        ->whereNotIn('topics.id', $blockedTopics )
                    ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 
//dd(date("Y-m-d H:i:s"));
        if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
            		->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                    ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                    ->select('topics.*', 'users.username as user_name','users.user_type as user_type', 'journal_counts.type as jtype')
                    ->groupBy("topics.id"); 
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if(isset($request->timezone) && $request->timezone !=null){
				$localTime= Carbon::now($request->timezone);
				//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
				 $query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
			}else{
               $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
			}
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
            $query->where('topics.user_id', '=', Auth::user()->id);
        }
		
		//Add new code filter peer group data 
		//if ($request->filled('filter_peer_group')) {
			//$query->whereHas('groups', function ($q) use ($request) {
			//	$q->where('peer_group_id', $request->filter_peer_group);
			//});
		//}

        if($request->filled('category_group')) {
            $mul = explode(',', $request->category_group);
            // $query->where('topics.category_group', '=', $request->category_group);
            $query->where(function($qr) use ($mul) {
                foreach ($mul as $title) {
                    $title = trim($title);
                    // $title = str_replace(', ', ',', $title);
                    $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
                }
            }); 
        }
        if(isset($disinterested_groups[0])){
            $query->where(function($qr) use ($disinterested_groups,$user_id) {
                foreach ($disinterested_groups as $group) {
                    $group  = trim($group);
                    // $title = str_replace(', ', ',', $title);
                    $qr->where(function($subQuery) use ($group, $user_id) {
                        $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
                                 ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            }); 
        }
        if(isset($disinterested_types[0])) {
            $query->where(function($qr) use ($disinterested_types, $user_id) {
                foreach ($disinterested_types as $type) {
                    $type  = trim($type);
                    $qr->where(function($subQuery) use ($type, $user_id) {
                        $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                                 ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            });
        }

        if($request->filled('filter_topic')) {           
            $mul = explode(',', $request->filter_topic);
            if(count($mul) > 1) {
                $query->where(function($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $title = trim($title);
                        $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    }
                }); 
            } else {
                $title = @$mul[0];
                $query->where(function($qr) use ($title) {
                    $qr->where('topics.title', 'LIKE', "%{$title}%")
                        ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                }); 
            }
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('topics.created_at', '=', $filter_date);
        }

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
	
        $query->where('topics.type', '=', $type);
        $query->where('topics.post_type', '!=', 'story');


        $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
        // $topics = $query->get()->toArray(); 
        $topics = $query->orderBy('created_at','DESC')->paginate(20)->toArray(); 
     
        $data = [];

        if(@$topics['data'] && count($topics['data'])) {           

            foreach ($topics['data'] as $topic) {     

                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                // if($topic['created_at'] != "0000-00-00 00:00:00") {
                array_push($data, $topic);
                // }
                // array_push($data, $topic);           
            }
        }
		
		//$topics = AvailableTopics::where(['type'=>'adults'])->orderBy('indexed', 'ASC')->get();
		
		//dd($topics);
       
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of topics.",
            "total" => @$topics['total'],
            "last_page" => @$topics['last_page'],
			//"peer_group" => $peerGroup,
            "data" => $data,
        ], 200);
    }

    public function stories(Request $request) {
        $disinterested_groups=UserDisinterestedGroup::where('user_id',auth()->user()->id)->pluck('group');
        $disinterested_types=UserDisinterestedType::where('user_id',auth()->user()->id)->pluck('type');
        $user_id=auth()->user()->id;
	
      //  $blocked = DB::table('blocked_users')->where('user_id', '=', auth()->user()->id)->select('to_user')->pluck('to_user')->toArray();

        $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                    ->where(['topics.status' => 0, 'users.status' => 0])
                   // ->whereNotIn('topics.user_id', $blocked )
                    ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 

        // if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

        //     $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
        //     		->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
        //             ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
        //             ->select('topics.*', 'users.username as user_name','users.user_type as user_type', 'journal_counts.type as jtype')
        //             ->groupBy("topics.id"); 
        // }

        // if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
        //     $query->whereDate('topics.created_at', '=', date('Y-m-d'));
        // }

        // if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
        //     $query->where('topics.user_id', '=', Auth::user()->id);
        // }

        // if($request->filled('category_group')) {
        //     $mul = explode(',', $request->category_group);
        //     // $query->where('topics.category_group', '=', $request->category_group);
        //     $query->where(function($qr) use ($mul) {
        //         foreach ($mul as $title) {
        //             $title = trim($title);
        //             // $title = str_replace(', ', ',', $title);
        //             $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
        //         }
        //     }); 
        // }
        if(isset($disinterested_groups[0])){
            $query->where(function($qr) use ($disinterested_groups,$user_id) {
                foreach ($disinterested_groups as $group) {
                    $group  = trim($group);
                    // $title = str_replace(', ', ',', $title);
                    $qr->where(function($subQuery) use ($group, $user_id) {
                        $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
                                 ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            }); 
        }
        if(isset($disinterested_types[0])) {
            $query->where(function($qr) use ($disinterested_types, $user_id) {
                foreach ($disinterested_types as $type) {
                    $type  = trim($type);
                    $qr->where(function($subQuery) use ($type, $user_id) {
                        $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                                 ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            });
        }

        // if($request->filled('filter_topic')) {           
        //     $mul = explode(',', $request->filter_topic);
        //     if(count($mul) > 1) {
        //         $query->where(function($qr) use ($mul) {
        //             foreach ($mul as $title) {
        //                 $title = trim($title);
        //                 $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
        //             }
        //         }); 
        //     } else {
        //         $title = @$mul[0];
        //         $query->where(function($qr) use ($title) {
        //             $qr->where('topics.title', 'LIKE', "%{$title}%")
        //                 ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
        //         }); 
        //     }
        // }

        // if($request->filled('filter_date')) {
        //     $filter_date = $request->filter_date;
        //     $query->whereDate('topics.created_at', '=', $filter_date);
        // }

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
	
        $query->where('topics.type', '=', $type);
        $query->where('topics.post_type', '=', 'story');


        $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
        // $topics = $query->get()->toArray(); 
        $topics = $query->orderBy('created_at','DESC')->paginate(20)->toArray(); 
     
        $data = [];

        if(@$topics['data'] && count($topics['data'])) {           

            foreach ($topics['data'] as $topic) {     

                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                // if($topic['created_at'] != "0000-00-00 00:00:00") {
                array_push($data, $topic);
                // }
                // array_push($data, $topic);           
            }
        }

        $ads = Advertisement::where('status', '1')->get();
        $diff=0;
        if(count($ads) >0){
           $diff = count($data) / count($ads);
        }
        $diff = round($diff);
        
        $index = 0;
        $count_stories = 1;
        $new_diff = $diff;
        
        $newData = [];
        
        foreach ($data as $i => $list) {
            $newData[] = $list;
        
            if (($count_stories == $new_diff) ) {
                $newData[] = $ads[$index]->toArray();
                $index++;
                $new_diff = $new_diff + $diff;
            }
            
            $count_stories++;
        }
        
        // $data = $newData;
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of topics.",
            "total" => @$topics['total'],
            "last_page" => @$topics['last_page'],
            "data" => $newData
        ], 200);
    }

    public function groupStories(Request $request) {
        $user_id=auth()->user()->id;
        $disinterested_groups=UserDisinterestedGroup::where('user_id',$user_id)->pluck('group');
        $disinterested_types=UserDisinterestedType::where('user_id',$user_id)->pluck('type');

        $disinterested_groups = array_map('trim', $disinterested_groups->toArray());
	
       // $blocked = DB::table('blocked_users')->where('user_id', '=', $user_id)->select('to_user')->pluck('to_user')->toArray();
        $groups=CategoryGroup::whereNotIn('name',$disinterested_groups)->pluck('name');
        $storyData = [];
        foreach($groups as $group){
           
            $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
            ->where(['topics.status' => 0, 'users.status' => 0])
            //->whereNotIn('topics.user_id', $blocked )
            ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 

            if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

                $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                        ->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                        ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                        ->select('topics.*', 'users.username as user_name','users.user_type as user_type', 'journal_counts.type as jtype')
                        ->groupBy("topics.id"); 
            }

            if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
                if(isset($request->timezone) && $request->timezone !=null){
                    $localTime= Carbon::now($request->timezone);
                    //echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
                     $query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
                }else{
                   $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
                }
            }

            if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
                $query->where('topics.user_id', '=', Auth::user()->id);
            }

            // if($request->filled('category_group')) {
            //     $mul = explode(',', $request->category_group);
            //     // $query->where('topics.category_group', '=', $request->category_group);
            //     $query->where(function($qr) use ($mul) {
            //         foreach ($mul as $title) {
            //             $title = trim($title);
            //             // $title = str_replace(', ', ',', $title);
            //             $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
            //         }
            //     }); 
            // }
            // if(isset($disinterested_groups[0])){
            //     $query->where(function($qr) use ($disinterested_groups,$user_id) {
            //         foreach ($disinterested_groups as $group) {
            //             $group  = trim($group);
            //             // $title = str_replace(', ', ',', $title);
            //             $qr->where(function($subQuery) use ($group, $user_id) {
            //                 $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
            //                         ->orWhere('topics.user_id', '=', $user_id);
            //             });
            //         }
            //     }); 
            // }
            if(isset($disinterested_types[0])) {
                $query->where(function($qr) use ($disinterested_types, $user_id) {
                    foreach ($disinterested_types as $type) {
                        $type  = trim($type);
                        $qr->where(function($subQuery) use ($type, $user_id) {
                            $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                                    ->orWhere('topics.user_id', '=', $user_id);
                        });
                    }
                });
            }

            if($request->filled('filter_topic')) {           
                $mul = explode(',', $request->filter_topic);
                if(count($mul) > 1) {
                    $query->where(function($qr) use ($mul) {
                        foreach ($mul as $title) {
                            $title = trim($title);
                            $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                        }
                    }); 
                } else {
                    $title = @$mul[0];
                    $query->where(function($qr) use ($title) {
                        $qr->where('topics.title', 'LIKE', "%{$title}%")
                            ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    }); 
                }
            }

            if($request->filled('filter_date')) {
                $filter_date = $request->filter_date;
                $query->whereDate('topics.created_at', '=', $filter_date);
            }

            $year = date("Y");
            $diff = $year - Auth::user()->birth_year;
            $type = ($diff < 18) ? "teens" : "adults"; 

            $query->where('topics.type', '=', $type);
            $query->where('topics.post_type', '=', 'story');
            $query->where('topics.category_group', 'LIKE', "%{$group}%");

            $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
            // $topics = $query->get()->toArray(); 
            $topics = $query->orderBy('created_at','DESC')->paginate(20)->toArray(); 
            $data=[];
            if(@$topics['data'] && count($topics['data'])) {    
                foreach ($topics['data'] as $topic) {     
                    $available_topic_array=explode(",",$topic["available_topic"]);
                    $topic["available_topic"]=$available_topic_array[0];
                    $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                    $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                    $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                    $user_rel = 0;
                    foreach($rels as $rel) {
                        if($rel->user_id == Auth::user()->id) {
                            $user_rel++;
                        }
                    }
                    $user_foll = 0;
                    foreach($folls as $foll) {
                        if($foll->user_id == Auth::user()->id) {
                            $user_foll++;
                        }
                    }
                    $topic["isFollow"] = ($user_foll) ? 1 : 0;
                    $topic["isRelated"] = ($user_rel) ? 1 : 0;
                    $topic["following_count"] = count($folls);
                    $topic["related_count"] = count($rels);

                    // if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                    // }
                    // array_push($data, $topic);           
                }
            }
            $story=[];
            if(!empty($data)){
                $storyData['group'] = $group;
                $storyData['data'] = $data;
                $story[]= $storyData;
            }
        }  

       /*
       ////// RICKY //////
        $ads = Advertisement::where('status','1' )->get();
        $adsIndexesArr = [];
        
        foreach($story as $key=>$list){
            if(count($list['data']) > 0){
                if(count($list['data']) > 5){
                    $needIndexes = round(count($list['data'])/3);
                }else{
                    $needIndexes = round(count($list['data'])/1);
                }
                $adsIndexesArr[$key][] = array_rand( $ads->toArray(), $needIndexes );
            }
        }
        foreach($adsIndexesArr as $key=>$item){
             $diff = count($story[$key]['data'])/count($item);
             $counter = 0;
             foreach($story[$key]['data'] as $i=>$it){
                if(($i%$diff) == 0){
                    $story[$key]['data']=array_splice($story[$key]['data'], $i, 0, $ads[$item[$counter]] );    
                    $counter++;
                }
             }
            }

        // dd($adsIndexesArr);
        ////// RICKY //////
        */



        $counter=0;

        //story count
        foreach($story as $list){
            foreach($list['data'] as $item){
                $counter++;    
            }
        }

        $ads = Advertisement::where('status','1' )->get();
        if(count($ads) >0){
            $diff=$counter / count($ads);

        }
        $diff=round($diff);
        // dd($diff);
        // if($diff<=0){
        //     $diff=1;
        // }
        $index=0;  
        $count_stories=1;
        $new_diff= $diff;
        foreach($story as $i=>$list){
            foreach($list['data'] as $key => $item){
               
                if(($count_stories == $new_diff) &&  ($new_diff<=count($ads))){
                    array_splice($story[$i]['data'],$diff,0,[$ads[$index]->toArray()]);
                    $index++;
                    
                }
                $count_stories++;
                $new_diff= $new_diff + $diff;
            }
        } 
       
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of topics.",
            "data" => $story
        ], 200);
    }

    public function userStories(Request $request) {
        $user_id=auth()->user()->id;
        $disinterested_groups=UserDisinterestedGroup::where('user_id',$user_id)->pluck('group');
        $disinterested_types=UserDisinterestedType::where('user_id',$user_id)->pluck('type');
        $blocked = DB::table('blocked_users')->where('user_id', '=', $user_id)->select('to_user')->pluck('to_user')->toArray();
        // $disinterested_groups = array_map('trim', $disinterested_groups->toArray());
	    //  $groups=CategoryGroup::whereNotIn('name',$disinterested_groups)->pluck('name');
        $users= User::whereNotIn('id',$blocked)->where('status',0)->whereHas('story',function ($query) {
            return $query->where('post_type', 'story');
        })->orderBy(function ($query) {
            $query->select('created_at')
                ->from('topics')
                ->whereColumn('topics.user_id', 'users.id')->latest('id')
                ->limit(1);
        }, 'desc' )->paginate(20);
        $storyData = [];
        $story=[];
        foreach($users as $user){
            $category_group=[] ;
            $query = Topic::where('topics.user_id',$user->id)->join('users', 'topics.user_id', '=', 'users.id')
            ->where(['topics.status' => 0])
            ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 

            if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

                $query = Topic::where('topics.user_id',$user->id)->join('users', 'topics.user_id', '=', 'users.id')->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                        ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                        ->select('topics.*', 'users.username as user_name','users.user_type as user_type', 'journal_counts.type as jtype')
                        ->groupBy("topics.id"); 
            }
            // dd(date('Y-m-d'));
			if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
				if(isset($request->timezone) && $request->timezone !=null){
					$localTime= Carbon::now($request->timezone);
					//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
					$query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
				}else{
				   $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
				}
			}

            if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
                $query->where('topics.user_id', '=', Auth::user()->id);
            }
			
			//Add new code filter peer group data 
			//if ($request->filled('filter_peer_group')) {
				//$query->whereHas('groups', function ($q) use ($request) {
				//	$q->where('peer_group_id', $request->filter_peer_group);
				//});
			//}

            if($request->filled('category_group')) {
                $mul = explode(',', $request->category_group);
                // $query->where('topics.category_group', '=', $request->category_group);
                $query->where(function($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $title = trim($title);
                        // $title = str_replace(', ', ',', $title);
                        $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
                    }
                }); 
            }
            if(isset($disinterested_groups[0])){
                $query->where(function($qr) use ($disinterested_groups,$user_id) {
                    foreach ($disinterested_groups as $group) {
                        $group  = trim($group);
                        // $title = str_replace(', ', ',', $title);
                        $qr->where(function($subQuery) use ($group, $user_id) {
                            $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
                                    ->orWhere('topics.user_id', '=', $user_id);
                        });
                    }
                }); 
            }
            if(isset($disinterested_types[0])) {
                $query->where(function($qr) use ($disinterested_types, $user_id) {
                    foreach ($disinterested_types as $type) {
                        $type  = trim($type);
                        $qr->where(function($subQuery) use ($type, $user_id) {
                            $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                                    ->orWhere('topics.user_id', '=', $user_id);
                        });
                    }
                });
            }

            if($request->filled('filter_topic')) {           
                $mul = explode(',', $request->filter_topic);
                if(count($mul) > 1) {
                    $query->where(function($qr) use ($mul) {
                        foreach ($mul as $title) {
                            $title = trim($title);
                            $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                        }
                    }); 
                } else {
                    $title = @$mul[0];
                    $query->where(function($qr) use ($title) {
                        $qr->where('topics.title', 'LIKE', "%{$title}%")
                            ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    }); 
                }
            }

            if($request->filled('filter_date')) {
                $filter_date = $request->filter_date;
                $query->whereDate('topics.created_at', '=', $filter_date);
            }

            $year = date("Y");
            $diff = $year - Auth::user()->birth_year;
            $type = ($diff < 18) ? "teens" : "adults"; 

            $query->where('topics.type', '=', $type);
            $query->where('topics.post_type', '=', 'story');
            // $query->where('topics.category_group', 'LIKE', "%{$group}%");

            $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
            // $topics = $query->get()->toArray(); 
            $topics = $query->orderBy('created_at','DESC')->get()->toArray(); 
            $data=[];
            if(@$topics && count($topics)) {   
                foreach ($topics as $topic) {     
                    $category_group[]=$topic['category_group'];
                    $available_topic_array=explode(",",$topic["available_topic"]);
                    $topic["available_topic"]=$available_topic_array[0];
                    $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                    $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                    $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                    $user_rel = 0;
                    foreach($rels as $rel) {
                        if($rel->user_id == Auth::user()->id) {
                            $user_rel++;
                        }
                    }
                    $user_foll = 0;
                    foreach($folls as $foll) {
                        if($foll->user_id == Auth::user()->id) {
                            $user_foll++;
                        }
                    }
                    $topic["isFollow"] = ($user_foll) ? 1 : 0;
                    $topic["isRelated"] = ($user_rel) ? 1 : 0;
                    $topic["following_count"] = count($folls);
                    $topic["related_count"] = count($rels);

                    // if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                    // }
                    // array_push($data, $topic);           
                }
            }
            if(!empty($data)){
                $storyData['user'] = $user->name;
                $storyData['categories']= implode(",",$category_group);
                $storyData['data'] = $data;
                $story[]= $storyData;
            }
        }  

        $counter=0;

        //story count
        if(!empty($story)){
            foreach($story as $list){
                foreach($list['data'] as $item){
                    $counter++;    
                }
            }

            $ads = Advertisement::where('status','1' )->get();
            $diff =0;
            if(count($ads) >0){
               $diff=$counter / count($ads);
            }
            $diff=round($diff);
            // dd($diff);
            // if($diff<=0){
            //     $diff=1;
            // }
            $index=0;  
            $count_stories=1;
            $new_diff= $diff;
            foreach($story as $i=>$list){
                foreach($list['data'] as $key => $item){
                
                    if(($count_stories == $new_diff)){
                        array_splice($story[$i]['data'],$key,0,[$ads[$index]->toArray()]);
                        $index++;
                        $new_diff= $new_diff + $diff;
                    }
                    $count_stories++;

                }
            } 
        }
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of user stories.",
            "total" => $users->total(),
            "last_page" => @$users->lastPage(),
            "data" => $story
        ], 200);
    }


    // public function addTopicNew(Request $request) {

    //     $validator = Validator::make($request->all(), [ 
    //         'date' => 'required', 
    //         'title' => 'required', 
    //         'details' => 'required'
    //     ]);

    //     if ($validator->fails()) { 
    //         return response()->json([
    //             "ReturnCode" => 0,
    //             "ReturnMessage" => $validator->errors()->first(),
    //             "data" => null
    //         ], 200);         
    //     }

    //     $input = $request->all();

    //     $images = array();

    //     // file_put_contents('files.txt', print_r($files, true) . "\n", FILE_APPEND);
    //     // file_put_contents('input.txt', print_r($input, true) . "\n", FILE_APPEND);

    //     if($request->has('image')) {

    //         $files = $request->file('image');            
    //         $path = public_path('uploads/topic_files/');           
    //         foreach ($files as $key => $file) {
    //             $fileType = $file->getMimeType();
    //             $fileName = $file->getClientOriginalName();
    //             $fileExtension = $file->getClientOriginalExtension();
    //             // $fileName = time().'-'.rand(3,100).'.'. $fileExtension;
    //             // $fileName = $fileName.'.'. $fileExtension;
    //             $check = $file->move($path, $fileName);  
    //             if($check) {
    //                 array_push($images, [
    //                     "name" => $fileName,
    //                     "type" => $fileType,
    //                 ]);
    //             }
    //         }
    //     }

    //     $input['image'] = count($images) ? serialize($images) : null;

    //     if(Auth::user()->birth_year) {
    //         $year = date("Y");
    //         $diff = $year - Auth::user()->birth_year;
    //         $type = ($diff < 18) ? "teens" : "adults"; 
    //         $input['type'] = $type;
    //     }

    //     $input['user_id'] = Auth::user()->id;
    //     $date = $input['date'];
    //     if($request->filled("id")) {
    //         $input['updated_at'] = $date;
    //         $topic = Topicnew::find($input["id"])->update($input);
    //     } else {    
    //         $input['created_at'] = $date;
    //         $topic = new Topicnew($input);
    //         $topic->save();
    //     }


    //     $topic->image = $images;

    //     return response()->json([
    //         "ReturnCode" => 1,
    //         "ReturnMessage" => "Topic saved.",
    //         "data" => $topic
    //     ], 200);
        
    // }


    // public function topicsNew(Request $request) {

    //     $query = Topicnew::join('users', 'topicsnew.user_id', '=', 'users.id')
    //                 ->where(['topicsnew.status' => 0, 'users.status' => 0])
    //                 ->select('topicsnew.*', 'users.username as user_name'); 

    //     if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

    //         $query = Topicnew::join('users', 'topicsnew.user_id', '=', 'users.id')
    //                 ->join('journal_counts', 'topicsnew.id', '=', 'journal_counts.topic_id')
    //                 ->where(['topicsnew.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
    //                 ->select('topicsnew.*', 'users.username as user_name', 'journal_counts.type')
    //                 ->groupBy("topicsnew.id"); 
    //     }

    //     if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
    //         $query->whereDate('topicsnew.created_at', '=', date('Y-m-d' , strtotime($request->filter_date)));
    //     }

    //     if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
    //         $query->where('topicsnew.user_id', '=', Auth::user()->id);
    //     }

    //     if($request->filled('filter_topic')) {
           
    //         $mul = explode(',', $request->filter_topic);
    //         if(count($mul) > 1) {
    //             $query->where(function($qr) use ($mul) {
    //                 foreach ($mul as $title) {
    //                     $qr->orWhere('topicsnew.available_topic', 'LIKE', "%{$title}%");
    //                 }
    //             }); 
    //         } else {
    //             $title = @$mul[0];
    //             $query->where(function($qr) use ($title) {
    //                 $qr->where('topicsnew.title', 'LIKE', "%{$title}%")
    //                     ->orWhere('topicsnew.available_topic', 'LIKE', "%{$title}%");
    //             }); 
    //         }
    //     }

    //     if($request->filled('filter_date')) {
    //         $filter_date = $request->filter_date;
    //         $query->whereDate('topicsnew.created_at', '=', $filter_date);
    //     }

    //     $year = date("Y");
    //     $diff = $year - Auth::user()->birth_year;
    //     $type = ($diff < 18) ? "teens" : "adults"; 
 
    //     $query->where('topicsnew.type', '=', $type);
    //     $topics = $query->get()->toArray(); 
     
    //     $data = [];

    //     if(count($topics)) {
    //         foreach ($topics as $topic) {                
    //             $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
    //             $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
    //             $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
    //             $user_rel = 0;
    //             foreach($rels as $rel) {
    //                 if($rel->user_id == Auth::user()->id) {
    //                     $user_rel++;
    //                 }
    //             }
    //             $user_foll = 0;
    //             foreach($folls as $foll) {
    //                 if($foll->user_id == Auth::user()->id) {
    //                     $user_foll++;
    //                 }
    //             }
    //             $topic["isFollow"] = ($user_foll) ? 1 : 0;
    //             $topic["isRelated"] = ($user_rel) ? 1 : 0;
    //             $topic["following_count"] = count($folls);
    //             $topic["related_count"] = count($rels);
    //             $images = [];
    //             if(@unserialize($topic['image'])) {
    //                 $images = unserialize($topic['image']);
    //             } else {
    //                 array_push($images, [
    //                     "name" => $topic['image'],
    //                     "type" => 'image/png',
    //                 ]);
    //             }
    //             $topic["image"] = $images;
    //             if($topic['created_at'] != "0000-00-00 00:00:00") {
    //                 array_push($data, $topic);
    //             }
    //         }
    //     }
        
    //     return response()->json([
    //         "ReturnCode" => 1,
    //         "ReturnMessage" => "list of topics.",
    //         "data" => $data
    //     ], 200);
    // }

    public function topic($id) {

        $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                    ->where(['topics.status' => 0, 'users.status' => 0, 'topics.id' => $id])
                    ->select('topics.*', 'users.username as user_name','users.user_type as user_type'); 
        
        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
 
        $query->where('topics.type', '=', $type);
        $topics = $query->get()->toArray(); 
     
        $data = [];

        if(count($topics)) {
            foreach ($topics as $topic) {                
                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);
                // $images = [];
                // if(@unserialize($topic['image'])) {
                //     $images = unserialize($topic['image']);
                // } else {
                //     array_push($images, [
                //         "name" => $topic['image'],
                //         "type" => 'image/png',
                //     ]);
                // }
                // $topic["image"] = $images;
                if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                }
            }
        }
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "topic details.",
            "data" => @$data[0]
        ], 200);
    }

    public function availableTopics() 
    {
        
      $categories = DB::table('categories')->get();
		
		$categoryGroups = CategoryGroup::orderBy('indexed', 'ASC')->get();
		
		//dd($categories, $categoryGroups);
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of categories.",
            "data" => $categories
        ], 200);
    }

    // public function mentalhealth() {
    //     $topics = DB::table('mental_health')->join('categories', 'mental_health.category_id', '=', 'categories.id')
    //                     ->select('mental_health.*', 'categories.name as category_name')->get();
    //     return response()->json([
    //         "ReturnCode" => 1,
    //         "ReturnMessage" => "list",
    //         "data" => $topics
    //     ], 200);
    // }
    public function mentalhealth(Request $request) {

        $query = DB::table('mental_health')->join('categories', 'mental_health.category_id', '=', 'categories.id')
                        ->select('mental_health.*', 'categories.name as category_name');

        if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = DB::table('mental_health')
                        ->join('categories', 'mental_health.category_id', '=', 'categories.id')
                        ->join('journal_counts', 'mental_health.id', '=', 'journal_counts.health_id')
                        ->where(["journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                        ->select('mental_health.*', 'categories.name as category_name', 'journal_counts.type')
                        ->groupBy("mental_health.id");

        }
        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if(isset($request->timezone) && $request->timezone !=null){
				$localTime= Carbon::now($request->timezone);
				//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
				 $query->whereBetween('mental_health.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
			}else{
               $query->whereDate('mental_health.created_at', '=',  date('Y-m-d'));
			}
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('mental_health.created_at', '=', $filter_date);
        }

        $query->where('mental_health.status', '=', 'Live');

        $topics = $query->get()->toArray(); 

        $data = [];

        if(count($topics)) {    

            foreach ($topics as $topic) {     
                
                $topic = (array) $topic;
    
                $topic["comments_count"] = JournalComments::where(["health_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "health_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "health_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                }

                // array_push($data, $topic);           
            }
        }



        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list",
            "data" => $data
        ], 200);
    }

    public function justmental_podcast(Request $request) {
        $query = DB::table('justmental_podcast')->join('justmental_categories', 'justmental_podcast.category_id', '=', 'justmental_categories.id')
        ->select('justmental_podcast.*', 'justmental_categories.name as category_name');

        if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = DB::table('justmental_podcast')
                        ->join('justmental_categories', 'justmental_podcast.category_id', '=', 'justmental_categories.id')
                        ->join('journal_counts', 'justmental_podcast.id', '=', 'journal_counts.podcast_id')
                        ->where(["journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                        ->select('justmental_podcast.*', 'justmental_categories.name as category_name', 'journal_counts.type')
                        ->groupBy("justmental_podcast.id");

        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if(isset($request->timezone) && $request->timezone !=null){
				$localTime= Carbon::now($request->timezone);
				//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
				 $query->whereBetween('justmental_podcast.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
			}else{
               $query->whereDate('justmental_podcast.created_at', '=',  date('Y-m-d'));
			}
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('justmental_podcast.created_at', '=', $filter_date);
        }

        $query->where('justmental_podcast.status', '=', 'Live');

        $topics = $query->get()->toArray(); 

        $data = [];

        if(count($topics)) {    

            foreach ($topics as $topic) {     
                
                $topic = (array) $topic;
    
                $topic["comments_count"] = JournalComments::where(["podcast_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "podcast_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "podcast_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                }

                // array_push($data, $topic);           
            }
        }
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list",
            "data" => $data
        ], 200);
       
    }

    public function supports(Request $request) {

        $query = Support::join('users', 'support.user_id', '=', 'users.id')
                    ->where(['support.status' => 0, 'users.status' => 0])
                    ->select('support.*', 'users.username as user_name','users.user_type as user_type'); 

        if($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = Topicnew::join('users', 'support.user_id', '=', 'users.id')
                    ->join('journal_counts', 'support.id', '=', 'journal_counts.topic_id')
                    ->where(['support.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id ])
                    ->select('support.*', 'users.username as user_name','users.user_type as user_type', 'journal_counts.type')
                    ->groupBy("support.id"); 
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if(isset($request->timezone) && $request->timezone !=null){
				$localTime= Carbon::now($request->timezone);
				//echo($localTime->startOfDay()->copy()->timezone("UTC").' '.$localTime->endOfDay()->copy()->timezone("UTC"));
				 $query->whereBetween('support.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"),$localTime->endOfDay()->copy()->timezone("UTC")]);
			}else{
               $query->whereDate('support.created_at', '=',  date('Y-m-d'));
			}
        }

        if($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
            $query->where('support.user_id', '=', Auth::user()->id);
        }

        if($request->filled('filter_topic')) {
           
            $mul = explode(',', $request->filter_topic);
            if(count($mul) > 1) {
                $query->where(function($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $qr->orWhere('support.available_topic', 'LIKE', "%{$title}%");
                    }
                }); 
            } else {
                $title = @$mul[0];
                $query->where(function($qr) use ($title) {
                    $qr->where('support.title', 'LIKE', "%{$title}%")
                        ->orWhere('support.available_topic', 'LIKE', "%{$title}%");
                }); 
            }
        }

        if($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('support.created_at', '=', $filter_date);
        }

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults"; 
 
        $query->where('support.type', '=', $type);
        $topics = $query->get()->toArray(); 
     
        $data = [];

        if(count($topics)) {
            foreach ($topics as $topic) {                
                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where([ "type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where([ "type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach($rels as $rel) {
                    if($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach($folls as $foll) {
                    if($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);
                // $images = [];
                // if(@unserialize($topic['image'])) {
                //     $images = unserialize($topic['image']);
                // } else {
                //     array_push($images, [
                //         "name" => $topic['image'],
                //         "type" => 'image/png',
                //     ]);
                // }
                $topic["image"] = $topic['image'];
                if($topic['created_at'] != "0000-00-00 00:00:00") {
                    array_push($data, $topic);
                }
            }
        }
        
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of supports.",
            "data" => $data
        ], 200);
    }
	    public function blockTopics(Request $request){
        
        $blockedTopic= BlockedTopic::insert(
            ['user_id' => auth()->user()->id, 'topic_id' => $request->post_id]
        );

         return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Topic Blocked",
        ], 200);
    }

    public function unblockTopics(Request $request){
        
        BlockedTopic::where(
            ['user_id' => auth()->user()->id, 'topic_id' => $request->post_id]
        )->delete();

         return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Topic Unblocked",
        ], 200);
    }

    public function BlockedTopicsList(Request $request){
        $BlockedTopicIds=BlockedTopic::where('user_id', auth()->user()->id)->get(['topic_id']);

        $BlockedTopics=Topic::whereIn('id',$BlockedTopicIds)->get();


         return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Blocked Topics List",
            "data" =>  $BlockedTopics
        ], 200);
    }

}