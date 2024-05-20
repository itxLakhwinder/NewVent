<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use App\Classes\Jwt;

trait GroupTrait
{
	public function getUserStatus($users)
	{
		$user = $users->where('id', auth()->user()->id)->first();
		
		if($user){
			return 1;
		}else{
			return 0;
		}
	}

    public function getMuteStatus($users){
		$user=$users->where('id', auth()->user()->id)->first();
        if($user){
		  return $user->pivot->status;
		}else{
			return 0;
		}
	
	}

	
	public function getPost($topics)
	{
		return $topics->filter(function ($topic) {
			return $topic->post_type !== 'story';
		});
	}

	public function getStory($topics)
	{
		return $topics->filter(function ($topic) {
			return $topic->post_type === 'story';
		});
	}
	
	public function sendPush($deviceToken, $deviceType, $message, $type = "", $id = 0) {

        try {

            Log::channel('pushnotifications')->info($id.'>>>>'.$type.'>>>>'.$message);
            $API_ACCESS_KEY = env('FCM_KEY');

            $registrationIds = $deviceToken;
            #prep the bundle
            $msg = array
            (
                'body'  => $message,
                'title' => 'Ventspace',
               // 'icon'  => 'myicon',
              //  'sound' => 'mySound',
				'id' => $id,
             	'type' => $type
            );

            $fields = array
            (
                'to' => $registrationIds,
                'notification' => $msg,
				'data' => $msg,
            );

           
            $headers = array
            (
                'Authorization: key='.$API_ACCESS_KEY,
                'Content-Type: application/json'
            );
            #Send Reponse To FireBase Server  
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result_log = curl_exec($ch);
            if ($result_log === FALSE) {
                return curl_error($ch);
            }
            curl_close($ch);
            Log::channel('pushnotifications')->info($result_log);
            return $result_log;
            
        }
        //catch exception
        catch(Exception $e) {
            return null;
        }
        
    }
}
