<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $out['code']    = 401;
        $out['status']  = 'failed';
        $out['message'] = 'Silahkan Masukan Key';

        if($request->key){
            
            $user = User::where('token', $request->key)->first();
            if($user != NULL AND $user != ''){
                return $next($request);
            }else{
                $out['message'] = 'Key Tidak Dikenal';
            }
        }

        /*
        if($request->key){
            $user = User::where('username', $request->username)->first();
            if($user != NULL AND $user != ''){
                if($user->token === $request->key){
                    return $next($request);
                }else{
                    $out['message'] = 'Key salah';
                }
            }else{
                $out['message'] = 'Username Tidak Dikenal';
            }
        }
        */

        return response()->json($out, $out['code']);
    }
}
