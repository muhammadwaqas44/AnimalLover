<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\ActionOnProfileService;
use Illuminate\Http\Request;

class ActionOnProfileController extends Controller
{
    public function like_action(ActionOnProfileService $actionOnProfileService, Request $request){
        $data = $actionOnProfileService->action($request);
        return $data;
    }

}
