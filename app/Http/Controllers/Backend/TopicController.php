<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\PeerGroup;
use App\Models\JournalComments;
use App\Models\AvailableTopics;
use App\Models\CategoryGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 


class TopicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        
        $topics = Topic::with('comments')->whereHas('user', function ($query) {
            $query->where('user_type','!=','Admin');
        })->orderBy('id','DESC');

        if($request->filled('group')) {            
            //$topics->where('category_group', 'LIKE', '%'.$request->group.'%');
			$attributes = explode(',', $request->group);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('category_group', 'LIKE', '%'.$value.'%');
				}
			});
        }
		
        if($request->filled('mental_health')) {            
            //$topics->where('available_topic', 'LIKE', '%'.$request->mental_health.'%');
			$attributes = explode(',', $request->mental_health);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('available_topic', 'LIKE', '%'.$value.'%');
				}
			});
        }

        if($request->has('q')) { 
            if($request->q == 'audio' || $request->q == 'video') {
                $topics->where('content_type', 'LIKE', $request->q);
            }
        }

        // dd($topics->toSql());
        $topics->where('post_type', '!=', 'story');
        $topics = $topics->paginate(20);

        $users = User::where('id', '!=', 2)->get();
        $availabletopics = AvailableTopics::where('type', '=', 'teens')->get();
		
		$preeGroups = PeerGroup::orderBy('indexed', 'DESC')->get();
		
		
        return view('topics.list')->with([ "topics" => $topics, "users" => $users, "availabletopics" => $availabletopics, 'preeGroups' => $preeGroups]);
    }

    public function filteredTopics(Request $request)
    {
        $clause=[];
        if($request->has('search')) { 
            $searchTerm=$request->search;  
            $clause=$searchQueryFunction=function ($query) use ($searchTerm) {
                $query->where('title', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('details', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('feedback_type', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('available_topic', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('category_group', 'LIKE', '%' . $searchTerm . '%');
            };
        }
        
        $topics = Topic::with('comments')->whereHas('user', function ($query) {
            $query->where('user_type','!=','Admin');
        });

        if($request->filled('group')) {            
            //$topics->where('category_group', 'LIKE', '%'.$request->group.'%');
			$attributes = explode(',', $request->group);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('category_group', 'LIKE', '%'.$value.'%');
				}
			});
        }
		
        if($request->filled('mental_health')) {            
            //$topics->where('available_topic', 'LIKE', '%'.$request->mental_health.'%');
			$attributes = explode(',', $request->mental_health);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('available_topic', 'LIKE', '%'.$value.'%');
				}
			});
        }

        if($request->has('q')) { 
            if($request->q == 'audio' || $request->q == 'video') {
                $topics->where('content_type', 'LIKE', $request->q);
            }
        }

        // dd($topics->toSql());
    
        $topics = $topics->where($clause)->orderBy($request->sort_column,$request->sort_direction)->paginate(20);
        $users = User::where('id', '!=', 2)->get();
        $availabletopics = AvailableTopics::where('type', '=', 'teens')->get();
        return response()->json([                
            "ReturnCode" => 0,
            "ReturnMessage" => "Filtered list",
            "topics" => $topics,
            "pagination" =>(string) $topics->links()                
        ], 200);
    }

    public function view($id)
    {
        $topic = Topic::where(['id' => $id])->first();
		
        return view('topics.view')->with([ "topic" => $topic]);
    }
	
	public function commentDelete($id)
    {   
		$data = JournalComments::where(['id' => $id])->delete();
		
        return redirect()->back()->with('success', 'Comment delete successfully.');
    }

    public function delete($id)
    {   
        Topic::where(['id' => $id])->delete();
        return redirect('/topics')->with('success', 'Post delete successfully.');
    }

    public function store(Request $request)
    {
        $topic = new Topic;
        $topic->title = $request->title;

        if($request->has('image')) {
            $file = @$request->file('image');
            if($file) {
                $path = Storage::disk('s3')->put('topics', $file);
                $topic->image = $path;  
            }
        }

        $user = User::find($request->user_id);
        $type = 'adults';
        if($user) {
            $year = date("Y");
            $diff = $year - $user->birth_year;
            $type = ($diff < 18) ? "teens" : "adults"; 
        }

        $topic->details = $request->details;
        $topic->feedback_type = $request->feedback_type;
        $topic->available_topic = implode(',', $request->available_topic);
        $topic->user_id = $request->user_id;
        $topic->type = $type;
        // $topic->created_at =  $request->date;
        $topic->created_at =  now();
        $topic->save();
		
		$topic->groups()->attach($request->peer_groups, ['type' => 'post', 'status' => 1]);
        return redirect('/topics')->with('success', 'Post added successfully.');
    }

    public function update(Request $request)
    {
        $topic = Topic::find($request->id);
        $topic->title = $request->title;
        if($request->has('image')) {
            $file = @$request->file('image');
            if($file) {
                $path = Storage::disk('s3')->put('topics', $file);
                $topic->image = $path;  
            }
        }
        
        $user = User::find($request->user_id);
        $type = 'adults';
        if($user) {
            $year = date("Y");
            $diff = $year - $user->birth_year;
            $type = ($diff < 18) ? "teens" : "adults"; 
        }

        $topic->details = $request->details2;
        $topic->user_id = $request->user_id;
        $topic->feedback_type = $request->feedback_type;
        $topic->type = $type;
        if($request->filled('available_topic')) {
            $topic->available_topic = implode(',', $request->available_topic);
        }

        $topic->updated_at = date('Y-m-d H:i:s');
        $topic->save();
		
		$topic->groups()->sync($request->peer_groups, ['type' => 'post', 'status' => 1]);
        return redirect('/topics')->with('success', 'Post updated successfully.');
    }

    public function disable($id)
    {   
        Topic::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/topics')->with('success', 'Post disabled successfully.');
    }
    
    public function enable($id)
    {   
        Topic::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/topics')->with('success', 'Post enabled successfully.');
    }

    // 
    // 
    // 
    // 
    //  Available Topics
    // 
    // 
    // 
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function availableTopics()
    {
        $topics = AvailableTopics::orderBy('indexed','asc')->get();
        return view('available_topics.list')->with([ "topics" => $topics]);
    }

    public function sortbyIndex(Request $request){

        if($request->has('ids')){
            $arr = explode(',',$request->input('ids'));
            
            foreach($arr as $sortOrder => $id){
                $topic = AvailableTopics::find($id);
                $topic->indexed = $sortOrder;
                $topic->save();
            }
            return ['success'=>true,'message'=>'Updated'];
        }
    }

    public function availableTopicsDelete($id)
    {   
        AvailableTopics::where(['id' => $id])->delete();
        return redirect('/available_topics')->with('success', 'Category delete successfully.');
    }  

    public function availableTopicsAdd(Request $request)
    {      
        $count=AvailableTopics::count();      
        $topic = new AvailableTopics;
        $topic->title = $request->title;
        $topic->type = $request->type;
        $topic->indexed = $count+1;
        $topic->save();
        return redirect('/available_topics')->with('success', 'Category added successfully.');
    }

    public function availableTopicsUpdate(Request $request)
    {           
        $topic = AvailableTopics::find($request->id);
        $topic->title = $request->title;
        $topic->type = $request->type;       
        $topic->save();
        return redirect('/available_topics')->with('success', 'Category updated successfully.');
    }
    
    public function adminPostsList(Request $request)
    {
        
        $topics = Topic::with('comments')->whereHas('user', function ($query) {
            $query->where('user_type', 'Admin');
        })->orderBy('id','DESC');

        if($request->filled('group')) {            
            //$topics->where('category_group', 'LIKE', '%'.$request->group.'%');
			$attributes = explode(',', $request->group);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('category_group', 'LIKE', '%'.$value.'%');
				}
			});
        }
		
        if($request->filled('mental_health')) {            
            //$topics->where('available_topic', 'LIKE', '%'.$request->mental_health.'%');
			$attributes = explode(',', $request->mental_health);
			$topics->where(function ($query) use ($attributes) {
				foreach ($attributes as $value)
				{
					$query->orWhere('available_topic', 'LIKE', '%'.$value.'%');
				}
			});
        }

        if($request->has('q')) { 
            if($request->q == 'audio' || $request->q == 'video') {
                $topics->where('content_type', 'LIKE', $request->q);
            }
        }

        // dd($topics->toSql());
    
        $topics = $topics->get();
        $availabletopics = AvailableTopics::where('type', '=', 'teens')->get();
        $groups = CategoryGroup::orderBy('indexed','asc')->get();
        return view('admin-posts.list')->with([ "topics" => $topics, "availabletopics" => $availabletopics,"groups"=>$groups]);
    }
    public function createAdminPosts(Request $request)
    {          
        $topic = new Topic;
        $topic->title = $request->title;

        // if($request->has('image')) {
        //     $file = $request->file('image');
        //     $path = public_path('uploads/topic_files/');
        //     if(@$file) {
        //         $fileType = $file->getMimeType();
        //         $fileName = $file->getClientOriginalName();
        //         $fileExtension = $file->getClientOriginalExtension();
        //         $fileName = time().'.'. $fileExtension;
        //         $file->move($path, $fileName); 
        //         $topic->image = $fileName;      
        //     }
        // }
        if($request->has('file')) {
            $file = @$request->file('file');
            if($file) {
                $mimeType = $file->getClientMimeType();
                $path = Storage::disk('s3')->put('topics', $file);
                $topic->image = $path;  
                $topic->content_type = $request->content_type;
            }
        }
        $topic->details = $request->details;

        $topic->available_topic = implode(',', $request->available_topic);
        $topic->category_group = implode(',', $request->category_group);
        $topic->user_id = 2;
        $topic->type = $request->type;
        // $topic->created_at =  $request->date;
        $topic->created_at =  now();
        $topic->save();
        return redirect('/admin-posts')->with('success', 'Post added successfully.');
    }
    public function updateAdminPosts(Request $request)
    {          
        $topic = Topic::find($request->id);
        $topic->title = $request->title;
        // if($request->has('image')) {
        //     $file = $request->file('image');
        //     $path = public_path('uploads/topic_files/');
        //     if(@$file) {
        //         $fileType = $file->getMimeType();
        //         $fileName = $file->getClientOriginalName();
        //         $fileExtension = $file->getClientOriginalExtension();
        //         $fileName = time().'.'. $fileExtension;
        //         $file->move($path, $fileName); 
        //         $topic->image = $fileName;      
        //     }
        // }
        if($request->has('file')) {
            $file = @$request->file('file');
            if($file) {
                $mimeType = $file->getClientMimeType();
                $path = Storage::disk('s3')->put('topics', $file);
                $topic->image = $path;  
                $topic->content_type = $request->content_type;
            }
        }
        $topic->details = $request->details2;

        $topic->user_id = 2;
        $topic->type =  $request->type;
        if($request->filled('available_topic')) {
            $topic->available_topic = implode(',', $request->available_topic);
        }
        if($request->filled('category_group')){
            $topic->category_group = implode(',', $request->category_group);
        }

        $topic->updated_at = date('Y-m-d H:i:s');
        $topic->save();
        return redirect('/admin-posts')->with('success', 'Post updated successfully.');
    }

    public function disableAdminPosts($id)
    {   
        Topic::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/admin-posts')->with('success', 'Post disabled successfully.');
    }
    
    public function enableAdminPosts($id)
    {   
        Topic::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/admin-posts')->with('success', 'Post enabled successfully.');
    }

    public function viewAdminPosts($id)
    {
        $topic = Topic::where(['id' => $id])->first();
        return view('admin-posts.view')->with([ "topic" => $topic]);
    }

    public function deleteAdminPosts($id)
    {   
        Topic::where(['id' => $id])->delete();
        return redirect('/admin-posts')->with('success', 'Post delete successfully.');
    }

}
