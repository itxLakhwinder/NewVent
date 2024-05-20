<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\AvailableTopics;
use App\Models\Location;
use Illuminate\Support\Facades\Auth; 
use DB;

class LocationController extends Controller
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
        $topics = DB::table('locations')->get();
        return view('locations.list')->with([ "topics" => $topics]);
    }

    public function delete($id)
    {   
        DB::table('locations')->where(['id' => $id])->delete();
        return redirect(url('locations'))->with('success', 'Location delete successfully.');
    }  

    public function store(Request $request)
    {
        DB::table('locations')->insert(
            ['name' => $request->name]
        );
        return redirect(url('locations'))->with('success', 'Location added successfully.');
    }

    public function update(Request $request)
    {           
        $affected = DB::table('locations')
              ->where('id', $request->id)
              ->update(['name' => $request->name]);
              
        return redirect(url('locations'))->with('success', 'Location updated successfully.');
    }


}
