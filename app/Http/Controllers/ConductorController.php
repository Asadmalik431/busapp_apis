<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conductor;
use Storage;
use File;
use Illuminate\Support\Facades\Hash;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class ConductorController extends Controller
{
    public function __construct()
	{

	} 

	public function get_data(Request $request){
		$columns     = ['id','name', 'email','contact'];
		$column      = $request['column'];
		$dir         = $request['dir'];
		$length      = $request['length'];
		$searchValue = $request['search'];
		$query = Conductor::select('*')
		->orderBy('conductors.id','DESC');
		if ($searchValue) {
			$query->where(function($query) use ($searchValue) {
			$query->where('name', 'like', '%' . $searchValue . '%')
			->orWhere('email', 'like', '%' . $searchValue . '%')
			->orWhere('contact', 'like', '%' . $searchValue . '%')
			->orWhere('id', 'like', '%' . $searchValue . '%');
			});
		}
		$data        = $query->paginate($length);
		// foreach ($data as $key => $value) {
		// 	$driver_bus  = DriverBusRelation::select('*')
		// 									   ->where('driver_id', $value['id'])
		// 									   ->first();
		// 	$times      = $driver_bus['book_time'];
		// 	if ($times != '') {
		// 		$times                   = explode(',', $times);
		// 		$driver_bus['starttime'] = $times[0];
		// 		$driver_bus['endtime']   = $times[1];
		// 	}
			
		// 	$data[$key]['driver_assign_bus'] = $driver_bus;							
		// }
		return new DataTableCollectionResource($data);
	}
	public function update_conductor_data(Request $request){
	
		// echo "<pre>";
		// print_r($request->all());
		// exit;
		if ($request->id == '') {

			$this->validate($request,[
	            'name'             => 'required|string|max:191',
				'email'            => 'required|string|email|max:191|unique:conductors',
				'contact'          => 'required|max:191|unique:conductors',
				'password'         => 'required',
	        ]);

	        $conductor                   = new Conductor;
			$conductor->name             = $request->name;
			$conductor->email            = $request->email;
			$conductor->contact          = $request->contact;
			$conductor->password         = Hash::make($request->password);
			$conductor->save();
			$LastInsertId = $conductor->id;
			if ($request->image != '' && $LastInsertId != '') {
				$imageName = 'conductor_profile_'.$LastInsertId.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$conductor    = Conductor::findOrFail($LastInsertId);
				$conductor->profile_image    = $imageName;
				$conductor->save();
				$profile   = $request->file('image');
				Storage::disk('conductor_profile')->put($imageName, File::get($profile));
			}
			
		}
		else{
			$id     = $request->id;
			$conductor = Conductor::findOrFail($id);
        	$this->validate($request,[
				'name'             => 'required|string|max:191',
				'email'            => 
									  'required|string|email|max:191|unique:conductors,email,'.$id,
				'contact'          => 'required|max:191|unique:conductors,contact,'.$id,
	
			]);
			$conductor->name             = $request->name;
			$conductor->email            = $request->email;
			$conductor->contact          = $request->contact;
			if (!empty($request->password)) {
	        	$conductor->password = Hash::make($request->password);
	        }
			$conductor->save();
			if ($request->image != '' && $id != '') {
				$imageName = 'conductor_profile'.$id.'.'.$request
					  		  ->image->getClientOriginalExtension();
				$conductor    = Conductor::findOrFail($id);
				$conductor->profile_image    = $imageName;
				$conductor->save();
				$profile   = $request->file('image');
				Storage::disk('conductor_profile')->put($imageName, File::get($profile));
			}
	        return ['message' => 'Updated the Condutor info'];
		}
		
	}
	public function delete_conductor(Request $request){

		$id  = $request->id;
		$res = Conductor::where('id',$id)->delete();
		return $res;
	}
}
