<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\TabListingService;
use Illuminate\Http\Request;

class TabListingController extends Controller
{
    public function getList(TabListingService $tabListingService, Request $request){
        $data = $tabListingService->getList($request);
        return $data;
    }
}
