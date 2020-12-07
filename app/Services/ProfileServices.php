<?php

namespace App\Services;

use App\InterestedAnimal;
use App\LikedAllergicAnimal;
use App\ProfileAction;
use App\User;
use App\AboutPet;
use App\AboutMe;
use Faker\Provider\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Driver\Query;

class ProfileServices
{
    public function getUserProfile()
    {
        $user = User::where('id', Auth::user()->id)->with('user_personal_info', 'Interested_animal')->first();
        if(!$user){
            return Helper::jsonResponse(0, 'User not exists', '');
        }
        $user->profile_image = asset('/project-assets/images/'.$user->profile_image);

//        if()
//        dd($user);
        if($user->service_id){
            $user->service_id = Auth::user()->InterestedService->name;
        }
        $user['full_name'] = $user->first_name.' '.$user->last_name;
        $user['interested_service'] = $user->option;
        $user['hobbies'] = Auth::user()->hobbies()->get();
        $user['pets'] = Auth::user()->pets()->get();
        $user['interests'] = Auth::user()->interests()->get();
        $user['pet_count'] = Auth::user()->pets()->count();
        $user['Interested_animal_count'] = $user->Interested_animal()->count();
        $user['Liked_Allergic_Animal'] = Auth::user()->LikedAllergicAnimal()->get();
        $user['posts'] = Auth::user()->posts()->get();
        return Helper::jsonResponse(1, 'User profile', ['token' => $user->token,'user'=>$user]);

    }

    public function allProfile($request)
    {
        $array = array();
        $myActionOnPeople = ProfileAction::where('action_by', Auth::user()->id)->pluck('action_to');    // users i make action on
        $peopleNotLiked_ByMe = ProfileAction::where('action_to', Auth::user()->id)->where('like', 1)->whereNotIn('action_by', $myActionOnPeople)->latest()->pluck('action_by');  // ids of users who liked me but i have not

        $profile = User::where("id", "!=", Auth::id())->whereNotIn('id', $myActionOnPeople);

        /*$randomUsers = User::where('id', '!=',Auth::user()->id);
        if($peopleNotLiked_ByMe->count() == 0){
            $randomUsers->take(20)->get();
        }*/

        $filter = false;

        if (!empty($request->gender)) {

            $errorMessage = [
                'gender.in' => 'Invalid gender',
            ];
            $validator = Validator::make($request->all(),
                [
                    'gender' => 'string|in:male,female,other',
                ], $errorMessage
            );
            if ($validator->fails()) {
                return Helper::jsonResponse(0, 'error', '', $validator->messages()->first());
            }

            $profile = $profile->whereHas('user_personal_info', function (Builder $query) use ($request) {
                $query->where('gender', '=', $request->gender);
            });
            $filter = true;
        }

        if (!empty($request->interested_gender)) {
            $errorMessage = [
                'interested_gender.in' => 'Invalid interested gender',
            ];
            $validator = Validator::make($request->all(),
                [
                    'interested_gender' => 'string|in:men,women,both',
                ], $errorMessage
            );
            if ($validator->fails()) {
                return Helper::jsonResponse(0, 'error', '', $validator->messages()->first());
            }
            $profile = $profile->whereHas('user_personal_info', function (Builder $query) use ($request) {
                $query->where('interested_gender', '=', $request->interested_gender);
            });
            $filter = true;
        }

        if (!empty($request->type)) {
            $errorMessage = [
                'type.in' => 'Invalid type',
            ];
            $validator = Validator::make($request->all(),
                [
                    'type' => 'string|in:cat,dog,horse,pocket_pets,birds,reptiles,other',
                ], $errorMessage
            );
            if ($validator->fails()) {
                return Helper::jsonResponse(0, 'error', '', $validator->messages()->first());
            }
            if ($request->type) {
                $profile->whereHas('pets', function (Builder $query) use ($request) {
                    return $query->where('kind', '=', $request->type);
                });
            };
            $filter = true;
        }

        if ($request->distance) {
            if (!empty($request->distance['lat']) && !empty($request->distance['long']) && !empty($request->distance['mile'])) {
                $lat = $request->distance['lat'];
                $lng = $request->distance['long'];
                $mile = $request->distance['mile'];
                //                $circle_radius = 6371;    //for KM
                $circle_radius = 3958.748;  // for miles
                $haverClause = '(' . $circle_radius . ' * acos(cos(radians(' . $lat . ')) * cos(radians(users.lat)) * cos(radians(users.long)- radians(' . $lng . ') )+sin(radians(' . $lat . ')) * sin(radians(users.lat))))';
                $user = User::select('users.*', DB::raw($haverClause . ' AS distance'))->whereIn('id', $peopleNotLiked_ByMe)
                    ->whereRaw($haverClause . ' <? ', [$mile])
                    ->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->withCount('pets')->latest("id")->get();
                $user->map(function ($u) {
                    $u->distance = round($u->distance, 2);
                    $u->profile_image = asset('/project-assets/images/'.$u->profile_image);
                    return $u;
                });
                return Helper::jsonResponse(1, 'User profile with distance', $user);
            }
        }

        if (!$filter) {
            $userLikedMe = User::whereIn('id', $peopleNotLiked_ByMe)->get();
        }

        $b = $profile->whereNotIn('id', $peopleNotLiked_ByMe)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal', 'Interested_animal')->withCount('pets')->get();

        $b->map(function ($w) {
            $w->profile_image = asset('/project-assets/images/'.$w->profile_image);
            return $w;
        });

        if (!$filter) {
            if ($userLikedMe->count() > 0) {
                $b = $userLikedMe->merge($b);
            }
        }

        $b->each(function ($i) {
            $newArr = [];
            foreach($i->pets as $pet){
                 array_push($newArr,$pet->breed);
            }
            $i->breeds = $newArr;
        });
        return Helper::jsonResponse(1, 'User profile', $b);
    }

