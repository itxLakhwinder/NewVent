<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\User;
use App\Models\Question; 
use App\Models\UserAnswer; 
use App\Models\UserLoginLog;
use App\Models\Report; 

class UserController extends Controller
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
        if($request->has('q')) { 
            if($request->q == 'qfilled') {
                $users = User::where('user_type', '=', 'User')->select('id','name','email','phone_number','status','address')->whereHas('answers')->withCount('answers')->paginate(20);
            }
			
        }else{
            $users = User::where('user_type', '=', 'User')->select('id','name','email','phone_number','status','address')->withCount('answers')->paginate(20);
			
			
        }
     
		foreach($users as $k => $user) {
			$logs = UserLoginLog::where('user_id', '=' ,$user['id'])->get();	
			$time_sum = 0;
			if(count($logs)) {
				foreach($logs as $log) {
					$time_sum = $time_sum + $log->tim_diff;
				}
				$users[$k]['time'] = round($time_sum / count($logs));
			} else {
				$users[$k]['time'] = 0;
			}
		} 
		
			
        return view('users.list')->with([ "users" => $users]);
    }
    public function FilterUsers(Request $request)
    {
        
        $clause=[];
        if($request->has('search')) { 
            $searchTerm=$request->search;  
            $clause=$searchQueryFunction=function ($query) use ($searchTerm) {
                   $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('phone_number', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('address', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('status', 'LIKE', '%' . $searchTerm . '%');
            };
        }
        if($request->q == 'qfilled') {
                $users = User::where('user_type', '=', 'User')->select('id','name','email','phone_number','status','address')->whereHas('answers')->where($clause)->orderBy($request->sort_column,$request->sort_direction)->withCount('answers')->paginate(20);
        }else{
            $users = User::where('user_type', '=', 'User')->select('id','name','email','phone_number','status','address')->where($clause)->withCount('answers')->orderBy($request->sort_column,$request->sort_direction)->paginate(20);
        }
     
		foreach($users as $k => $user) {
			$logs = UserLoginLog::where('user_id', '=' ,$user['id'])->get();	
			$time_sum = 0;
			if(count($logs)) {
				foreach($logs as $log) {
					$time_sum = $time_sum + $log->tim_diff;
				}
				$users[$k]['time'] = round($time_sum / count($logs));
			} else {
				$users[$k]['time'] = 0;
			}
		}   
			
        return view('users.filteredlist')->with([ "users" => $users]);
    }

    public function view($id)
    {
        $user = User::where(['id' => $id])->first();

        $questions = Question::where('status', '=', 0)->with(['answer' => function($query) use ($id) {
            $query->where('user_id', '=', $id)->select('answer', 'question_id');
        }])->select('id','question')->get(); 
       
        return view('users.view')->with([ "user" => $user, "questions" => $questions]);
    }

    public function disable($id)
    {   
        User::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/users')->with('success', 'User disabled successfully.');
    }
    
    public function enable($id)
    {   
        User::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/users')->with('success', 'User enabled successfully.');
    }

    public function delete($id)
    {   
        User::where(['id' => $id])->delete();
        return redirect('/users')->with('success', 'User delete successfully.');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reports()
    {
        $reports = Report::orderBy('id', 'desc')->with(["topic","user", "group"])->get();
		
        return view('reports.list')->with([ "reports" => $reports]);
    }
}
