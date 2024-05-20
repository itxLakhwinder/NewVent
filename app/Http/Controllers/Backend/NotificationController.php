<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth; 

class NotificationController extends Controller
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
        $notifications = Notification::get();
        $users = User::where('id', '<>', '2')->select('id', 'name', 'email')->get();
        return view('notifications.list')->with([ "notifications" => $notifications, "users" => $users]);
    }

    public function view($id)
    {
        $user = User::where(['id' => $id])->first();
        return view('users.view')->with([ "user" => $user]);
    }

    public function delete($id)
    {   
        Notification::where(['id' => $id])->delete();
        return redirect('/notifications')->with('success', 'Notification delete successfully.');
    }

    public function store(Request $request)
    {           
        $resource = new Notification;
        $resource->title = $request->title;
        $resource->message = $request->message;
        $resource->from_user = Auth::user()->id;
        $resource->to_user = $request->to_user;
        $resource->created_at = date('Y-m-d H:i:s');
        $resource->save();
        return redirect('/notifications')->with('success', 'Notification added successfully.');
    }

    public function update(Request $request)
    {           
        $resource = Notification::find($request->id);
        $resource->title = $request->title;
        $resource->message = $request->description;
        $resource->updated_at = date('Y-m-d H:i:s');
        $resource->save();
        return redirect('/notifications')->with('success', 'Notification updated successfully.');
    }
}
