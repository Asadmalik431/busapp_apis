<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications;
use GuzzleHttp\Client;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class NotificationController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }
    public function startTripNotification(Request $request){

        $notification              = new Notifications;
		$notification->sender_id   = $request->driver_id;
		$notification->type        = $request->type;
		$notification->body        = $request->message;
		$notification->save();
        $res['notification_count'] = Notifications::where('status' ,'0')->count();
        $notification              = Notifications::select('notifications.*',
        							 'drivers.name')
        							 ->where('reader_id' , null)
        						     ->leftJoin('drivers', 'drivers.id', '=', 'notifications.sender_id')
        						     ->orderby('notifications.id','desc')
        							 ->limit(3)->get();
        $res['notification_cntxt'] = json_encode($notification);
        $response = postGuzzleRequest('get_notification',$res);
        echo "Notification Send";
        return $response;

    }

    public function get_all_notifications(){
    	
    	$res['notification_count'] = Notifications::where('status' ,'0')->count();
        $notification              = Notifications::select('notifications.*',
        							 'drivers.name')
        							 ->where('reader_id' , null)
        						     ->leftJoin('drivers', 'drivers.id', '=', 'notifications.sender_id')
        						     ->orderby('notifications.id','desc')
        							 ->limit(3)->get();
        $res['notification_cntxt'] = json_encode($notification);
        return $res;
    }
    public function clear_notification(Request $request){

    	Notifications::where('status' ,'0')->update(['status' => '1']);
    	$res['notification_count'] = Notifications::where('status' ,'0')->count();
    	return $res;
    }

    public function read_notification(Request $request){

    	$id         = $request->notyId;
    	$login_user = $request->login_user;
    	Notifications::where('id' , $id)->update(['reader_id' => $login_user,
    	'status' => '1']);
    	$res['notification_count'] = Notifications::where('status' ,'0')->count();
        $notification              = Notifications::select('notifications.*',
        							 'drivers.name')
        							 ->where('reader_id' , null)
        						     ->leftJoin('drivers', 'drivers.id', '=', 'notifications.sender_id')
        						     ->orderby('notifications.id','desc')
        							 ->limit(3)->get();
        $res['notification_cntxt'] = json_encode($notification);
        return $res;
    }

    public function get_all_notifications_with_status(){
    	
    	$_notifications        = Notifications::select('notifications.*',
        						 'drivers.name')
        						 ->leftJoin('drivers', 'drivers.id', '=', 
        						 'notifications.sender_id')
        						 ->orderby('notifications.id','desc')
        					     ->get();

        $res['_notifications'] = json_encode($_notifications);
        return $res;
    }
    public function send_Alert_to_app(){


    	$optionBuilder       = new OptionsBuilder();
		// $optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder('Hello ahmed');
		$notificationBuilder->setBody('Arslan Starts His Trip')
						    ->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option       = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data         = $dataBuilder->build();

		$token = "ezD2l7ik5M0:APA91bFaL4usdu5ifq_CATO3XI3A1DcCVdL8cU2e_qjHMHmk6MaQfvZJDZgmL35xgFZrMyqdQ4W9htcslt7scvTGrOAW94QBFMWhhUlE7-x7w3nFrj1laCDfMkVipwpfvfzMIz0RFab_";
        
		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

		return $downstreamResponse->numberSuccess();
    }
}
