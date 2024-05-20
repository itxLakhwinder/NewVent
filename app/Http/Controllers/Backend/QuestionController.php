<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\User;
use App\Models\Question;
use App\Models\Resource;

class QuestionController extends Controller
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
        $questions = Question::get();
        return view('questions.list')->with([ "questions" => $questions]);
    }

    public function view($id)
    {
        $user = User::where(['id' => $id])->first();
        return view('users.view')->with([ "user" => $user]);
    }

    public function delete($id)
    {   
        Question::where(['id' => $id])->delete();
        return redirect('/questions')->with('success', 'Question delete successfully.');
    }

    public function store(Request $request)
    {           
        $resource = new Question;
        $resource->question = $request->question;
        $resource->is_multiselect = $request->is_multiselect;
        $resource->options = serialize($request->options);
        $resource->save();
        return redirect('/questions')->with('success', 'Question added successfully.');
    }

    public function update(Request $request)
    {           
        $resource = Question::find($request->id);
        $resource->question = $request->question;
        $resource->is_multiselect = $request->is_multiselect;
        $resource->options = serialize($request->options);
        $resource->save();
        return redirect('/questions')->with('success', 'Question updated successfully.');
    }

    public function disable($id)
    {   
        Question::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/questions')->with('success', 'Question disabled successfully.');
    }
    
    public function enable($id)
    {   
        Question::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/questions')->with('success', 'Question enabled successfully.');
    }
}
