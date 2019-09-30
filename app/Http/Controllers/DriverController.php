<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Driver;
use App\Busses;
use App\RouteModel;
use App\DriverBusRelation;
use Storage;
use File;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class DriverController extends Controller
{
    public function __construct()
	{

	} 

	public function get_data(Request $request){
		$columns     = ['id','name', 'email'];
		$column      = $request['column'];
		$dir         = $request['dir'];
		$length      = $request['length'];
		$searchValue = $request['search'];
		$query = Driver::select('*')
		->orderBy('drivers.id','DESC');
		if ($searchValue) {
			$query->where(function($query) use ($searchValue) {
			$query->where('name', 'like', '%' . $searchValue . '%')
			->orWhere('email', 'like', '%' . $searchValue . '%')
			->orWhere('id', 'like', '%' . $searchValue . '%');
			});
		}
		$data        = $query->paginate($length);
		foreach ($data as $key => $value) {
			$driver_bus  = DriverBusRelation::select('*')
											   ->where('driver_id', $value['id'])
											   ->first();
			$times      = $driver_bus['book_time'];
			if ($times != '') {
				$times                   = explode(',', $times);
				$driver_bus['starttime'] = $times[0];
				$driver_bus['endtime']   = $times[1];
			}
			
			$data[$key]['driver_assign_bus'] = $driver_bus;							
		}
		return new DataTableCollectionResource($data);
	}
	public function update_driver_data(Request $request){
	
		// echo "<pre>";
		// print_r($request->all());
		// exit;
		if ($request->id == '') {
			$this->validate($request,[
	            'name'             => 'required|string|max:191',
				'email'            => 'required|string|email|max:191|unique:drivers',
				'phonenumber'      => 'required|max:191|unique:drivers',
				'password'         => 'required',
				'eye_sight'        => 'required|max:191',
				'medical_category' => 'required|max:191',
				'register_school'  => 'required|max:191',
				'register_branch'  => 'required|max:191',
	            'idcardnumber'     => 'required|string|max:191|unique:drivers'
	        ]);

			
	        $driver                   = new Driver;
			$driver->name             = $request->name;
			$driver->email            = $request->email;
			$driver->phonenumber      = $request->phonenumber;
			$driver->idcardnumber     = $request->idcardnumber;
			$driver->address          = $request->address;
			$driver->eye_sight        = $request->eye_sight;
			$driver->medical_category = $request->medical_category;
			$driver->register_school  = $request->register_school;
			$driver->register_branch  = $request->register_branch;
			$driver->password         = Hash::make($request->password);
			$driver->save();
			$LastInsertId = $driver->id;
			if ($request->image != '' && $LastInsertId != '') {
				$imageName = 'driver_profile_'.$LastInsertId.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$driver    = Driver::findOrFail($LastInsertId);
				$driver->profile_image    = $imageName;
				$driver->save();
				$profile   = $request->file('image');
				Storage::disk('driver_profile')->put($imageName, File::get($profile));
			}
			
		}
		else{
			$id     = $request->id;
			$driver = Driver::findOrFail($id);
        	$this->validate($request,[
				'name'             => 'required|string|max:191',
				'email'            => 
									  'required|string|email|max:191|unique:drivers,email,'.$id,
				'phonenumber'      => 'required|max:191|unique:drivers,phonenumber,'.$id,
				'idcardnumber'     => 
								      'required|string|max:191|unique:drivers,idcardnumber,'.$id,
				'eye_sight'        => 'required|max:191',
				'medical_category' => 'required|max:191',
				'register_school'  => 'required|max:191',
				'register_branch'  => 'required|max:191',
			]);
			$driver->name             = $request->name;
			$driver->email            = $request->email;
			$driver->phonenumber      = $request->phonenumber;
			$driver->idcardnumber     = $request->idcardnumber;
			$driver->address          = $request->address;
			$driver->eye_sight        = $request->eye_sight;
			$driver->medical_category = $request->medical_category;
			$driver->register_school  = $request->register_school;
			$driver->register_branch  = $request->register_branch;
			if (!empty($request->password)) {
	        	$driver->password = Hash::make($request->password);
	        }
			$driver->save();
			if ($request->image != '' && $id != '') {
				$imageName = 'driver_profile_'.$id.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$driver    = Driver::findOrFail($id);
				$driver->profile_image    = $imageName;
				$driver->save();
				$profile   = $request->file('image');
				Storage::disk('driver_profile')->put($imageName, File::get($profile));
			}
	        return ['message' => 'Updated the Driver info'];
		}
		
	}
	public function delete_driver(Request $request)
	{
		$id  = $request->id;
		$res = Driver::where('id',$id)->delete();
		return $res;
	}
	public function assign_bus(Request $request){
        
        $driver_id      = $request->id;

        $assign_busses  = DriverBusRelation::where('driver_id','!=',$driver_id)
        				  ->groupby('bus_id')
        				  ->pluck('bus_id')
        				  ->all();
       	$data['busses'] = Busses::whereNotIn('id', $assign_busses)->get();
		// $data['busses'] = Busses::all();
		$data['routes'] = RouteModel::all();
		
		return $data;
	}
	public function assign_bus_to_drivers(Request $request){
		$messages = array( 'required' => 'This field is Required' );
		$this->validate($request,[
			'bus_id'    => 'required',
			'route_id'    => 'required',
			
		],$messages);
		$res =  $this->check_date_avalilability($request->all());
		if($res > 0){
			$data['msg'] = 'Not_Available';
			echo json_encode($data);
		}
		else{
			if ($request->id == '') {
				$bus_driver_rel = new DriverBusRelation;
				$book_time      = $request->starttime .','.$request->endtime;
				$bus_driver_rel->book_time  = $book_time;
			}
			else{
				$bus_driver_rel = DriverBusRelation::findOrFail($request->id);
				if ($request->starttime != '' && $request->endtime == '') {
					$book_time  = $request->starttime .','.$request->endtime;
					$bus_driver_rel->book_time  = $book_time;
				}
			}
			
			$bus_driver_rel->driver_id  = $request->driver_id;
			$bus_driver_rel->bus_id     = $request->bus_id;
			$bus_driver_rel->route_id   = $request->route_id;
			
			$bus_driver_rel->save();
			$data['msg']  = 'saved';
			echo json_encode($data);
		}
	}
	public function check_date_avalilability($req){
		$id        = $req['bus_id'];
		$count     = 0;
		$startTime = strtotime($req['starttime']);
		$endTime   = strtotime($req['endtime']);
		$times     = DriverBusRelation::where('bus_id',$id)->get();
		foreach($times as $time){
			$point = $time->book_time;
			if ($point != '') {
				$point = explode(',' , $point);
				$startpoint = strtotime($point[0]);
				$endpoint   = strtotime($point[1]);
				if($this->testRange($startTime,$endTime,$startpoint,$endpoint)){
					
				}
				else{
					$count ++;
				}
			}
			else{

			}	
		}
		return $count;
		// echo $count;exit;
	}
	function testRange($start_time1,$end_time1,$start_time2,$end_time2){

		$timeCheck;

		if(($end_time1 < $start_time2))
		{
			$timeCheck = true;
			return $timeCheck;
		}
		else if(($start_time1 > $start_time2) && ($start_time1 > $end_time2)) 
		{
			$timeCheck = true;
			return $timeCheck;
		}
		else
		{
			$timeCheck = false;
			return $timeCheck;
		}

	}

	public function driver_stations(Request $request){
        
        $id        = $request->id; 
        $data = DriverBusRelation::select('tbl_stations.name',
                'tbl_stations.latitude as lat',
                'tbl_stations.longitude as lang')
				->leftJoin('tbl_stations', 'tbl_stations.route_id', '=', 'driver_bus_relations.route_id')
                ->where('driver_id', $id)
                ->get();
        return  response()->json([
            'success' => '1',
            'data'    => $data,

        ]);
    }

}
