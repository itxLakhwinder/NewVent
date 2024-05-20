<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\AvailableTopics;
use App\Models\JustMentalPodcastCategory;
use Illuminate\Support\Facades\Auth; 
use DB;

class CategoryController extends Controller
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
    public function index()
    {
        $topics = DB::table('categories')->orderBy('id','desc')->get();
        return view('categories.list')->with([ "topics" => $topics]);
    }

    public function delete($id)
    {   
        DB::table('categories')->where(['id' => $id])->delete();
        return redirect(url('categories'))->with('success', 'Category delete successfully.');
    }  

    public function store(Request $request)
    {
        DB::table('categories')->insert(
            ['name' => $request->name]
        );
        return redirect(url('categories'))->with('success', 'Category added successfully.');
    }

    public function update(Request $request)
    {           
        $affected = DB::table('categories')
              ->where('id', $request->id)
              ->update(['name' => $request->name]);
        return redirect(url('categories'))->with('success', 'Category updated successfully.');
    }


    // **************** just mental podcast categories *********************** //
    public function podcastCategoryindex()
    {
        $topics = DB::table('justmental_categories')->orderBy('indexed','asc')->get();
        return view('justmental_categories.list')->with([ "topics" => $topics]);
    } 

    public function sortbyIndex(Request $request){

        if($request->has('ids')){
            $arr = explode(',',$request->input('ids'));
            
            foreach($arr as $sortOrder => $id){
                $topic = JustMentalPodcastCategory::find($id);
                $topic->indexed = $sortOrder;
                $topic->save();
            }
            return ['success'=>true,'message'=>'Updated'];
        }
    }

    public function podcastCategorydelete($id)
    {   
        DB::table('justmental_categories')->where(['id' => $id])->delete();
        return redirect(url('justmental_categories'))->with('success', 'Just Mental Podcast Category delete successfully.');
    }  

    public function podcastCategorystore(Request $request)
    {
        $topics = DB::table('justmental_categories')->get();
        $count=count($topics)+1;
        DB::table('justmental_categories')->insert(
            ['name' => $request->name,'indexed'=>$count]
        );
        return redirect(url('justmental_categories'))->with('success', 'Just Mental Podcast Category added successfully.');
    }

    public function podcastCategoryupdate(Request $request)
    {           
        $affected = DB::table('justmental_categories')
              ->where('id', $request->id)
              ->update(['name' => $request->name]);
        return redirect(url('justmental_categories'))->with('success', 'Just Mental Podcast Category updated successfully.');
    }




    // ************************************* //

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function mIndex()
    {
        $topics = DB::table('mental_health')->get();
        $categories = DB::table('categories')->get();
        return view('mentalhealth.list')->with([ "topics" => $topics, "categories" => $categories]);
    }

    public function mDelete($id)
    {   
        DB::table('mental_health')->where(['id' => $id])->delete();
        return redirect(url('mentalhealth'))->with('success', 'Mental Health Education delete successfully.');
    }  

    public function mStore(Request $request)
    {
        DB::table('mental_health')->insert([
                'category_id' => $request->category_id,
                'code' => $request->code,
                'video_title' => $request->video_title,
                'audience' => $request->audience,
                'sponsor' => $request->sponsor,
                'status' => $request->status,
                'hyperlink' => $request->hyperlink,
                'embed_code' => $request->embed_code]
            );
        return redirect(url('mentalhealth'))->with('success', 'Mental Health Education added successfully.');
    }

    public function mUpdate(Request $request)
    {           
        $affected = DB::table('mental_health')
              ->where('id', $request->id)
              ->update([
                    'category_id' => $request->category_id,
                    'code' => $request->code,
                    'video_title' => $request->video_title,
                    'audience' => $request->audience,
                    'sponsor' => $request->sponsor,
                    'status' => $request->status,
                    'hyperlink' => $request->hyperlink,
                    'embed_code' => $request->embed_code
                ]);
        return redirect(url('mentalhealth'))->with('success', 'Mental Health Education updated successfully.');
    }
    //justmental podcast
    public function podcastIndex()
    {
        $topics = DB::table('justmental_podcast')->get();
        $categories = DB::table('justmental_categories')->get();
        return view('justmental_podcast.list')->with([ "topics" => $topics, "categories" => $categories]);
    }

    public function podcastDelete($id)
    {   
        DB::table('justmental_podcast')->where(['id' => $id])->delete();
        return redirect(url('justmental_podcast'))->with('success', 'Just Mental Podcast updated delete successfully.');
    }  

    public function podcastStore(Request $request)
    {
        DB::table('justmental_podcast')->insert([
                'category_id' => $request->category_id,
                'video_title' => $request->video_title,
                'status' => $request->status,
                'hyperlink' => $request->hyperlink
                ]
            );
        return redirect(url('justmental_podcast'))->with('success', 'Just Mental Podcast updated added successfully.');
    }

    public function podcastUpdate(Request $request)
    {         
        $affected = DB::table('justmental_podcast')
              ->where('id', $request->id)
              ->update([
                'category_id' => $request->category_id,
                'video_title' => $request->video_title,
                'status' => $request->status,
                'hyperlink' => $request->hyperlink
                ]);
        return redirect(url('justmental_podcast'))->with('success', 'Just Mental Podcast updated successfully.');
    }


}
