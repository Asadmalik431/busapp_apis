<?php

namespace App\Http\Controllers\Conductors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Illuminate\Support\Facades\Hash;
use App\Conductor;
use Storage;

class MainController extends Controller
{
    public function login(Request $request){
    	# code...
    	$email    = $request->email;
        $password = $request->password;
        //  echo $this->checkEmail($email);exit;
        if($this->checkEmail($email)){
            $parent   = Conductor::where('email', $email)->first();
        }
        else{
            $parent   = Conductor::where('contact', $email)->first();
        }
        if ($parent) {
            $hashedPassword = $parent->password;
            if (Hash::check($password, $hashedPassword)) {
                 if ($parent) {
                    if ($parent['profile_image']) {
                        $parent['image_url'] = url('storage/app/conductor_profile/'.$parent['profile_image'].''); 
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
}
