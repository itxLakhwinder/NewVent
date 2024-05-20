<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;

class ResourceController extends Controller
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
        $resources = Resource::get();
        return view('resources.list')->with([ "resources" => $resources]);
    }

    public function view($id)
    {
        $user = User::where(['id' => $id])->first();
        return view('users.view')->with([ "user" => $user]);
    }

    public function delete($id)
    {   
        Resource::where(['id' => $id])->delete();
        return redirect('/resources')->with('success', 'Resource delete successfully.');
    }

    public function store(Request $request)
    {           
        $resource = new Resource;
        $resource->title = $request->title;
        $resource->description = $request->description;
        $resource->save();
        return redirect('/resources')->with('success', 'Resource added successfully.');
    }

    public function update(Request $request)
    {           
        $resource = Resource::find($request->id);
        $resource->title = $request->title;
        $resource->description = $request->description;
        $resource->save();
        return redirect('/resources')->with('success', 'Resource updated successfully.');
    }
}
