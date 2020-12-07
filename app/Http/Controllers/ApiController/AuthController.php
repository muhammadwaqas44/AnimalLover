<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signup(Request $request, AuthServices $authServices){
        $data = $authServices->registeruser($request);
        return $data;
    }

    public function signin(Request $request, AuthServices $authServices){
        $data = $authServices->loginuser($request);
        return $data;
    }

    public function forgetemail(Request $request, AuthServices $authServices){
        $data = $authServices->forgetemailservice($request);
        return $data;
    }

    public function forgettoken(Request $request, AuthServices $authServices){
        $data = $authServices->forgettokenservice($request);
        return $data;
    }

    public function restpassword(Request $request, AuthServices $authServices){
        $data = $authServices->passwordreset($request);
        return $data;
    }

    public function deleteAccount(Request $request, AuthServices $authServices){
        $data = $authServices->deleteAccount($request);
        return $data;
    }
    public function sendFeedback(Request $request, AuthServices $authServices){
        $data = $authServices->sendFeedback($request);
        return $data;
    }

    public function deleteImage(Request $request, AuthServices $authServices){
        $data = $authServices->deleteImage($request);
        return $data;
    }

    public function apiLogout(Request $request, AuthServices $authServices){
        $data = $authServices->apiLogout($request);
        return $data;
    }







}
