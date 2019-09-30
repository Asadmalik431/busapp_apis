<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ParentsTable;
use App\Driver;
use App\User;
use App\Busses;
use App\DriverBusRelation;
use App\RouteModel;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function get_dashboard()
    {
        $data     = array();
        $data['total_drivers'] = Driver::count();
        $data['total_parents'] = ParentsTable::count();
        $data['total_busses']  = Busses::count();
        $data['total_routes']  = RouteModel::count();
        $Busses_ids            = Busses::pluck('id')->all();
        $data['bus_assign']    = DriverBusRelation::whereIn('bus_id', $Busses_ids)
                                 ->count();
        $Routes_ids            = RouteModel::pluck('id')->all();
        $data['route_assign']  = DriverBusRelation::whereIn('route_id', $Routes_ids)
                                 ->count();
        return $data;
    }
    public function get_admin_info(Request $request){
        $id   = $request->id;
        $data = User::select('id','name','email')->where('id',$id)->first();
        return $data;
    }

    public function update_admin(Request $request){

        $id   = $request->id;
        $user = User::findOrFail($id);
        $this->validate($request,[
            'name'             => 'required|string|max:191',
        
        ]);
        $user->name  = $request->name;
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return ['message' => 'Updated Successfully'];
    }
}
