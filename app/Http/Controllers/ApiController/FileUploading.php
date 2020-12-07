<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Services\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploading extends Controller
{
    public function fileuploading(Request $request, Helper $helper){
        $data = $helper->uploadFile($request->profile_image);
        return $data;
    }
}
