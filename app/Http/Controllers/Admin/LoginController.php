<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginView(){
        if(Auth::check()){
            return redirect()->route("admin-dashboard");
        }
        return view('auth.login');
    }

    public function loginPost(Request $request){
        $this->validate($request, [
            'password' => 'required',
            'email' => 'required|email'
        ], [
            'password.required' => 'Password is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email ;format is not valid',
        ]);
        $credentials = $request->only('email', 'password');
        $remember = ($request->remember) ? true : false;
        if (Auth::attempt($credentials, $remember)) {
            return response()->json(['result' => 'success', 'message' => 'Login  successfully']);
        } else {
            return response()->json(['result' => 'error', 'message' => 'Failed to Login']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login-view');
    }

}