    public function userProfileEdit($request){

        $errorMessages = [
            'interested_service.in' => 'interested_service should be 1 for dating, 2 for dating_socializing, 3 for socializing',
            'interested_gender.in' => 'interested_gender should be among men,women,both',
            'relationship_status.in' => 'relationship_status should be single or married',
//            'interest_animal.in' => 'interest_animal should be among dog,cat,horse,pocketpet,birds,reptiles,other',
        ];
        $validator = Validator::make($request->all(),
            [
                'first_name' => 'string|nullable',
                'last_name' => 'string|nullable',
                'address' => 'string|nullable',
                'phone' => 'numeric|nullable|min:0',
                'age' => 'numeric|nullable|min:0',
                'gender' => 'string|nullable|in:male,female,other',
                'city' => 'string|nullable',
                'zip_code' => 'numeric|nullable',
                'interested_gender' => 'string|nullable|in:men,women,both',
                'relationship_status' => 'string|nullable|in:single,married',
                'interested_service' => 'numeric|nullable|in:1,2,3',
                'about_me' => 'string|nullable|max:300',
                'interested_animal' => 'array',
                'interested_animal.*' => 'string|distinct|nullable',
//                'interest_animal.*' => 'string|distinct|nullable|in:dog,cat,horse,pocketpet,birds,reptiles,other',
                'allergic_animal' => 'array',
                'allergic_animal.*' => 'string|distinct|nullable',
//              'allergic_animal.*' =>  No allergies,Dog,Cat,Horse,Pocket, pets (Hamsters, rats, mice, etc.),Birds,reptiles,Other, please specify []
            ],$errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }



        $user = User::where('id', Auth::user()->id)->with('user_personal_info', 'Interested_animal', 'LikedAllergicAnimal')->first();
        if($user->count() > 0){
            if(!empty($request->first_name)){
                $user->first_name = $request->first_name;
            }
            if(!empty($request->last_name) != ''){
                $user->last_name = $request->last_name;
            }
            if(!empty($request->address)){
                $user->address = $request->address;
            }
            if(!empty($request->phone)){
                $user->phone = $request->phone;
            }
            if(!empty($request->city)){
                $user->city = $request->city;
            }
            if(!empty($request->zip_code)){
                $user->zip_code = $request->zip_code;
            }
            if(!empty($request->interestedSrvices)){
                $user->service_id = $request->interestedSrvices;
            }
            if(!empty($request->profile_image) ){
                if(!empty($user->profile_image)){
                    if(is_file(public_path('project-assets/images/'.$user->profile_image))){
                         unlink(public_path('project-assets/images/'.$user->profile_image));
                    }
                }
                $user->profile_image = $request->profile_image;
            }
            $aboutme = AboutMe::where('user_id', $user->id)->first();
            if(!$aboutme){
                $aboutme = new AboutMe();
            }
            if(!empty($request->interestedGender)){
                $aboutme->interested_gender = $request->interestedGender;
            }

            if(!empty($request->interestedRelationship)){
                $aboutme->relationship_status = $request->interestedRelationship;
            }
            if(!empty($request->about_me)){
                $aboutme->about_me = $request->about_me;
            }

            if(!empty($request->age)){
                $aboutme->age = $request->age;
            }
            if(!empty($request->gender)){
                $aboutme->gender = $request->gender;
            }
            $aboutme->user_id = Auth::user()->id;

            if(!empty($request->interested_animal)){
                InterestedAnimal::where('user_id', Auth::user()->id)->delete();
                $saved_animals = collect($user->interested_animal)->pluck('name')->toArray();
                if(!empty($saved_animals)){
                    foreach ($request->interested_animal as $animal){
                        if(!(in_array($animal, $saved_animals))){
                            $ia =  new InterestedAnimal ();
                            $ia->name = strtolower($animal);
                            $ia->user_id = Auth::user()->id;
                            $ia->save();
                        }
                    }
                }
                else{
                    foreach ($request->interested_animal as $animal){
                        $ia =  new InterestedAnimal ();
                        $ia->name = strtolower($animal);
                        $ia->user_id = Auth::user()->id;
                        $ia->save();
                    }
                }
            }

            if(!empty($request->allergic_animal)){
                if(strtolower($request->allergic_animal[0]) == "no allergies"){
                    LikedAllergicAnimal::where('user_id', Auth::user()->id)->delete();
                    $naa = new LikedAllergicAnimal();
                    $naa->name = "no allergies";
                    $naa->type = "none";
                    $naa->user_id = Auth::user()->id;
                    $naa->save();
                }
                else{
                    LikedAllergicAnimal::where('user_id', Auth::user()->id)->delete();
                    foreach ($request->allergic_animal as $allergic_animal){
                        $i =  new LikedAllergicAnimal ();
                        $i->name = strtolower($allergic_animal);
                        $i->type = strtolower($allergic_animal);
                        $i->user_id = Auth::user()->id;
                        $i->save();
                    }
                }
            }

            $aboutme->save();
            $user->save();
            $more = User::where('id', Auth::user()->id)->with('user_personal_info', 'Interested_animal', 'LikedAllergicAnimal')->first();
            $more->profile_image = asset('/project-assets/images/'.$more->profile_image);
            if($more->service_id == 1){
                $more['interested_service'] = 'dating';
            }
            if($more->service_id == 2){
                $more['interested_service'] = 'both';
            }
            if($more->service_id == 3){
                $more['interested_service'] = 'socializing';
            }

            return Helper::jsonResponse(1, 'User updated with the information', ['token'=>$user->token, 'user'=>$more]);

        }
    }

