<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AvailableTopics;
use App\Models\CategoryGroup;
use App\Models\WeeklyVent;
use App\Models\WeeklyVentAnswers;
use App\User; 
use App\Models\WeeklyVentTitle;
use Illuminate\Http\Request;

class WeeklyVentController extends Controller
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

     //Titles
    public function indexTitles()
    {
        $weekly_vent_titles = WeeklyVentTitle::get();
        $categories = AvailableTopics::where('type', '=', 'teens')->orderBy('indexed','asc')->get();
        $groups = CategoryGroup::orderBy('indexed','asc')->get();
        return view('weekly_vent.titles')->with([ "weekly_vent_titles" => $weekly_vent_titles, "categories" =>$categories, "groups" => $groups]);
    }
    public function deleteTitle($id)
    {   
        WeeklyVentTitle::where(['id' => $id])->delete();
        WeeklyVent::where(['title_id' => $id])->delete();
        return redirect('/weekly-vent-titles')->with('success', 'Weekly Vent Title deleted successfully.');
    }
    public function storeTitle(Request $request)
    {           
        $weeklyTitle = new WeeklyVentTitle;
        $weeklyTitle->type = $request->type;
        $weeklyTitle->category_id = implode(',', $request->category_id);
        $weeklyTitle->group_id =implode(',', $request->group_id); 
        $weeklyTitle->title = $request->title;
        $weeklyTitle->description = $request->description;
        $weeklyTitle->valid_from = $request->valid_from;
        $weeklyTitle->valid_to = $request->valid_to;
        $weeklyTitle->save();
        return redirect('/weekly-vent-titles')->with('success', 'Weekly Vent Title added successfully.');
    }

    public function updateTitle(Request $request)
    {          
        $weeklyTitle = WeeklyVentTitle::find($request->id);
        $weeklyTitle->type = $request->type;
        $weeklyTitle->category_id = implode(',', $request->category_id);
        $weeklyTitle->group_id =implode(',', $request->group_id);
        $weeklyTitle->title = $request->title;
        $weeklyTitle->description = $request->description;
        $weeklyTitle->valid_from = $request->valid_from;
        $weeklyTitle->valid_to = $request->valid_to;
        $weeklyTitle->save();
        return redirect('/weekly-vent-titles')->with('success', 'Weekly Vent Title updated successfully.');
    }

    //Questions
    public function indexQuestions()
    {
        $weekly_vent_questions = WeeklyVent::with('title')->get();
        $titles = WeeklyVentTitle::get();
        return view('weekly_vent.list')->with([ "weekly_vent_questions" => $weekly_vent_questions,'titles'=>$titles]);
    }

    public function deleteQuestion($id)
    {   
        WeeklyVent::where(['id' => $id])->delete();
        return redirect('/weekly-vent-questions')->with('success', 'Weekly Vent Question deleted successfully.');
    }


    public function storeQuestion(Request $request)
    {           
        $weeklyQuestion = new WeeklyVent;   
        $weeklyQuestion->title_id = $request->title_id;
        $weeklyQuestion->question = $request->question;
        $weeklyQuestion->is_multiselect = $request->is_multiselect;
        $weeklyQuestion->options = serialize($request->options);
        $weeklyQuestion->save();
        return redirect('/weekly-vent-questions')->with('success', 'Weekly Vent Question added successfully.');
    }

    public function updateQuestion(Request $request)
    {           
        $weeklyQuestion = WeeklyVent::find($request->id);
        $weeklyQuestion->title_id = $request->title_id;
        $weeklyQuestion->question = $request->question;
        $weeklyQuestion->is_multiselect = $request->is_multiselect;
        $weeklyQuestion->options = serialize($request->options);
        $weeklyQuestion->save();
        return redirect('/weekly-vent-questions')->with('success', 'Weekly Vent Question updated successfully.');
    }
    public function disableQuestion($id)
    {   
        WeeklyVent::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/weekly-vent-questions')->with('success', 'Weekly Vent Question disabled successfully.');
    }
    
    public function enableQuestion($id)
    {   
        WeeklyVent::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/weekly-vent-questions')->with('success', 'Weekly Vent Question enabled successfully.');
    }
    public function SubmittedAnswerUsersList()
    {   
        $answersList=[];
        $data = User::whereHas("weekly_vent_answers")->get();
        foreach($data as $dataitem){
            $last_submission_date=WeeklyVentAnswers::where("user_id",$dataitem->id)->latest()->first();
            $dataitem->last_submitted=$last_submission_date['created_at'];
            $answersList[]=$dataitem;
        }        
        return view('weekly_vent.answers.list')->with([ "answers_list" => $answersList]);
    }
    public function SubmittedAnswerView($id)
    {   
        $userId=$id;
        $answers=[];
        $data = WeeklyVentTitle::whereHas('questions.answers')->with(['questions','questions.answers'=> function ($query) use ($userId){
            $query->where(['user_id'=> $userId]);
        }])->latest()
        ->get();
        foreach($data as $dataitem){
            $dataitem->category_name=$dataitem->category_id;
            $dataitem->group_name=$dataitem->group_id;
            $answers[]=$dataitem;
            unset($dataitem->category_id);
            unset($dataitem->group_id);
        }  
        return view('weekly_vent.answers.view')->with([ "answers" => $answers]);
    }
   
}
