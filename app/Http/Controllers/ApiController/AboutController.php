<?php

namespace App\Http\Controllers\ApiController;

use App\Services\AboutServices;
use Illuminate\Http\Request;

class AboutController
{
    public function aboutpet(Request $request, AboutServices $aboutServices){
        $data = $aboutServices->createaboutpet($request);
        return $data;
    }

    public function petsListing(Request $request, AboutServices $aboutServices){
        $data = $aboutServices->petsListing($request);
        return $data;
    }
    public function deletePet(Request $request, AboutServices $aboutServices){
        $data = $aboutServices->deletePet($request);
        return $data;
    }
    public function editPet(Request $request, AboutServices $aboutServices){
        $data = $aboutServices->editPet($request);
        return $data;
    }
    public function aboutme(Request $request, AboutServices $aboutServices)
    {
        $data = $aboutServices->createaboutme($request);
        return $data;
    }
}
