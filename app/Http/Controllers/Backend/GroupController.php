<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\AvailableTopics;
use App\Models\CategoryGroup;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\File;


class GroupController extends Controller
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
        $groups = CategoryGroup::orderBy('indexed','asc')->get();
        return view('groups.list')->with([ "groups" => $groups]);
    }

    public function sortbyIndex(Request $request){

        if($request->has('ids')){
            $arr = explode(',',$request->input('ids'));
            
            foreach($arr as $sortOrder => $id){
                $group = CategoryGroup::find($id);
                $group->indexed = $sortOrder;
                $group->save();
            }
            return ['success'=>true,'message'=>'Updated'];
        }
    }

    public function view($id)
    {
        $topic = CategoryGroup::where(['id' => $id])->first();
        return view('groups.view')->with([ "topic" => $topic]);
    }

    public function delete($id)
    {   
        CategoryGroup::where(['id' => $id])->delete();
        return redirect('/groups')->with('success', 'Group delete successfully.');
    }

    public function store(Request $request)
    {
        $count = CategoryGroup::count();
        $topic = new CategoryGroup;
        $topic->name = $request->name;
        $topic->indexed = $count + 1;

		if ($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/group_files/');
            if (@$file) {
                $fileType = $file->getMimeType();
                $fileNameLogo = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameLogo = 'image' . time() . '.' . $fileExtension;
                $file->move($path, $fileNameLogo);
                $topic->image = env('APP_URL') . 'uploads/group_files/' . $fileNameLogo;
            }
        }
        
        $topic->save();
        return redirect('/groups')->with('success', 'Group added successfully.');
    }

    public function update(Request $request)
    {  
        $topic = CategoryGroup::find($request->id);
        $topic->name = $request->name;  
		if ($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/group_files/');
            if (@$file) {
                $fileType = $file->getMimeType();
                $fileNameLogo = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameLogo = 'image' . time() . '.' . $fileExtension;
                $file->move($path, $fileNameLogo);
                $topic->image = env('APP_URL') . 'uploads/group_files/' . $fileNameLogo;
            }
        }
        $topic->save();
        return redirect('/groups')->with('success', 'Group updated successfully.');
    }
    

}
