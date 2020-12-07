<?php

namespace App\Services;

use App\LikedAllergicAnimal;
use App\PetMemories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionnaireService
{
    public function addQuestionnaireService(Request $request){

        $errorMessage = [
//            'liked_animals.required' => 'Must add animal you like other then your pet',
//            'allergic_animals.required' => 'Must add animal you are allergic with',
            'memories.required' => 'Please share some of your and your pet\'s memories with us'
        ];
        $validator = Validator::make($request->all(),
            [
//                'liked_animals' => 'required',
//                'allergic_animals' => 'required',
                'memories' => 'required|string'
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, '','',$validator->messages()->first());
        }
        if ($request->liked_animals){
            foreach($request->liked_animals as $liked_animal){
                $likedanimal = new LikedAllergicAnimal();
                $likedanimal->type = 'Liked';
                $likedanimal->name = $liked_animal;
                $likedanimal->user_id = Auth::user()->id;
                $likedanimal->save();
            }
        }
        if ($request->allergic_animals){
            foreach($request->allergic_animals as $allergic_animal){
                $allergicanimal = new LikedAllergicAnimal();
                $allergicanimal->type = 'Allergic';
                $allergicanimal->name = $allergic_animal;
                $allergicanimal->user_id = Auth::user()->id;
                $allergicanimal->save();
            }
        }
        if ($request->memories){
            $memories = new PetMemories();
            $memories->memories = $request->memories;
            $memories->user_id = Auth::user()->id;
            $memories->save();
        }
        return Helper::jsonResponse(1, 'Questionnaire completed successfully', ['token'=>Auth::user()->token]);
    }
}
