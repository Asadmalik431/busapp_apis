<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RouteModel;
use App\tblStation;
use DB;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class RouteController extends Controller
{
    public function __construct(){

	}
    public function get_data(Request $request){

		$columns     = ['id','location'];
		$column      = $request['column'];
		$dir         = $request['dir'];
		$length      = $request['length'];
		$searchValue = $request['search'];
		$query = RouteModel::select('*')
		->orderBy('route_models.id','DESC');
		if ($searchValue) {
			$query->where(function($query) use ($searchValue) {
			$query->where('location', 'like', '%' . $searchValue . '%')
			->orWhere('id', 'like', '%' . $searchValue . '%');
			});
		}
        $data        = $query->paginate($length);
        foreach ($data as $key => $value) {
			$data[$key]['stations'] = tblStation::where('route_id', $value['id'])->orderBy('order', 'ASC')->get();
		}
		return new DataTableCollectionResource($data);
    }
    
    public function update_route_data(Request $request){

		if ($request->id == '') {
			$this->validate($request,[
                'location'     => 'required|string',
	        ]);
	        $route            = new RouteModel;
			$route->location  = $request->location;
			$route->save();
			$LastInsertId = $route->id;
			// $count = 0 ;
			// foreach ($request->station as $key => $value) {
			// 	echo $value['station']."<br>";
				
			// 	if (!empty($value['station'])) {
			// 		$count ++;
			// 		$stations            = new tblStation;
			// 		$stations->name      = $value['station'];
			// 		$stations->route_id  = $LastInsertId;
			// 		$stations->order     = $count;
			// 		$stations->save();
			// 	}
			// 	else{

			// 	}
			// }
		}
		else{
			$id  = $request->id;
            $route = RouteModel::findOrFail($id);
            $this->validate($request,[
                'location'     => 'required|string',
	        ]);
			$route->location  = $request->location;
			$route->save();
		}
		
    }
    public function delete_route(Request $request){

		$id  = $request->id;
		$res = RouteModel::where('id',$id)->delete();
		return $res;
    }
    public function save_stations(Request $request){

    	$data                = $request->all();
    	$time_diversion      = explode(':', $data['timepick']) ;
    	$hour                = $time_diversion[0];
    	$minutes             = $time_diversion[1];
    	if ($hour < 10 ) {
    		$hour = '0'.(int)$hour;
    	}
    	if ($minutes == '') {
    		$minutes = '0';
    	}
    	if ($minutes < 10){
    		$minutes ='0'.(int)$minutes;
    	}
    	if ($hour < 12) {
    		$time_slap = 'am';
    	}
    	if ($hour >= 12) {
    		if ($hour == 24) {
    			$time_slap = 'am';
    		}
    		else{
    			$time_slap = 'pm';
    		}
    		if ($hour > 12) {
    			$hour = (int)$hour - 12;
    		}
    		if ($hour < 10) {
    			$hour = '0'.$hour;
    		}
    	}

    	$data['timepick']    = $hour.':'.$minutes .' '. $time_slap; 
    	$name_check          = tblStation::where('route_id', 
    						   $data['route_id'])
    						   ->where('name',$data['address']['route'])
    						   ->first();
    	if ($name_check) {
    		return response()->json([
		    'success' => 0,
		    'data'    => 'Station already exist in this route '

		]);
    	}
    	else{
    		$station_number      = tblStation::where('route_id', $data['route_id'])
    						   ->count();
	    	$station_number      += 1;
	    	$stations            = new tblStation;
			$stations->name      = $data['address']['route'];
			$stations->route_id  = $data['route_id'];
			$stations->latitude  = $data['address']['latitude'];
			$stations->longitude = $data['address']['longitude'];
			$stations->order     = $station_number;
			$stations->timepick  = $data['timepick'];
			$stations->save();
			$all_stattions       = tblStation::where('route_id', $data['route_id'])
	    						   ->get();
	   		return response()->json([
			    'success' => 1,
			    'data'    => $all_stattions

			]);
    	}
    	exit;
    	
    }
    public function update_stations_list(Request $request){
    	
    	$data = $request->all();
    	tblStation::where('route_id',$data['route_id'])->delete();
    	foreach ($data['stattions'] as $key => $value) {
		    $dataSet[] = [
		    	'id'         => $value['id'],
		        'name'       => $value['name'],
		        'route_id'   => $data['route_id'],
		        'created_at' => $value['created_at'],
		        'updated_at' => $value['updated_at'],
		        'order'      => $key + 1,
		        'latitude'   => $value['latitude'],
		        'longitude'  => $value['longitude'],
		    ];
		}
		tblStation::insert($dataSet);
		$all_stattions       = tblStation::where('route_id', 
							   $data['route_id'])
							   ->orderBy('order', 'ASC')
	    						   ->get();
   		return response()->json([
		    'success' => 1,
		    'data'    => $all_stattions

		]);
    }
    
}
