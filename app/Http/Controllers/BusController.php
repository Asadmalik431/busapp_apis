<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Busses;
use App\DriverBusRelation;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
class BusController extends Controller
{
    public function __construct(){

	} 
	public function get_data(Request $request){

		$columns     = ['id','bus_no', 'bus_model'];
		$column      = $request['column'];
		$dir         = $request['dir'];
		$length      = $request['length'];
		$searchValue = $request['search'];
		$query = Busses::select('*')
		->orderBy('busses.id','DESC');
		if ($searchValue) {
			$query->where(function($query) use ($searchValue) {
			$query->where('bus_no', 'like', '%' . $searchValue . '%')
			->orWhere('bus_model', 'like', '%' . $searchValue . '%')
			->orWhere('id', 'like', '%' . $searchValue . '%');
			});
		}
        $data        = $query->paginate($length);
        foreach ($data as $key => $value) {
			$data[$key]['total_drivers'] = DriverBusRelation::where('bus_id', $value['id'])->count();
		}
		return new DataTableCollectionResource($data);
	}
	public function update_bus_data(Request $request){

		// echo "<pre>";
		// print_r($request->all());
		// exit;
		if ($request->id == '') {
			$this->validate($request,[
                'bus_no'     => 'required|string|max:191|unique:busses',
                'bus_model'  => 'required|string|max:191',
                'capacity'   => 'required',
                'bus_column' => 'required',
	        ]);
	        $bus            = new Busses;
			$bus->bus_no    = $request->bus_no;
			$bus->bus_model = $request->bus_model;
			$bus->capacity  = $request->capacity;
			$bus->bus_column= $request->bus_column;
			$bus->save();
		}
		else{
			$id  = $request->id;
            $bus = Busses::findOrFail($id);
            $this->validate($request,[
                'bus_no'     => 'required|string|max:191|unique:busses,bus_no,'.$id,
                'bus_model'  => 'required|string|max:191',
                'capacity'   => 'required',
                'bus_column' => 'required',
	        ]);
            $bus->bus_no    = $request->bus_no;
			$bus->bus_model = $request->bus_model;
			$bus->capacity  = $request->capacity;
			$bus->bus_column= $request->bus_column;
			$bus->save();
	        return ['message' => 'Updated the Driver info'];
		}
		
	}
	public function delete_bus(Request $request){

		$id  = $request->id;
		$res = Busses::where('id',$id)->delete();
		return $res;
    }
    
}
