<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Illuminate\Support\Facades\Hash;
use App\ParentsTable;
use App\Children;
use App\DriverBusRelation;
use App\Busses;
use App\Driver;
use App\childBus;
use App\tblStation;
use DB;
use Carbon\Carbon;
use Storage;
use File;
class ParentsController extends Controller
{
    public function __construct()
	{

	}
	public function get_data(Request $request){
		
		$columns     = ['id','name'];
		$column      = $request['column'];
		$dir         = $request['dir'];
		$length      = $request['length'];
		$searchValue = $request['search'];
		$query = ParentsTable::select('parents_tables.*')
			->orderBy('parents_tables.id','DESC');
		if ($searchValue) {
			$query->where(function($query) use ($searchValue) {
			$query->where('name', 'like', '%' . $searchValue . '%')
			->orWhere('id', 'like', '%' . $searchValue . '%');
			})
			;
		}
		$data        = $query->paginate($length);
		foreach ($data as $key => $value) {
			$data[$key]['total_childs'] = Children::where('parent_id', $value['id'])->count();
			$data[$key]['childs'] = Children::select('childrens.id',
									'childrens.child_stop as bus_station',
									'childrens.name',
									'childrens.rollno','childrens.class',
									'childrens.parent_id','childrens.age',
									'childrens.school','childrens.section',
									'childrens.branch',
									'childrens.date_of_bith',
									'childrens.nic','childrens.present_address',
									'childrens.permanent_address',
									'childrens.special_handling',
									'childrens.profile_image',
									'child_buses.bus_id as bus_id')
									->leftjoin("child_buses",'child_buses.child_id' , 'childrens.id')
									->where('childrens.parent_id', $value['id'])->get();
		}
		// $data['bus_relation'] = DriverBusRelation::get();
		return new DataTableCollectionResource($data);
	}
	public function update_parent_data(Request $request){
		//  echo "<pre>";
		// print_r($request->all());
		// exit;
		if ($request->id == '') {
			$this->validate($request,[
	            'name'          => 'required|string|max:191|unique:parents_tables',
	           	'email'         => 'required|string|email|max:191|unique:parents_tables',
	            'password'      => 'required|string|min:6',
	            'nicNumber'     => 'required|string|max:191|unique:parents_tables',
	            'contact'       => 'required|string|max:191',
	            'qualification' => 'required|string|max:191',
	            'occupation'    => 'required|string|max:191',
	            'homeAddress'   => 'required|string|max:191',



	        ]);


	        $parent                 = new ParentsTable;
			$parent->name           = $request->name;
			$parent->password       = Hash::make($request->password);
			$parent->contact        = $request->contact;
			$parent->email          = $request->email;
			$parent->nicNumber      = $request->nicNumber;
			$parent->qualification  = $request->qualification;
			$parent->occupation     = $request->occupation;
			$parent->homeAddress    = $request->homeAddress;
			$parent->officeAddress  = $request->officeAddress;
			$parent->save();
			$LastInsertId = $parent->id;
			if ($request->file('image') != '' && $LastInsertId != '') {
				$imageName = 'parent_profile_'.$LastInsertId.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$parent_update  = ParentsTable::findOrFail($LastInsertId);
				$parent_update->profile_image    = $imageName;
				$parent_update->save();
				$profile   = $request->file('image');
				Storage::disk('parent_profile')->put($imageName, File::get($profile));
			}
		}
		else{
			$id   = $request->id;
			$user = ParentsTable::findOrFail($id);
	        $this->validate($request,[
	            'name'          => 
	            					'required|string|max:191|unique:parents_tables,name,'.$id,
	            'email'         => 
	            					'required|string|email|max:191|unique:parents_tables,email,'.$id,
	            'nicNumber'     => 
	            					'required|string|max:191|unique:parents_tables,nicNumber,'.$id,
	            'contact'       => 'required|string|max:191',
	            'qualification' => 'required|string|max:191',
	            'occupation'    => 'required|string|max:191',
	            'homeAddress'   => 'required|string|max:191',
	        ]);
	        $user->name           = $request->name;
	        $user->contact        = $request->contact;
			$user->email          = $request->email;
			$user->nicNumber      = $request->nicNumber;
			$user->qualification  = $request->qualification;
			$user->occupation     = $request->occupation;
			$user->homeAddress    = $request->homeAddress;
			$user->officeAddress  = $request->officeAddress;
	        if (!empty($request->password)) {
	        	$user->password = Hash::make($request->password);
	        }
	        $user->save();
	        if ($request->file('image') != '' &&  $id != '') {

				$imageName = 'parent_profile_'.$id.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$parent_update  = ParentsTable::findOrFail($id);
				$parent_update->profile_image    = $imageName;
				$parent_update->save();
				// Storage::disk('parent_profile')->delete($imageName);
				$profile   = $request->file('image');

				Storage::disk('parent_profile')->put($imageName, File::get($profile));
			}
	        return ['message' => 'Updated the user info'];
		}
		
	}
	function get_busess_for_childs (){
		$data = DriverBusRelation::select('busses.*','driver_bus_relations.route_id','driver_bus_relations.id as rel_id')
				->leftjoin("busses",'busses.id' , 'driver_bus_relations.bus_id')
				->get();
		foreach ($data as $key => $value) {
			$count = childBus::where('bus_id', $value['id'])->count();
			if ($count < $value['capacity']) {
				# code...
			}else{
				unset($data[$key]);
			}
			$data[$key]['bus_station'] = tblStation::where('route_id',$value['route_id'])
										 ->get();

		}
		return $data;
	}
	function get_busess_for_childs_edit ($bus_id){
		$data = DriverBusRelation::select('busses.*','driver_bus_relations.route_id','driver_bus_relations.id as rel_id')
				->join("busses",'busses.id' , 'driver_bus_relations.bus_id')
				->get();
		foreach ($data as $key => $value) {
			$data[$key]['bus_station'] = tblStation::
										 where('route_id',$value['route_id'])
										 ->get();
			if ($bus_id == $value['id']) {
				
			}
			else{
				$count = childBus::where('bus_id', $value['id'])->count();
				if ($count < $value['capacity']) {
					# code...
				}else{
					if (isset($data[$key])) {
						unset($data[$key]);
					}
				}
			}
		}
		return $data;
	}
	public function update_child_data(Request $request){
		// echo "<pre>";
		// print_r($request->all());
		// exit;
		$this->validate($request,[
            'name'         => 'required|string|max:191',
           	'rollno'       => 'required|string|max:191',
           	'class'        => 'required|string|max:191',
           	'age'          => 'required|string|max:191',
           	'school'       => 'required|string|max:191',
           	'section'      => 'required|string|max:191',
           	'date_of_bith' => 'required|string|max:191',
           	'branch'       => 'required|string|max:191',
           	'nic'          => 'required|string|max:191',
           	'bus_id'       => 'required',
           	'bus_station'  => 'required',

        ]);
		if ($request->id == '') {
			
	        $children                    = new Children;
			$children->name              = $request->name;
			$children->rollno            = $request->rollno;
			$children->class             = $request->class;
			$children->age               = $request->age;
			$children->school            = $request->school;
			$children->section           = $request->section;
			$children->date_of_bith      = $request->date_of_bith;
			$children->branch            = $request->branch;
			$children->nic               = $request->nic;
			$children->present_address   = $request->present_address;
			$children->permanent_address = $request->permanent_address;
			$children->special_handling  = $request->special_handling;
			$children->special_handling  = $request->special_handling;
			$children->parent_id         = $request->parent_id;
			$children->child_stop        = $request->bus_station;
			$children->save();
			$LastInsertId = $children->id;
			if ($request->file('image') != '' && $LastInsertId != '') {
				$imageName = 'child_profile_'.$LastInsertId.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$child_update    = Children::findOrFail($LastInsertId);
				$child_update->profile_image    = $imageName;
				$child_update->save();
				$profile   = $request->file('image');
				Storage::disk('child_profile')->put($imageName, File::get($profile));
			}
			$LastInsertId                = $children->id;
			$childbus                    = new childBus;
			$childbus->bus_id            = $request->bus_id;
			$childbus->child_id          = $LastInsertId;
			$childbus->rel_id            = $request->rel_id;
			$childbus->parent_id         = $request->parent_id;
			$childbus->save();

			


		}
		else{
			// echo "<pre>";
			// print_r($request->all());
			// exit;
			$id                          = $request->id;
			$children                    = Children::findOrFail($id);
	        $children->name              = $request->name;
			$children->rollno            = $request->rollno;
			$children->class             = $request->class;
			$children->age               = $request->age;
			$children->school            = $request->school;
			$children->section           = $request->section;
			$children->date_of_bith      = $request->date_of_bith;
			$children->branch            = $request->branch;
			$children->nic               = $request->nic;
			$children->present_address   = $request->present_address;
			$children->permanent_address = $request->permanent_address;
			$children->special_handling  = $request->special_handling;
			$children->special_handling  = $request->special_handling;
			$children->parent_id         = $request->parent_id;
			$children->child_stop        = $request->bus_station;
			$children->save();
			if ($request->file('image') != '' && $id != '') {
				$imageName = 'child_profile_'.$id.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$child_update    = Children::findOrFail($id);
				$child_update->profile_image    = $imageName;
				$child_update->save();
				$profile   = $request->file('image');
				Storage::disk('child_profile')->put($imageName, File::get($profile));
			}
			childBus::where('child_id',$id)->delete();
			$childbus                    = new childBus;
			$childbus->bus_id            = $request->bus_id;
			$childbus->child_id          = $id;
			$childbus->rel_id            = $request->rel_id;
			$childbus->parent_id         = $request->parent_id;
			$childbus->save();
	        return ['message' => 'Updated the Child info'];
		}

	}
	
