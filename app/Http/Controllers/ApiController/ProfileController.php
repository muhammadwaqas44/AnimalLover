<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\ProfileServices;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getUserProfile(ProfileServices $profileServices)
    {
        $data = $profileServices->getUserProfile();
        return $data;
    }

    public function userProfileEdit(ProfileServices $profileServices, Request $request)
    {
        $data = $profileServices->userProfileEdit($request);
        return $data;
    }

    public function allProfile(ProfileServices $profileServices, Request $request)
    {
        $data = $profileServices->allProfile($request);
        return $data;
    }

    public function searchUsers(ProfileServices $profileServices, Request $request)
    {
        $data = $profileServices->searchUsers($request);
        return $data;
    }

    public function userById(ProfileServices $profileServices, Request $request)
    {
        $data = $profileServices->userById($request);
        return $data;
    }





}
