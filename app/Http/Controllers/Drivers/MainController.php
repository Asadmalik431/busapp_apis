<?php

namespace App\Http\Controllers\Drivers;

use Illuminate\Http\Request;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Illuminate\Support\Facades\Hash;
use App\Driver;
use App\Message;
use App\Http\Controllers\Controller;
use Storage;
class MainController extends Controller
{
    public function login(Request $request){
    	# code...
    	$email    = $request->email;
        $password = $request->password;
        //  echo $this->checkEmail($email);exit;
        if($this->checkEmail($email)){
            $parent   = Driver::where('email', $email)->first();
        }
        else{
            $parent   = Driver::where('phonenumber', $email)->first();
        }
        if ($parent) {
            $hashedPassword = $parent->password;
            if (Hash::check($password, $hashedPassword)) {
                 if ($parent) {
                    if ($parent['profile_image']) {
                        $parent['image_url'] = url('storage/app/driver_profile/'.$parent['profile_image'].''); 
                    }
                      
                }
                return response()->json([
                    'success' => "1",
                    'data' => $parent,

                ]);
            }
            else{
                return response()->json([
                    'success' => "0",
                    'success_message' => 'Authentication Failed'

                ]);
            }
        }
        else{
            return response()->json([
                    'success' => "0",
                    'success_message' => 'User Not Found'

                ]);
        }
    }
    function checkEmail($email) {

       $find1 = strpos($email, '@');
       $find2 = strpos($email, '.');
       return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }
    public function driver_detail(Request $request){
        
        $id       = $request->id;
    	$driver   = Driver::select('drivers.*','busses.bus_no as busNumber',
    				'busses.bus_model as busModel','route_models.location as routeName')
    				->leftJoin('driver_bus_relations', 'driver_bus_relations.driver_id', '=', 'drivers.id')
    				->leftJoin('route_models', 'route_models.id', '=', 'driver_bus_relations.route_id')
    				->leftJoin('busses', 'busses.id', '=', 'driver_bus_relations.bus_id')
    				->where('drivers.id', $id)
    				->first()
    				;
        if ($driver) {
            if ($driver['profile_image']) {
                $driver['image_url'] = url('storage/app/driver_profile/'.$driver['profile_image'].''); 
            }   
        }
        // echo "<pre>";
        // print_r($driver);
        // exit;
    	return  response()->json([
			'success' => "1",
			'data'    => $driver

		]);
	}
	
	public function driver_detail_byid(Request $request){

        $id       = $request->id;
    	$driver   = Driver::where('id', $id)->first();
    	return  response()->json([
				    'success' => "1",
 				    'data' => $driver,

				]);
    }

    public function update_driver_token_and_location(Request $request){
        
        $id     = $request->id;
        $driver = Driver::findOrFail($id);
        if($request->token_id != ""){
        $driver->token_id  = $request->token_id;}
        $driver->longitude  = $request->longitude;
        $driver->latitude  = $request->latitude;
        $driver->save();
    	return  response()->json([
                    'success' => "1",
				]);
    }

    public function update_messages(Request $request){ 
        $message                   = new Message;
        $message->from  = $request->from;
        $message->to  = $request->to;
        $message->message_body= $request->message_body;
        $message->save();
        return  response()->json([
            'success' => "1",
        ]);
    }

    public function get_messages(Request $request){ 
       $from       = $request->from;
       $to       = $request->to;
        $msg   = Message::where( 'from', $from)->where( 'to', $to)->get();
    	return  response()->json([
				    'success' => "1",
                    'data' => $msg,
				]);
    }

    public function message_detail(Request $request){ 
     $msg   = Message::select('from','to')->groupby('from','to')->get();
         return  response()->json([
                     'success' => "1",
                     'data' => $msg,
                 ]);
     }
}
