<?php

namespace App\Http\Middleware;

use App\Services\Helper;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticateUser
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
        $errorMessage = [
            'token.required' => 'User Token is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'token' => 'required|string',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, '','',$validator->messages()->first());
        }
        $user = User::where('token',$request->token)->first();
        if ($user){
            if(Auth::loginUsingId($user->id)){
                return $next($request);
            }
            else{
                return Helper::jsonResponse(0, '','','Authentication Fails');
            }
        }else{
            return Helper::jsonResponse(0, '','','Invalid User token');
        }

    }
}
