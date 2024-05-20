<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\UserAnswer; 
use App\Models\JournalComments;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Redirect;
use Carbon\CarbonInterval;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }
	
		
    public function share($id)
    {
        $topic = Topic::where(['id' => $id])->first();
		
		$useragent=$_SERVER['HTTP_USER_AGENT'];
		
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {

			//Detect special conditions devices
			$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
			$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
			$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

			//do something with this information
			if( $iPod || $iPhone ){
			    return Redirect::to('https://apps.apple.com/us/app/ventspace/id1514627232');
			}else if($iPad){
			    return Redirect::to('https://apps.apple.com/us/app/ventspace/id1514627232');
			}else if($Android){
			    return Redirect::to('https://play.google.com/store/apps/details?id=com.application.ventspace');
			}

		}



        return view('topics.share')->with([ "topic" => $topic]);
    }
	

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('user_type', '=', 'User')->get();

        $arr = [];
        foreach ($users as $key => $user) {
            if( $user->birth_year && $user->birth_year > 0) {
                $int = (int) filter_var($user->birth_year, FILTER_SANITIZE_NUMBER_INT);  
				$num_length = strlen((string)$int);
				if($num_length == 4) {					
					$age = date('Y') - $int;
					if($age >= 0) {
						array_push($arr, $age);
					}
				}
            }
        }
        $averageage = 0;
		
        if(count($arr)) {
            $average = array_sum($arr) / count($arr);
            $averageage = ceil($average);
        }
        //Our array, which contains a set of numbers.
        $topics = Topic::with('comments')->whereHas('user', function ($query) {
            $query->where('user_type','!=','Admin');
        })->where('post_type', '!=', 'story')->orderBy('id','DESC')->get();
        $answers = User::where('user_type', '=', 'User')->whereHas('answers')->get();
	
        $dt = date('Y-m-d');

        $todaytopics = Topic::where('created_at','like', '%'.$dt.'%')->count();
		
		$comments = JournalComments::whereNotNull('topic_id')->count();

		$firstday = date('Y-m-d', strtotime("this week"));
		$lastday = date('Y-m-d', strtotime("sunday this week"));
		
		$mon_start =  strtotime(date('Y-m-01'));
   		$end_start =  strtotime(date('Y-m-t'));
		
		$weeks = [];
		$month = [];
		foreach($topics as $topic) {
			$created = date('Y-m-d', strtotime($topic->created_at)); 			
			if(strtotime($created) >= strtotime($firstday) && strtotime($created) <= strtotime($lastday)) {				
				array_push($weeks, $topic);
			}
			
			if(strtotime($topic->created_at) >= $mon_start && strtotime($topic->created_at) <= $end_start) {
				array_push($month, $topic);
			}
		}
		
		$text = $topics->whereNull('content_type')->count();
		$video = $topics->where('content_type', 'video')->count();
		$audio = $topics->where('content_type', 'audio')->count();
		$logs = UserLoginLog::groupBy('user_id')->get()->count();
		$logs_time = UserLoginLog::sum('tim_diff');
		$tot_log_avg = 0;
		if($logs > 0) {
			$tot_log_avg_minutes = round($logs_time / $logs);
			$interval = CarbonInterval::minutes($tot_log_avg_minutes);
			// Check if the average is greater than or equal to 1 hour
			$formatted_average = $interval->cascade()->forHumans();
		
			$tot_log_avg = $formatted_average;
		}

        return view('home', [
            'users' => count($users),
            'averageage' => $averageage,
            'topics' => count($topics),
            'todaytopics' => $todaytopics,
			'comments' => $comments,
			'weeks' => count($weeks),
			'month' => count($month),
            'answers' => count($answers),
			'text' => $text,
			'video' => $video,
			'audio' => $audio,
			'tot_log_avg' => $tot_log_avg,
        ]);
    }
}
