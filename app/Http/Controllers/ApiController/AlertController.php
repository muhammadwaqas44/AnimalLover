<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\AlertServices;
use Illuminate\Http\Request;

class AlertController extends Controller
{

    public function create_alert(Request $request, AlertServices $alertServices){
        $data = $alertServices->create_alert($request);
        return $data;
    }

    public function alerts(Request $request,AlertServices $alertServices){
        $data = $alertServices->alerts($request);
        return $data;
    }

    public function delete_alert(Request $request, AlertServices $alertServices){
        $data = $alertServices->delete_alert($request);
        return $data;
    }


    public function edit_alert(Request $request, AlertServices $alertServices){
        $data = $alertServices->edit_alert($request);
        return $data;
    }

    public function delete_alert_image(Request $request, AlertServices $alertServices){
        $data = $alertServices->delete_alert_image($request);
        return $data;
    }


}
