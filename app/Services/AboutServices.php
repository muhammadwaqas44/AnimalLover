<?php

namespace App\Services;

use App\AboutMe;
use App\AboutPet;
use App\GalleryImage;
use App\Hobbie;
use App\Interest;
use App\InterestedService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Log;

class AboutServices
{
    public function createaboutpet($request)
    {
        $errorMessage = [
            'kind.required' => 'Kind is required',
            'name.required' => 'name is required',
            'age.required' => 'age is required',
            'breed.required' => 'breed is required',
            'description.required' => 'description is required',
            'image.required' => 'Image is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'kind' => 'required',
                'name' => 'required',
                'age' => 'required',
                'breed' => 'required',
                'description' => 'required',
                'image' => 'required',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, '','',$validator->messages()->first());
        }

        $aboutpet = new AboutPet();
        $aboutpet->pets_description = $request->description;
        $aboutpet->kind = $request->kind;
        $aboutpet->name = $request->name;
        $aboutpet->age = $request->age;
        $aboutpet->breed = $request->breed;
        $aboutpet->user_id = Auth::user()->id;
        $aboutpet->image = $request->image;
        $aboutpet->save();

        return Helper::jsonResponse(1, 'Pets added successfully', ['token'=>Auth::user()->token, 'pet'=> $aboutpet]);
    }

    public function petsListing($request){
        $pets = Auth::user()->pets()->get();
        if($pets->count() > 0){
            return Helper::jsonResponse(1, 'List of '.Auth::user()->first_name.' pets', ['token'=>Auth::user()->token, 'pets' => $pets]);
        }else{
            return Helper::jsonResponse(1, 'No record found', [], '');
        }
    }

    public function deletePet($request){
        $errorMessage = [
            'pet_id.required' => 'Pet id is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'pet_id' => 'required|numeric|min:1',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }
        $pet = AboutPet::where('id', $request->pet_id)->first();
        if($pet){
            if($pet->user_id != Auth::user()->id){
                return Helper::jsonResponse(1, 'Not authorize to delete this pet', ['token'=>Auth::user()->token]);
            }
            $pet->delete();
            return Helper::jsonResponse(1, 'Pet has been deleted successfully', ['token'=>Auth::user()->token]);
        }else{
            return Helper::jsonResponse(1, 'Pet not found', ['token'=>Auth::user()->token]);
        }
    }

    public function editPet($request){
        $errorMessage = [
            'pet_id.required' => 'Pet id is required',
            'age.numeric' => 'Age of pet is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'pet_id' => 'required|numeric|min:1',
                'age' => 'numeric|min:1',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }

        $pet = AboutPet::where(['id' => $request->pet_id, 'user_id' => Auth::user()->id])->first();
        if($pet){
            if($pet->user_id != Auth::user()->id){
                return Helper::jsonResponse(0, '', ['token'=>Auth::user()->token],'Not authorize to edit this pet');
            }
            (!empty($request->kind)) && $pet->kind = $request->kind;
            (!empty($request->name)) && $pet->name = $request->name;
            (!empty($request->age)) && $pet->age = $request->age;
            (!empty($request->breed)) && $pet->breed = $request->breed;
            (!empty($request->pets_description)) && $pet->pets_description = $request->pets_description;
            if(!empty($request->image) ){
                if(!empty($pet->image)){
                    if(is_file(public_path('project-assets/images/'.$pet->image))){
                        unlink(public_path('project-assets/images/'.$pet->image));
                    }
                }
                $pet->image = $request->image;
            }
            $pet->save();
            return Helper::jsonResponse(1, 'Pet updated successfully', ['token'=> Auth::user()->token, 'pet' => $pet]);
        }else{
            return Helper::jsonResponse(1, 'Pet not found', [], '');
        }
    }

    public function createaboutme($request){
        $errorMessage = [
            'gender.required' => 'Please select gender',
            'relationship_status.required' => 'Please select a relationship status',
            'occupation.required' => 'Please enter your occupation',
            'hobbies.required' => 'Please add multiple hobbies',
            'interests.required' => 'Please select at least one interest',
            'age.required' => 'Please enter your age',
            'interested_animal.required' => 'Please add interested animal',
        ];
        $validator = Validator::make($request->all(),
            [
                'gender' => 'required|string',
                'relationship_status' => 'required|string',
                'occupation' => 'required|string',
                'hobbies' => 'required|array|min:1',
                'hobbies.*' => 'required|string|distinct|max:10',
                'interests' => 'required|array|min:1',
                'interests.*' => 'required|string|distinct|max:10',
                'age' => 'required|numeric|min:0',
                'interested_animal' => 'required',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, '','',$validator->messages()->first());
        }

        // if($request->interested_services == 1 || $request->interested_services == 2 || $request->interested_services == 3){
        //     Auth::user()->Service_id = $request->interested_services;
        //     Auth::user()->save();
        // }else{
        //     return Helper::jsonResponse(0, 'Select option from 1 2 3','');
        // }


        foreach ($request->hobbies as $hobby){
            $newhobby = new Hobbie();
            $newhobby->name = $hobby;
            $newhobby->user_id = Auth::user()->id;
            $newhobby->save();
        }
        foreach ($request->interests as $interest){
            $newinterest = new Interest();
            $newinterest->name = $interest;
            $newinterest->user_id = Auth::user()->id;
            $newinterest->save();
        }

        if ($request->images != null){
            foreach ($request->images as $image){
                $galleryImage = new GalleryImage();
                $galleryImage->name = 'project-assets/images/'.$image;
                $galleryImage->user_id = Auth::user()->id;
                $galleryImage->save();
            }
        }
        $aboutme = new AboutMe();
        $aboutme->gender = $request->gender;
        $aboutme->relationship_status = $request->relationship_status;
        $aboutme->occupation = $request->occupation;
        $aboutme->age = $request->age;
        $aboutme->interested_animal =$request->interested_animal;
        $aboutme->user_id = Auth::user()->id;
        $aboutme->about_me = $request->about_me;
        $aboutme->save();
        return Helper::jsonResponse(1, 'Data added successfully', ['token'=>Auth::user()->token]);
    }

}
