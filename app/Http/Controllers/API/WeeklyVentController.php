<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller; 
use App\Models\WeeklyVentTitle;
use App\Models\WeeklyVentAnswers;
use Validator;
use DB;


class WeeklyVentController extends Controller 
{

    public function GetWeeklyVentQuestions(){
    
        $current_date=date('Y-m-d');
        $data = WeeklyVentTitle::whereHas('questions',function ($query) {
            $query->where('status',0);
        })->with([
        'questions',
        'questions.answers'=> function ($query) {
                $query->where('user_id', Auth::user()->id );
        }])->whereDate('valid_from',"<=",$current_date)->whereDate('valid_to',">=",$current_date)->latest()
            ->first();
        if($data){
            $data->category_name=$data->category_id;
            $data->group_name=$data->group_id;
            unset($data->category_id);
            unset($data->group_id);
            $data=collect([$data]);
        }
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Weekly Vent Question.",
            "data" => $data
        ], 200);
    }
    public function submitWeeklyVentQuestions(Request $request) {
        try {

            $input = $request->all();
            if(count($input)) {


                foreach ($input as $key => $value) {
                    WeeklyVentAnswers::where(['question_id'=>$key,'user_id' => Auth::user()->id])->delete();
                    $ans = @explode(',', $value);
                    if(count($ans)){
                        foreach ($ans as $answer) {
                            WeeklyVentAnswers::insert(
                                ['question_id' => $key, 'user_id' => Auth::user()->id, 'answer' => $answer]
                            );
                        }
                    }
                }
            }

            return response()->json([
                "ReturnCode" => 1,
                "ReturnMessage" => "Answers saved.",
                "data" => $input
            ], 200);
        }
        //catch exception
        catch(Exception $e) {
            return null;
        }
   } 
}