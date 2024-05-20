<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\AvailableTopics;
use App\Models\PeerGroup;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\File;


class PeerGroupController extends Controller
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
        $groups = PeerGroup::orderBy('indexed','asc')->get();
        return view('peergroups.list')->with([ "groups" => $groups]);
    }

    public function sortbyIndex(Request $request){

        if($request->has('ids')){
            $arr = explode(',',$request->input('ids'));
            
            foreach($arr as $sortOrder => $id){
                $group = PeerGroup::find($id);
                $group->indexed = $sortOrder;
                $group->save();
            }
            return ['success'=>true,'message'=>'Updated'];
        }
    }

    public function delete($id)
    {   
        PeerGroup::where(['id' => $id])->delete();
        return redirect('/peer-groups')->with('success', 'Peer group delete successfully.');
    }

    public function store(Request $request)
    {
        $count = PeerGroup::count();
        $peerGroup = new PeerGroup;
        $peerGroup->name = $request->name;
		$peerGroup->description = $request->description;
        $peerGroup->indexed = $count + 1;

		if ($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/group_files/');
            if (@$file) {
                $fileType = $file->getMimeType();
                $fileNameLogo = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameLogo = 'image' . time() . '.' . $fileExtension;
                $file->move($path, $fileNameLogo);
                $peerGroup->image = env('APP_URL') . 'uploads/group_files/' . $fileNameLogo;
            }
        }
        
        $peerGroup->save();
        return redirect('/peer-groups')->with('success', 'Peer group added successfully.');
    }

    public function update(Request $request)
    {  
        $peerGroup = PeerGroup::find($request->id);
        $peerGroup->name = $request->name;
		$peerGroup->description = $request->description;
		if ($request->has('image')) {
            $file = $request->file('image');
            $path = public_path('uploads/group_files/');
            if (@$file) {
                $fileType = $file->getMimeType();
                $fileNameLogo = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameLogo = 'image' . time() . '.' . $fileExtension;
                $file->move($path, $fileNameLogo);
                $peerGroup->image = env('APP_URL') . 'uploads/group_files/' . $fileNameLogo;
            }
        }
        $peerGroup->save();
        return redirect('/peer-groups')->with('success', 'Peer group updated successfully.');
    }
    

}
