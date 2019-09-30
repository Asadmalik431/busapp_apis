<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ParentsTable;
use App\Driver;
use App\Busses;
use App\DriverBusRelation;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Hash;

use Session;

class LoginController extends Controller
{
    public function __construct()
    {

    }
    public function login(Request $request){
        $this->validate($request,[
            'email'            => 'required|string|email|max:191',
            'password'         => 'required',
        ]);
        $res                  = array();
        $count = User::where('email',$request->email)->count();
        if($count == 1){
            $res['err_email']     = 'no';
            $res['email_message'] = '';
            $data = User::where('email',$request->email)->first();
            if( Hash::check($request->password,$data->password)) {
                Session()->put('id', $data->id);
                Session()->put('email', $data->email);
                Session()->put('type', $data->type);
                Session()->put('photo', $data->photo);
                $res['pass_message'] = '';
                $res['err_pass']      = 'no'; 
            }
            else{
                // echo "ff";exit;
                $res['err_pass']      = 'yes'; 
                $res['pass_message']  = 'Password you enter does not match';
            }
        }
        else{
            $res['err_pass']      = 'yes'; 
            $res['pass_message']  = '';
            $res['err_email']     = 'yes';
            $res['email_message'] = 'Email You Enter Does Not Exist';
        }
        // $request->session()->flush();
        return $res;
        // $request->session()->put('id', 'value');
        // print_r($request->session()->all());
        // $request->session()->flush();
    }
    function get_session(Request $request){
        
        if(session('id'))
        {
            $res['err'] =  'yes';
        }
        else{
            $res['err'] = 'no';
        }
        return $res;
    }
}
