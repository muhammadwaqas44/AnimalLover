<?php

namespace App\Http\Controllers\ApiController;

use App\Services\QuestionnaireService;
use Illuminate\Http\Request;

class QuestionnaireController
{
    public function adddata(Request $request, QuestionnaireService $questionnaireService){
        $data = $questionnaireService->addQuestionnaireService($request);
        return $data;
    }
}