	public function parent_login(Request $request){
    	# code...
    	$username = $request->username;
    	$password = $request->password;

    	echo $username;
    }
	public function get_all_busess_routes($id){
		// echo $id;
		$data = childBus::select('busses.bus_no','busses.bus_model',
				'busses.capacity','busses.bus_column',
				'drivers.name as driver_name','driver_bus_relations.id',
				'driver_bus_relations.bus_id',
				'driver_bus_relations.book_time',
				'driver_bus_relations.route_id')
				->leftjoin('busses','busses.id','child_buses.bus_id')
				->leftjoin('driver_bus_relations','driver_bus_relations.id','child_buses.rel_id')
				->leftjoin('drivers','drivers.id','driver_bus_relations.driver_id')
				->where('child_buses.child_id',$id)
				->first();
		if ($data) {
			$date               = explode(',', $data['book_time']);
			$start_date         = $date[0];
			$end_date           = $date[1];
			$start_date         = Carbon::parse($start_date)->
								  format('h:m M-d');
			$start_year         = Carbon::parse($start_date)->format('Y');
			$end_date           = Carbon::parse($end_date)->format('h:m M-d');
			$end_year           = Carbon::parse($end_date)->format('Y');
			$data['start_date'] = $start_date;
			$data['start_year'] = $start_year;
			$data['end_date']   = $end_date;
			$data['end_year']   = $end_year;

			$data['book_seats'] = childBus::where('bus_id',$data['bus_id'])
								  ->where('rel_id',$data['id'])
								  ->count();
			$bus_station        = tblStation::where('route_id',
								  $data['route_id'])->get(); 
			$stations           = array();
			foreach ($bus_station as $key => $value) {
				$stations[$key]['date_build']  = "";
				$stations[$key]['description'] = $value['name'];
				$stations[$key]['name']        = $value['name'];
				$stations[$key]['position']    = ['lat'=> (float)$value['latitude'],
												  'lng'=> (float)$value['longitude']];	
			}
			$data['bus_station'] = $stations;
		}
		// foreach ($data as $key => $value) {
		// 	$driver = Driver::where('id',$value['driver_id'])->first();
		// 	if ($driver) {
		// 		$data[$key]['driver_name'] = $driver->name;
		// 	}
		// 	$bus = Busses::where('id',$value['bus_id'])->first();
		// 	if ($bus) {
		// 		$data[$key]['bus_no']     = $bus->bus_no;
		// 		$data[$key]['bus_model']  = $bus->bus_model;
		// 		$data[$key]['capacity']   = $bus->capacity;
		// 		$data[$key]['bus_column'] = $bus->bus_column;
		// 	}
		
		// }
		$res['data'] = $data;
		return $data;
	}

	public function save_seat(Request $request){

		childBus::where('bus_id',$request->busid)
		->where('child_id',$request->childid)
		->where('parent_id',$request->parentid)
		->where('rel_id',$request->relationid)
		->update(['seatNo' => $request->seatNo])
		;
	}

	public function get_bus_seat(Request $request){
		
		$busid           = $request->busid;
		$relid           = $request->relid;
		$res             = 
						   childBus::select('seatNo','child_id')->where('bus_id',$busid)->where('seatNo','!=', null)->where('rel_id',$relid)->get();
		$data['res']     = $res;
		return $data;
		}
		public function get_driver_route(Request $request){	
		$driverid		=$request->driverid;
		$data = tblStation::select('*')
		->leftjoin('driver_bus_relations','driver_bus_relations.route_id','tbl_stations.route_id')
		->where('driver_bus_relations.driver_id',$driverid)->get();
		return  response()->json([
			'success' => "1",
			 'data' => $data,

		]);
		}
}
