<?php

namespace App\Http\Controllers\Parents;

use Illuminate\Http\Request;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Illuminate\Support\Facades\Hash;
use App\ParentsTable;
use App\Children;
use App\childBus;
use App\Driver;
use App\tblStation;
use App\Http\Controllers\Controller;
use Mail;
class MainController extends Controller
{
   //sdkfkds ahmed
    public function parent_login(Request $request){
    	# code...
    	$email    = $request->email;
    	$password = $request->password;
    // 	echo $this->checkEmail($email);exit;
    	if($this->checkEmail($email)){
    	    $parent   = ParentsTable::where('email', $email)->first();
    	}
    	else{
    	    $parent   = ParentsTable::where('contact', $email)->first();
    	}
    	
    	if ($parent) {
    		$hashedPassword = $parent->password;
			if (Hash::check($password, $hashedPassword)) {
                if ($parent) {
                    if ($parent['profile_image']) {
                        $parent['image_url'] = url('storage/app/parent_profile/'.$parent['profile_image'].''); 
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
    public function myChilds(Request $request){

    	$parent_id = $request->id;
    	$mychild   = Children::where('parent_id', $parent_id)->get();
        foreach ($mychild as $key => $value) {
            if ($value) {
                if ($value['profile_image']) {
                    $mychild[$key]['image_url'] = url('storage/app/child_profile/'.$value['profile_image'].''); 
                }
                else{

                  $mychild[$key]['image_url']  = '';
                }
            }
        }
    	return  response()->json([
				    'success' => "1",
				    'data' => $mychild,

				]);
    }
    public function childDetail(Request $request){

    	$child_id  = $request->id;
    	$mychild   = Children::select('childrens.*','parents_tables.name as parent_name','parents_tables.contact as parent_phone_number')->where('childrens.id', $child_id)->join('parents_tables', 'parents_tables.id', '=', 'childrens.parent_id')->first();
        if ($mychild) {
            if ($mychild['profile_image']) {
                $mychild['image_url'] = url('storage/app/child_profile/'.$mychild['profile_image'].''); 
            }
              
        }
    	return  response()->json([
				    'success' => "1",
				    'data' => $mychild,

				]);
    }
    public function childlistLocation(Request $request){

        $id        = $request->id; 
        $mychild   = Children::select('childrens.*','tbl_stations.latitude as stop_latitude','tbl_stations.longitude as stop_longitude','tbl_stations.name as stop_name')
                     ->leftJoin('tbl_stations', 'tbl_stations.id', '=', 'childrens.child_stop')
                     ->where('parent_id', $id)->get();
               foreach ($mychild as $key => $value) {
            if ($value) {
                if ($value['profile_image']) {
                    $mychild[$key]['image_url'] = url('storage/app/child_profile/'.$value['profile_image'].''); 
                }
                else{

                  $mychild[$key]['image_url']  = '';
                }
            }
        }      
        return  response()->json([
            'success' => '1',
            'data'    => $mychild,

        ]);
    }

    public function child_all_detail(Request $request){

        $id        = $request->id; 
        $mychild   = childBus::select('child_buses.*','driver_bus_relations.route_id',
                    'driver_bus_relations.driver_id','busses.bus_no',
                    'busses.bus_model','drivers.name as driver_name',
                    'childrens.name as child_name','childrens.child_stop',
                    'tbl_stations.name as child_bus_stop',
                    'tbl_stations.latitude as child_bus_latitude',
                    'tbl_stations.longitude as child_bus_longitude')
                     ->leftJoin('driver_bus_relations', 'driver_bus_relations.id', '=', 'child_buses.rel_id')
                     ->leftJoin('busses', 'busses.id', '=', 'child_buses.bus_id')
                     ->leftJoin('drivers', 'drivers.id', '=', 'driver_bus_relations.driver_id')
                     ->leftJoin('childrens', 'childrens.id', '=', 'child_buses.child_id')
                     ->leftJoin('tbl_stations', 'tbl_stations.id', '=', 'childrens.child_stop')
                     ->where('child_buses.child_id', $id)->first();
        // $mychild['bus_stops'] = tblStation::select('name','latitude','longitude')
        //                         ->where('route_id',$mychild->route_id)
        //                         ->get();
        return  response()->json([
            'success' => '1',
            'data'    => $mychild,

        ]);
    }
    
    public function child_bus_stops(Request $request){
        
        $id   = $request->id; 
        $data = childBus::select('tbl_stations.name',
                'tbl_stations.latitude as lat',
                'tbl_stations.longitude as lang')
                ->leftJoin('driver_bus_relations', 'driver_bus_relations.id', '=', 'child_buses.rel_id')
                ->leftJoin('tbl_stations', 'tbl_stations.route_id', '=', 'driver_bus_relations.route_id')
                ->where('child_buses.child_id', $id)
                ->get();
        return  response()->json([
            'success' => '1',
            'data'    => $data,

        ]);
    }

    public function child_location(Request $request){
        $childid = $request->childid;
        $data = childBus::select('drivers.longitude','drivers.latitude')
        ->leftJoin('driver_bus_relations', 'driver_bus_relations.id', '=', 'child_buses.rel_id')
        ->leftJoin('drivers', 'drivers.id', '=', 'driver_bus_relations.driver_id')
        ->where('child_buses.child_id', $childid)
        ->get();
        return  response()->json([
            'success' => '1',
            'data'    => $data,

        ]);
    }


    public function checkEmail($email) {
       $find1 = strpos($email, '@');
       $find2 = strpos($email, '.');
       return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }

    public function parent_detail(Request $request){
        $id       = $request->id;
    	$parent   = ParentsTable::where('id', $id)->first();
        if ($parent) {
            if ($parent['profile_image']) {
                $parent['image_url'] = url('storage/app/parent_profile/'.$parent['profile_image'].''); 
            }
              
        }
    	return  response()->json([
				    'success' => "1",
 				    'data' => $parent,

				]);
    }
    public function sendEmail()
    {
        $data['title'] = "This is Test Mail Tuts Make";
        try{
            $send = Mail::send('email', $data, function($message) {
                $message->from('asadmalik@cicm.pk', 'Admin');
                $message->to('asadmalik431@gmail.com', 'Asad MAlik')
     
                        ->subject('Tuts Make Mail');
            });
            // dd(Mail::failures());
            if (Mail::failures()) {
               echo "fail";
             }else{
               echo "succes";
             }
        }
        catch(\Exception $e){
            // Get error here
            print $e->getMessage();
            exit;
        }
        
    }
    public function get_childs_drivers(Request $request){

    	$res  = array();
		$id   = $request->id;
		$data = childBus::select('drivers.*')->leftJoin('driver_bus_relations', 'driver_bus_relations.id', '=', 'child_buses.rel_id')->where('parent_id' ,$id)->leftJoin('drivers', 'drivers.id', '=', 'driver_bus_relations.driver_id')->where('parent_id' ,$id)->get();
		foreach ($data as $key => $value) {
            if ($value) {
                if ($value['profile_image']) {
                    $data[$key]['image_url'] = url('storage/app/driver_profile/'.$value['profile_image'].''); 
                }
                else{

                  $data[$key]['image_url']  = '';
                }
            }
        }
		return  response()->json([
		    'success' => "1",
 		    'data' => $data,

		]);
    }
    public function drivers_location(Request $request){

    	$res  = array();
		$id   = $request->id;
		$data = childBus::select('drivers.longitude','drivers.latitude')->leftJoin('driver_bus_relations', 'driver_bus_relations.id', '=', 'child_buses.rel_id')->where('parent_id' ,$id)->leftJoin('drivers', 'drivers.id', '=', 'driver_bus_relations.driver_id')->where('parent_id' ,$id)->get();
		return  response()->json([
		    'success' => "1",
 		    'data' => $data,

		]);
	}
}
