<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostStoryResource;
use App\Models\PeerGroup;
use App\Models\BlockedTopic;
use App\Models\UserDisinterestedGroup;
use App\Models\UserDisinterestedType;
use App\Models\Topic;
use App\Models\Advertisement;
use App\Models\Notification;
use App\User;
use App\Models\JournalComments;
use App\Models\JournalCount;
use Illuminate\Support\Facades\Auth;
use DB;
use Validator;
use App\Http\Traits\GroupTrait;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GroupController extends Controller
{
	use GroupTrait;
    public function index()
	{
		$category_group = PeerGroup::withCount(['users', 'topics' => function ($query) {
        $query->where('post_type', '!=', 'story');
        }])->orderBy('indexed','asc')->get();
		
		//dd($category_group);
		return response()->json(["ReturnCode" => 1,
            "ReturnMessage" => "Peer Groups List.",            
            "data" => GroupResource::collection($category_group),
        ], 200);
	}
	
	public function joinUnjoinStatus(Request $request)
	{
		// Check if the user belongs to the group
		$existingRelation = auth()->user()->groups()->where('peer_group_id', $request->group_id)->first();
		
		if(empty($existingRelation) && $request->status == 'join'){
			auth()->user()->groups()->attach($request->group_id, ['status' => 1]);
		}elseif(!empty($existingRelation) && $request->status == 'unjoin'){
			auth()->user()->groups()->detach($request->group_id);
		}elseif(!empty($existingRelation) && $request->status == 'mute'){
			$data = $existingRelation->users()->updateExistingPivot(auth()->user()->id, ['status' => 0]);
		}elseif(!empty($existingRelation) && $request->status == 'unmute'){
			$data = $existingRelation->users()->updateExistingPivot(auth()->user()->id, ['status' => 1]);
		}
		
		return response()->json(["ReturnCode" => 1,
            "ReturnMessage" => "Status Updated.",            
            "data" => null,
        ], 200);
		
	}
	
	public function peerGroupPost(Request $request)
    {

        $disinterested_groups = UserDisinterestedGroup::where('user_id', auth()->user()->id)->pluck('group');
        $disinterested_types = UserDisinterestedType::where('user_id', auth()->user()->id)->pluck('type');
        $user_id = auth()->user()->id;

        $peerGroup = PeerGroup::withCount(['users', 'topics' => function ($query) use ($request) {
        $query->where('post_type', '!=', 'story');
        $query->where('peer_group_id', $request->filter_peer_group);
        }])->find($request->filter_peer_group);


        $blockedTopics = BlockedTopic::where('user_id', '=', auth()->user()->id)->select('topic_id')->pluck('topic_id')->toArray();

        $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
            ->where(['topics.status' => 0, 'users.status' => 0])
            ->whereNotIn('topics.id', $blockedTopics)
            ->select('topics.*', 'users.username as user_name', 'users.user_type as user_type');
        if ($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

            $query = Topic::join('users', 'topics.user_id', '=', 'users.id')
                ->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id])
                ->select('topics.*', 'users.username as user_name', 'users.user_type as user_type', 'journal_counts.type as jtype')
                ->groupBy("topics.id");
        }

        if ($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
            if (isset($request->timezone) && $request->timezone != null) {
                $localTime = Carbon::now($request->timezone);
                $query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"), $localTime->endOfDay()->copy()->timezone("UTC")]);
            } else {
                $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
            }
        }

        if ($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
            $query->where('topics.user_id', '=', Auth::user()->id);
        }

        //Add new code filter peer group data 
        if ($request->filled('filter_peer_group')) {
			$query->whereHas('groups', function ($q) use ($request) {
				$q->where('peer_group_id', $request->filter_peer_group);
			});
        }else{
			$query->doesntHave('groups');
		}

        if ($request->filled('category_group')) {
            $mul = explode(',', $request->category_group);
            $query->where(function ($qr) use ($mul) {
                foreach ($mul as $title) {
                    $title = trim($title);
                    $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
                }
            });
        }
        if (isset($disinterested_groups[0])) {
            $query->where(function ($qr) use ($disinterested_groups, $user_id) {
                foreach ($disinterested_groups as $group) {
                    $group  = trim($group);
                    $qr->where(function ($subQuery) use ($group, $user_id) {
                        $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
                            ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            });
        }
        if (isset($disinterested_types[0])) {
            $query->where(function ($qr) use ($disinterested_types, $user_id) {
                foreach ($disinterested_types as $type) {
                    $type  = trim($type);
                    $qr->where(function ($subQuery) use ($type, $user_id) {
                        $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                            ->orWhere('topics.user_id', '=', $user_id);
                    });
                }
            });
        }

        if ($request->filled('filter_topic')) {
            $mul = explode(',', $request->filter_topic);
            if (count($mul) > 1) {
                $query->where(function ($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $title = trim($title);
                        $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    }
                });
            } else {
                $title = @$mul[0];
                $query->where(function ($qr) use ($title) {
                    $qr->where('topics.title', 'LIKE', "%{$title}%")
                        ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                });
            }
        }

        if ($request->filled('filter_date')) {
            $filter_date = $request->filter_date;
            $query->whereDate('topics.created_at', '=', $filter_date);
        }

        $year = date("Y");
        $diff = $year - Auth::user()->birth_year;
        $type = ($diff < 18) ? "teens" : "adults";

        $query->where('topics.type', '=', $type);
        $query->where('topics.post_type', '!=', 'story');


        $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
        $topics = $query->orderBy('created_at', 'DESC')->paginate(20)->toArray();

        $data = [];

        if (@$topics['data'] && count($topics['data'])) {

            foreach ($topics['data'] as $topic) {

                $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                $folls = JournalCount::where(["type" => "following", "topic_id" => $topic['id']])->get();
                $rels = JournalCount::where(["type" => "related", "topic_id" => $topic['id']])->get();
                $user_rel = 0;
                foreach ($rels as $rel) {
                    if ($rel->user_id == Auth::user()->id) {
                        $user_rel++;
                    }
                }
                $user_foll = 0;
                foreach ($folls as $foll) {
                    if ($foll->user_id == Auth::user()->id) {
                        $user_foll++;
                    }
                }
                $topic["isFollow"] = ($user_foll) ? 1 : 0;
                $topic["isRelated"] = ($user_rel) ? 1 : 0;
                $topic["following_count"] = count($folls);
                $topic["related_count"] = count($rels);

                array_push($data, $topic);
            }
        }

        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "list of topics.",
            "total" => @$topics['total'],
            "last_page" => @$topics['last_page'],
            "peer_group" => $peerGroup,
            "data" => $data,
        ], 200);
    } 
	
	public function peerUserStories(Request $request)
    {
        $user_id = auth()->user()->id;
        $disinterested_groups = UserDisinterestedGroup::where('user_id', $user_id)->pluck('group');
        $disinterested_types = UserDisinterestedType::where('user_id', $user_id)->pluck('type');
        $blocked = DB::table('blocked_users')->where('user_id', '=', $user_id)->select('to_user')->pluck('to_user')->toArray();

        $users = User::whereNotIn('id', $blocked)->where('status', 0)->whereHas('story', function ($query) {
            return $query->where('post_type', 'story');
        })->orderBy(function ($query) {
            $query->select('created_at')
                ->from('topics')
                ->whereColumn('topics.user_id', 'users.id')->latest('id')
                ->limit(1);
        }, 'desc')->paginate(20);
        $storyData = [];
        $story = [];
        foreach ($users as $user) {
            $category_group = [];
            $query = Topic::where('topics.user_id', $user->id)->join('users', 'topics.user_id', '=', 'users.id')
                ->where(['topics.status' => 0])
                ->select('topics.*', 'users.username as user_name', 'users.user_type as user_type');

            if ($request->filled('sub_menu') && (strtolower($request->sub_menu) == "following" || strtolower($request->sub_menu) == "related")) {

                $query = Topic::where('topics.user_id', $user->id)->join('users', 'topics.user_id', '=', 'users.id')->join('journal_counts', 'topics.id', '=', 'journal_counts.topic_id')
                    ->where(['topics.status' => 0, "journal_counts.type" => strtolower($request->sub_menu), "journal_counts.user_id" => Auth::user()->id])
                    ->select('topics.*', 'users.username as user_name', 'users.user_type as user_type', 'journal_counts.type as jtype')
                    ->groupBy("topics.id");
            }

            if ($request->filled('sub_menu') && strtolower($request->sub_menu) == "today") {
                if (isset($request->timezone) && $request->timezone != null) {
                    $localTime = Carbon::now($request->timezone);
                    $query->whereBetween('topics.created_at', [$localTime->startOfDay()->copy()->timezone("UTC"), $localTime->endOfDay()->copy()->timezone("UTC")]);
                } else {
                    $query->whereDate('topics.created_at', '=',  date('Y-m-d'));
                }
            }

            if ($request->filled('sub_menu') && strtolower($request->sub_menu) == "my posts") {
                $query->where('topics.user_id', '=', Auth::user()->id);
            }

            //Add new code filter peer group data 
            if ($request->filled('filter_peer_group')) {
                $query->whereHas('groups', function ($q) use ($request) {
                    $q->where('peer_group_id', $request->filter_peer_group);
                });
            }else{
				$query->doesntHave('groups');
			}

            if ($request->filled('category_group')) {
                $mul = explode(',', $request->category_group);
                $query->where(function ($qr) use ($mul) {
                    foreach ($mul as $title) {
                        $title = trim($title);
                        $qr->orWhere('topics.category_group', 'LIKE', "%{$title}%");
                    }
                });
            }
            if (isset($disinterested_groups[0])) {
                $query->where(function ($qr) use ($disinterested_groups, $user_id) {
                    foreach ($disinterested_groups as $group) {
                        $group  = trim($group);
                        $qr->where(function ($subQuery) use ($group, $user_id) {
                            $subQuery->where('topics.category_group', 'NOT LIKE', "%{$group}%")
                                ->orWhere('topics.user_id', '=', $user_id);
                        });
                    }
                });
            }
            if (isset($disinterested_types[0])) {
                $query->where(function ($qr) use ($disinterested_types, $user_id) {
                    foreach ($disinterested_types as $type) {
                        $type  = trim($type);
                        $qr->where(function ($subQuery) use ($type, $user_id) {
                            $subQuery->where('topics.available_topic', 'NOT LIKE', "%{$type}%")
                                ->orWhere('topics.user_id', '=', $user_id);
                        });
                    }
                });
            }

            if ($request->filled('filter_topic')) {
                $mul = explode(',', $request->filter_topic);
                if (count($mul) > 1) {
                    $query->where(function ($qr) use ($mul) {
                        foreach ($mul as $title) {
                            $title = trim($title);
                            $qr->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                        }
                    });
                } else {
                    $title = @$mul[0];
                    $query->where(function ($qr) use ($title) {
                        $qr->where('topics.title', 'LIKE', "%{$title}%")
                            ->orWhere('topics.available_topic', 'LIKE', "%{$title}%");
                    });
                }
            }

            if ($request->filled('filter_date')) {
                $filter_date = $request->filter_date;
                $query->whereDate('topics.created_at', '=', $filter_date);
            }

            $year = date("Y");
            $diff = $year - Auth::user()->birth_year;
            $type = ($diff < 18) ? "teens" : "adults";

            $query->where('topics.type', '=', $type);
            $query->where('topics.post_type', '=', 'story');

            $query->where('topics.created_at', '!=', '0000-00-00 00:00:00');
            $topics = $query->orderBy('created_at', 'DESC')->get()->toArray();
            $data = [];
            if (@$topics && count($topics)) {
                foreach ($topics as $topic) {
                    $category_group[] = $topic['category_group'];
                    $available_topic_array = explode(",", $topic["available_topic"]);
                    $topic["available_topic"] = $available_topic_array[0];
                    $topic["comments_count"] = JournalComments::where(["topic_id" => $topic['id']])->count();
                    $folls = JournalCount::where(["type" => "following", "topic_id" => $topic['id']])->get();
                    $rels = JournalCount::where(["type" => "related", "topic_id" => $topic['id']])->get();
                    $user_rel = 0;
                    foreach ($rels as $rel) {
                        if ($rel->user_id == Auth::user()->id) {
                            $user_rel++;
                        }
                    }
                    $user_foll = 0;
                    foreach ($folls as $foll) {
                        if ($foll->user_id == Auth::user()->id) {
                            $user_foll++;
                        }
                    }
                    $topic["isFollow"] = ($user_foll) ? 1 : 0;
                    $topic["isRelated"] = ($user_rel) ? 1 : 0;
                    $topic["following_count"] = count($folls);
                    $topic["related_count"] = count($rels);

                    array_push($data, $topic);
                }
            }
            if (!empty($data)) {
                $storyData['user'] = $user->name;
                $storyData['categories'] = implode(",", $category_group);
                $storyData['data'] = $data;
                $story[] = $storyData;
            }
        }

        $counter = 0;

        //story count
        if (!empty($story)) {
            foreach ($story as $list) {
                foreach ($list['data'] as $item) {
                    $counter++;
                }
            }

            $ads = Advertisement::where('status', '1')->get();
            $diff = 0;
            if (count($ads) > 0) {
                $diff = $counter / count($ads);
            }
            $diff = round($diff);
            $index = 0;
            $count_stories = 1;
            $new_diff = $diff;
            foreach ($story as $i => $list) {
                foreach ($list['data'] as $key => $item) {

                    if (($count_stories == $new_diff)) {
                        array_splice($story[$i]['data'], $key, 0, [$ads[$index]->toArray()]);
                        $index++;
                        $new_diff = $new_diff + $diff;
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
	
	public function peerAddTopic(Request $request)
    {
        try {

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

            $peerGroup = $request->input('peer_group');

            $peerGroupsArray = [];
            if ($peerGroup) {
                $peerGroupsArray = explode(',', $peerGroup);
                $peerGroupsArray = array_map('trim', $peerGroupsArray);
           	}

            if ($request->input('post_type') == 'story') {
                $postType = 'story';
            } else {
                $postType = 'post';
            }


            if ($request->has('image')) {
                $file = @$request->file('image');
                if ($file) {
                    $path = Storage::disk('s3')->put('topics', $file);
                    $input['image'] = $path;
                }
            }

            if (Auth::user()->birth_year) {
                $year = date("Y");
                $diff = $year - Auth::user()->birth_year;
                $type = ($diff < 18) ? "teens" : "adults";
                $input['type'] = $type;
            }

            $input['user_id'] = Auth::user()->id;
            $date = $input['date'];
            if ($request->filled("id")) {
                $input['updated_at'] = $date;
                $topic = Topic::find($input["id"]);
                $topic->update($input);
				if(!empty($peerGroupsArray)){
                	$topic->groups()->sync($peerGroupsArray, ['type' => $postType, 'status' => 1]);
				}
            } else {
                $input['created_at'] = $date;
				
				$peerGroupUser = PeerGroup::with(['users' => function($query) {
					$query->wherePivot('status', '1');
					$query->wherePivot('user_id', '!=', auth()->user()->id);
				}])->find($peerGroup);
				
                $topic = new Topic($input);
                $topic->save();
				if(!empty($peerGroupsArray)){
					$topic->groups()->attach($peerGroupsArray, ['type' => $postType, 'status' => 1]);

					if(count($peerGroupUser->users) > 0){

						$message = Auth::user()->username. " created a new post on your group, ".$peerGroupUser->name;
						foreach($peerGroupUser->users as $user){
							if($user->pivot->status == '1'){
								if(!in_array('all_notification', $user->notification_check)){
									
									if(!in_array('rooms', $user->notification_check)){
										
										$rep = $this->sendPush($user->device_token, $user->device_type, $message, "Group", $topic->id);
										$resource = new Notification;
										$resource->title = "Ventspace";
										$resource->message = $message;
										$resource->from_user = Auth::user()->id;
										$resource->to_user = $user->id;
										$resource->type = "GROUP";
										$resource->pid = $topic->id;
										$resource->created_at = $input['created_at'];
										$resource->save();
									
									}
									
									
								}
								
							}
						}
					}
				}
                
            }

            return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Topic saved.",
                "data" => $input
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "ReturnCode" => 0,
                "ReturnMessage" => $e->getMessage(),
                "data" => null
            ], 200);
        }
    }
	
	public function listStoryPost(Request $request)
	{
		$peerGroup = PeerGroup::with(['topics' => function ($query) use ($request) {
			$query->withCount('comments');
			$query->wherePivot('peer_group_id', $request->group_id);
		}, 'users'])->withCount(['users', 'topics'])->find($request->group_id);

		$data = new PostStoryResource($peerGroup);
		
		return response()->json(["ReturnCode" => 1,
            "ReturnMessage" => "Post Story list", 
            "data" => $data,
        ], 200);
	}

}
