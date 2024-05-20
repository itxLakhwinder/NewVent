<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Support;
use App\Models\AvailableTopics;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage; 
use DB;

class SupportController extends Controller
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
        $topics = Support::orderBy('id','DESC')->get();
        $users = User::where('id', '!=', 2)->get();
        $availabletopics = AvailableTopics::where('type', '=', 'teens')->get();
        $categories = DB::table('categories')->get();
        return view('supports.list')->with([ "topics" => $topics, "users" => $users, "categories" => $categories]);
        
    }

    public function view($id)
    {
        $topic = Support::where(['id' => $id])->first();
        return view('supports.view')->with([ "topic" => $topic]);
    }

    public function delete($id)
    {   
        Support::where(['id' => $id])->delete();
        return redirect('/supports')->with('success', 'Support delete successfully.');
    }

    public function store(Request $request)
    {           
        $topic = new Support;
        $topic->title = $request->title;

        if($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/support_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $topic->image = $fileName;      
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
        $topic->post_type = 'post';
        // $topic->created_at =  $request->date;
        $topic->created_at =  now();
        $topic->save();
        return redirect('/supports')->with('success', 'Support added successfully.');
    }

    public function update(Request $request)
    {           
        $topic = Support::find($request->id);
        $topic->title = $request->title;
        if($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/support_files/');
            if(@$file) {
                $fileType = $file->getMimeType();
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time().'.'. $fileExtension;
                $file->move($path, $fileName); 
                $topic->image = $fileName;      
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
        return redirect('/supports')->with('success', 'Support updated successfully.');
    }

    public function disable($id)
    {   
        Support::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/supports')->with('success', 'Support disabled successfully.');
    }
    
    public function enable($id)
    {   
        Support::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/supports')->with('success', 'Support enabled successfully.');
    }

    public function advertisement()
    {   
        $ads = Advertisement::latest('id')->get();
        return view('advertisement.list')->with([ "ads" => $ads]);
    } 

    public function createAd()
    {   
        return view('advertisement.create');
    } 


    public function storeAd(Request $request){
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'link' => 'required|url',
            'logo' => 'image|max:2048',
            'image' => 'image|max:2048',         
        ]);

        $ad = new Advertisement;
        $ad->title = $request->title;

        if($request->has('image')) {
            $file = @$request->file('image');
            if($file) {
                $path = Storage::disk('s3')->put('advertisement', $file);
                $ad->image = $path;  
            }
        }
        if($request->has('logo')) {
            $file = @$request->file('logo');
            if($file) {
                $path = Storage::disk('s3')->put('advertisement', $file);
                $ad->logo = $path;  
            }
        }
        $ad->description = $request->description;
        $ad->link = $request->link;
        $ad->status = '1';
        $ad->save();
        return redirect('/advertisement')->with('success', 'Advertisement Created successfully.');
    }

    public function editAd($id){
        $ad = Advertisement::find($id);
        return view('advertisement.edit')->with([ "ad" => $ad]);
    }

    public function updateAd(Request $request){
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'link' => 'required|url',
            'logo' => 'image|max:2048',
            'image' => 'image|max:2048',         
        ]);
        $ad = Advertisement::find($request->id);
        $ad->title = $request->title;
        if($request->has('image')) {
            $file = @$request->file('image');
            if($file) {
                $path = Storage::disk('s3')->put('advertisement', $file);
                $ad->image = $path;  
            }
        }
        if($request->has('logo')) {
            $file = @$request->file('logo');
            if($file) {
                $path = Storage::disk('s3')->put('advertisement', $file);
                $ad->logo = $path;  
            }
        }
        $ad->description = $request->description;
        $ad->link = $request->link;
        $ad->save();
        return redirect('/advertisement')->with('success', 'Advertisement updated successfully.');
    }

    public function disableAd($id)
    {   
        Advertisement::where(['id' => $id])->update(['status' => '1' ]);
        return redirect('/advertisement')->with('success', 'Advertisement status enabled.');
    }
    
    public function enableAd($id)
    {   
        Advertisement::where(['id' => $id])->update(['status' => '0' ]);
        return redirect('/advertisement')->with('success', 'Advertisement status disabled.');
    }


    public function deleteAd($id)
    {   
        Advertisement::where(['id' => $id])->delete();
        return redirect('/advertisement')->with('success', 'Advertisement delete successfully.');
    }

    public function viewAd($id){
        $ad = Advertisement::where(['id' => $id])->first();
        return view('advertisement.view')->with([ "ad" => $ad]);

    }
  

}
