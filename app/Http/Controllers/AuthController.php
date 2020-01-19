<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {


        
    }


    public function register(Request $request)
    {

        $this->validate($request, [
            'username'  => 'required|unique:users',
            'name'      => 'required',
            'password'  => 'required',
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->name     = $request->name;
        $user->password = Hash::make($request->password);
        $result = $user->save();


        $out = [
            'message' => 'register failed',
            'code'    => 401,
            'status'  => 'failed',
        ];

        if($result){
            $out = [
                    "message" => "register_success",
                    "code"    => 200,
                    "status"  => 'success',
                    "result"  => [
                        'username'  => $request->username,
                        'name'      => $request->name,
                        'password'  => $request->password,
                    ],
                ];
        }

        return response()->json($out, $out['code']);
    }






    public function login(Request $request)
    {

        $this->validate($request,[
            'username'  => 'required',
            'password'  => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;


        $out['code']    = 401;
        $out['status']  = 'failed';
        $out['message'] = 'Login failed';
        $out['result']  = NULL;

        $user = User::where('username', $username)->first();
        if($user != NULL AND $user != ''){
            if(Hash::check($password, $user->password)){

                $newToken   = $this->generateRandomString(100);
                $result = $user->update([
                    'token' => $newToken,
                ]);
        
                if($result){
                    $out['code'] = 200;
                    $out['status'] = 'success';
                    $out['message'] = 'Login success';
                    $out['result'] = [
                            'username'  => $username,
                            'name'      => $user->name,
                            'key'       => $newToken,
                        ];
                }
            }
        }

        return response()->json($out, $out['code']);
    }



    public function logout(Request $request)
    {
        $user = User::where('token', $request->key);

        $out['code']    = 401;
        $out['status']  = 'failed';
        $out['message'] = 'Logout failed';

        if($user != NULL AND $user != ''){
            $result = $user->update([
                'token' => NULL,
            ]);

            if($result){
                $out['code']    = 200;
                $out['status']  = 'success';
                $out['message'] = 'Logout Success';
            }
        }

        return response()->json($out, $out['code']);
    }





    protected function generateRandomString($length = 80)
    {
        $karakkter = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakkter);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $karakkter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }

}