    public function searchUsers($request)
    {
        if ($request->name) {
            $full_name = $request->name;
            $splitName = explode(' ', $full_name, 2);
            $firstName = $splitName[0];
            $lastName = !empty($splitName[1]) ? $splitName[1] : '';

            if(strpos($request->name, '@') == true || strpos($request->name, '.com') == true){    // if email contains . or @
                $allUsers = User::where('email','like', '%' . $request->name . '%')->with('user_personal_info:user_id,about_me','pets:user_id,pets_description')->get();
                $allUsers->map(function ($w) {
                    $w->profile_image = asset('/project-assets/images/'.$w->profile_image);
                    return $w;
                });
                return Helper::jsonResponse(1, 'Users', ['length' => $allUsers->count(), 'users' => $allUsers]);
            }

            if(preg_match('/@.+ \./', $request->name)){   // if email contains . and @ returns exact email
                $allUsers = User::where('email',$request->name)->with('user_personal_info:user_id,about_me','pets:user_id,pets_description')->get();
                $allUsers->map(function ($w) {
                    $w->profile_image = asset('/project-assets/images/'.$w->profile_image);
                    return $w;
                });
                return Helper::jsonResponse(1, 'Users', ['length' => $allUsers->count(), 'users' => $allUsers]);
            }

            $allUsers = User::where('first_name', 'like', '%' . $firstName . '%')->orWhere('last_name', 'like', '%' . $lastName . '%')->orWhere('email','like', '%' . $request->name . '%')
                ->orWhere('username','like', '%' . $request->name . '%')
                ->with('user_personal_info:user_id,about_me','pets:user_id,pets_description')
                ->orderBy('id', 'DESC')
                ->get();

            $allUsers->map(function ($w) {
                $w->profile_image = asset('/project-assets/images/'.$w->profile_image);
                return $w;
            });

            return Helper::jsonResponse(1, 'Users', ['length' => $allUsers->count(), 'users' => $allUsers]);
        }

    }

    public function userById($request){
        $errorMessages = [
            'id.required' => 'id is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'id' => 'numeric|required|min:0',
            ],$errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        $user = User::where('id', $request->id)->with('user_personal_info', 'Interested_animal')->first();
        if(!$user){
            return Helper::jsonResponse(0, 'User not exists', '');
        }
        $user->profile_image = asset('/project-assets/images/'.$user->profile_image);
        if($user->service_id){
            $user->service_id = $user->InterestedService->name;
        }

        $user['full_name'] = $user->first_name.' '.$user->last_name;
        $user['interested_service'] = $user->option;
        $user['hobbies'] = $user->hobbies()->get();
        $user['pets'] = $user->pets()->get();
        $user['interests'] = $user->interests()->get();
        $user['pet_count'] = $user->pets()->count();
        $user['Interested_animal_count'] = $user->Interested_animal()->count();
        $user['Liked_Allergic_Animal'] = $user->LikedAllergicAnimal()->get();
        $user['posts'] = $user->posts()->get();
//        $user['pet_breeds'] = $user->pets()->get()->pluck('breed');
        $user['pet_breeds'] = $user->pets()->groupBy('breed')->pluck('breed');
        return Helper::jsonResponse(1, 'User profile', ['token' => Auth::user()->token,'user'=>$user]);

    }
}


