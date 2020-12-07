<?php

namespace App\Services;

use App\AboutPet;
use App\Alert;
use App\AlertGallery;
use App\Comment;
use App\Conversation;
use App\FeedBack;
use App\Hobbie;
use App\Interest;
use App\InterestedAnimal;
use App\InterestedService;
use App\LikedAllergicAnimal;
use App\PetMemories;
use App\Post;
use App\ReportPost;
use App\User;
use App\AboutMe;
use App\UserLikePost;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Log;


class AuthServices
{

    public function apiLogout($request){
        $user = User::where('id', Auth::id())->first();
        $user->fcm_token = null;
        $user->save();
        Auth::logout();
        return Helper::jsonResponse(1, 'User logged out', $user, '');
    }
    public function forgetemailservice($request)
    {
        $errorMessages = [
            'email.required' => 'Email is required',
            'email.email' => 'Email format is not valid',
        ];
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|email',
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if($user->login_type == 'Email'){
                $forgettoken = Str::random(4);
                $user->forget_token = $forgettoken;
                Mail::to($request->email)->send(new SendMail($forgettoken));
                $user->save();
                return Helper::jsonResponse(1, 'Token sent to your email');
            }
            if($user->login_type !== 'Email'){
                return Helper::jsonResponse(0, '', '', 'User is registered with social platform');
            }
        } else {
            return Helper::jsonResponse(0, '', '', 'Email is not registered');
        }
    }

    public function forgettokenservice($request)
    {
        $errorMessages = [
            'forget_token.required' => 'Forget Token is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'forget_token' => 'required|string',
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }
        $user = User::where('forget_token', $request->forget_token)->first();
        if ($user) {
            return Helper::jsonResponse(1, 'Valid token', ['id' => $user->id]);
        } else {
            return Helper::jsonResponse(0, '', '', 'Invalid token');
        }
    }

    public function passwordreset($request)
    {
        $errorMessages = [
            'password.required' => 'Password is required',
            'password.confirmed' => 'Password and confirm password do not match',
            'id.required' => 'User id is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'password' => 'required|string|min:6|confirmed',
                'id' => 'required|numeric',
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }
        $user = User::find($request->id);
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return Helper::jsonResponse(1, 'Password Changed Successfully', ['token' => $user->token]);
        } else {
            return Helper::jsonResponse(0, '', '', 'User not found');
        }
    }

    public function loginuser($request)
    {
        $request->login_type = ucwords($request->login_type);
        $errorMessages = [
            'email.required_if' => 'Email is required',
            'email.email' => 'Email format is not valid',
            'password.required_if' => 'Password is required',
            'password.min' => 'Please make sure to enter at least 6 characters',
            'social_token.required_if' => 'Social Token is required',
            'lat.required' => 'Latitude is required',
            'long.required' => 'Longitude is required',
            'fcm_token.required' => 'FCM Token is required',
            'login_type.required' => 'Login Type is required',
            'time_zone.required' => 'Time zone is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'email' => 'required_if:login_type,Email,email|email',
                'password' => 'required_if:login_type,Email,email|min:6',
                'social_token' => 'required_if:login_type,Social,social',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'fcm_token' => 'required',
                'login_type' => 'required',
                'time_zone' => 'required',

            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        if ($request->login_type != 'Email') {
            $user = User::where('email', $request->email)->first();

            if ($user) {
//                if($user->status == 0){
//                    return Helper::jsonResponse(0, '', '','User is no more active.');
//                }
                if ($request->login_type != $user->login_type) {
                    return Helper::jsonResponse(0, '', '','User already exists with ' . $user->login_type . ' email');
                }
                if ($request->social_token != $user->social_token) {
                    $user->social_token = $request->social_token;
                }
                $attempts = $user->attempts;
                $user->attempts += 1;
                $user->lat = $request->lat;
                $user->long = $request->long;
                $user->fcm_token = $request->fcm_token;
                $user->time_zone = $request->time_zone;
                $user->save();
                $getuser = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
                $getuser['is_new_registered '] = false;
                $checkAboutMe = AboutMe::where('id', $user->id)->get();
                $user->aboutme_status = 0;
                if ($checkAboutMe->count() > 1) {
                    $user->aboutme_status = 1;
                }
                return Helper::jsonResponse(1, 'Login Successfully', ['token' => $user->token, 'attempts' => $attempts, 'aboutme_status' => $user->aboutme_status, 'user' => $getuser]);
            } else {
                $tokenstring = Str::random(40) . time();
                $user = new User();
                $attempts = $user->attempts;
                $user->attempts += 1;
                $user->social_token = $request->social_token;
                $user->profile_image = $request->profile_image;
                $user->role_id = 2;
                $user->package_id = 1;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->lat = $request->lat;
                $user->long = $request->long;
                $user->login_type = $request->login_type;
                $user->time_zone = $request->time_zone;
                $user->fcm_token = $request->fcm_token;
                $user->token = $tokenstring;
                $user->social_token = $request->social_token;
                $user->save();
                $getuser = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
                $getuser['is_new_registered '] = true;
                $checkAboutMe = AboutMe::where('id', $user->id)->get();
                $user->aboutme_status = 0;
                if ($checkAboutMe->count() > 1) {
                    $user->aboutme_status = 1;
                }
                return Helper::jsonResponse(1, 'User Registered Successfully', ['token' => $tokenstring,'attempts' => $attempts, 'aboutme_status' => $user->aboutme_status, 'user' => $getuser]);
            }
        } else {
            $user = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();

            if ($user) {
//                if($user->status == 0){
//                    return Helper::jsonResponse(0, '', '','User is no more active.');
//                }
                if ($request->login_type != $user->login_type) {
                    return Helper::jsonResponse(0, '', '', 'A user with this email address currently exists');
                }
                if (Hash::check($request->password, $user->password)) {
                    $attempts = $user->attempts;
                    $user->attempts += 1;
                    $user->fcm_token = $request->fcm_token;
                    $user->time_zone = $request->time_zone;
                    $user->save();
                    $checkAboutMe = AboutMe::where('id', $user->id)->get();
                    $user->aboutme_status = 0;
                    if(!empty($user->profile_image)){
                        $user->profile_image = asset('project-assets/images/'.$user->profile_image);
                    }
                    if ($checkAboutMe->count() > 1) {
                        $user->aboutme_status = 1;
                    }


                    return Helper::jsonResponse(1, 'Login Successfully', ['token' => $user->token, 'attempts' => $attempts, 'user' => $user ]);
                } else {
                    return Helper::jsonResponse(0, '', '', 'Invalid email and/or password');
                }
            } else {
                return Helper::jsonResponse(0, '', '', 'Invalid email and/or password');
            }
        }
    }

    public function registeruser($request)
    {
        $request->login_type = ucwords($request->login_type);
        $errorMessages = [
            'first_name.required_if' => 'First Name is required',
            'last_name.required_if' => 'Last Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email format is not valid',
            'password.required_if' => 'Password is required',
            'password.min' => 'Please make sure to enter at least 6 characters in password',
            'password.confirmed' => 'Password and confirm password do not match',
            'username.required_if' => 'Username is required',
            'zip_code.required_if' => 'Zip Code is required',
            'profile_image.required' => 'Profile image is required',
            'lat.required' => 'Latitude is required',
            'long.required' => 'Longitude is required',
            'time_zone.required' => 'Time Zone is required',
            'fcm_token.required' => 'FCM Token is required',
            'login_type.required' => 'Login Type is required',
            'social_token.required_if' => 'Social token is required',
            'interested_service.required' => 'Please select what service you are interested in',
            'interested_service.between' => 'Please select what service you are interested in',
//            'phone.required_if' => 'Phone Number required',
//            'city.required_if' => 'City is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'password' => 'required_if:login_type,Email,email|string|min:6|confirmed',
                'first_name' => 'required_if:login_type,Email,email',
                'last_name' => 'required_if:login_type,Email,email',
                'email' => 'required|email',
                'username' => 'required_if:login_type,Email,email|unique:users',
//                'phone' => 'required_if:login_type,Email,email|numeric',
                'zip_code' => 'required_if:login_type,Email,email',
                'social_token' => 'required_if:login_type,Facebook|required_if:login_type,Google',
                'profile_image' => 'required',
                'lat' => 'required',
                'long' => 'required',
                'time_zone' => 'required',
                'fcm_token' => 'required',
                'login_type' => 'required',
                'interested_service' => 'required|numeric|between:1,3'

            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        if(ucwords($request->login_type) == 'Email') {
            $existing_user = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
            if ($existing_user) {
                return Helper::jsonResponse(0, '', '','A user with this email address currently exists');
            }
        }
        if (ucwords($request->login_type) != 'Email') {
            $existing_user = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
            if ($existing_user) {
                if (ucwords($request->login_type) != $existing_user->login_type) {
                    return Helper::jsonResponse(0, '', '', 'A user with this email address currently exists');
                }else{
                    $existing_user->time_zone = $request->time_zone;
                    $existing_user->save();
                    return Helper::jsonResponse(1, 'Login successfully', ['token' => $existing_user->token,'user'=>$existing_user]);
                }

                if ($request->social_token != $existing_user->social_token && ucwords($request->login_type) == $existing_user->login_type) {
                    return Helper::jsonResponse(0, '', '', 'User is not exists with ' . $request->login_type . ' email');
                }
                $checkAboutMe = AboutMe::where('id', $existing_user->id)->get();
                $existing_user->aboutme_status = 0;
                if ($checkAboutMe->count() > 1) {
                    $existing_user->aboutme_status = 1;
                }
                return Helper::jsonResponse(1, 'Login Successfully', ['token' => $existing_user->token, 'aboutme_status' => $existing_user->aboutme_status, 'user' => $existing_user]);
            }
        }
        $existing_user = User::where('email', $request->email)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
        if ($existing_user) {
            if (ucwords($request->login_type) != $existing_user->login_type) {
                return Helper::jsonResponse(0, '', '', 'A user with this email address currently exists');
            }
            $existing_user->time_zone = $request->time_zone;
            $existing_user->save();
            $checkAboutMe = AboutMe::where('id', $existing_user->id)->get();
            $existing_user->aboutme_status = 0;
            if ($checkAboutMe->count() > 1) {
                $existing_user->aboutme_status = 1;
            }
            return Helper::jsonResponse(0, 'User already exists', ['token' => $existing_user->token, 'aboutme_status' => $existing_user->aboutme_status, 'user' => $existing_user], 'User already exists');
        }

        if (ucwords($request->login_type) != 'Email') {
            $username = self::userNameCheck($request);
        } else {
            $username = $request->username;
        }
        $tokenstring = Str::random(40) . time();
        $user = new User();
        if ($request->login_type == 'Email' ) {
//            dd('a');

            $user->phone = $request->phone;
            if ($request->address) {
                $user->address = $request->address;
            }
            if ($request->city) {
                $user->city = $request->city;
            }
            if ($request->state) {
                $user->state = $request->state;
            }
            $user->zip_code = $request->zip_code;
            $user->password = Hash::make($request->password);
            $user->profile_image =  $request->profile_imageprofile_image;
        } else {
            $user->social_token = $request->social_token;
            $user->profile_image = $request->profile_image;
        }
        $user->role_id = 2;
        $user->package_id = 1;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->lat = $request->lat;
        $user->long = $request->long;
        $user->login_type = $request->login_type;
        $user->time_zone = $request->time_zone;
        $user->fcm_token = $request->fcm_token;
        $user->token = $tokenstring;
        $user->username = $username;
        $user->social_token = $request->social_token;
        $user->service_id = $request->interested_service;
        $user->profile_image = $request->profile_image;
        $user->save();
        $checkAboutMe = AboutMe::where('id', $user->id)->get();
        $user->aboutme_status = 0;
        if ($checkAboutMe->count() > 1) {
            $user->aboutme_status = 1;
        }
        if($checkAboutMe->count() < 1){
            $sl = new AboutMe();
            if($request->gender){
                $sl->gender = $request->gender;
                if($request->interested_gender){
                    $sl->interested_gender = $request->interested_gender;
                }
                $sl->user_id = $user->id;
                $sl->save();
            }
        }


        $users = User::where('id', $user->id)->with('user_personal_info', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')->first();
        if(!empty($users->profile_image)){
            $users->profile_image = asset('project-assets/images/'.$users->profile_image);
        }

        return Helper::jsonResponse(1, 'User Registered Successfully', ['token' => $user->token, 'aboutme_status' => $user->aboutme_status, 'user' => $users]);
    }

    public function userNameCheck($request)
    {
        $username = strtolower($request->first_name) . rand(1000, 9999);
        $exists = User::where('username', $username)->get();
        if ($exists->count() > 0) {
            $results = self::userNameCheck($request);
        } else {
            $results = $username;
            return $results;
        }
    }

    public function deleteAccount($request){
//        $user = User::where('id', Auth::user()->id)->first();
//        if($user){
//            dd(Auth::id());


        DB::table('users')->where('users.id', Auth::id())->delete();
        DB::table('about_mes')->where('about_mes.user_id', Auth::id())->delete();
        DB::table('about_pets')->where('about_pets.user_id', Auth::id())->delete();
        DB::table('alerts')->where('alerts.created_by', Auth::id())->delete();
        sleep(1);
        DB::table('alert_gallery')->where('alert_gallery.user_id', Auth::id())->delete();
        DB::table('comments')->where('comments.user_id', Auth::id())->delete();
        DB::table('feedback')->where('feedback.user_id', Auth::id())->delete();
        sleep(1);
        DB::table('hobbies')->where('hobbies.user_id', Auth::id())->delete();
        DB::table('interested_animals')->where('interested_animals.user_id', Auth::id())->delete();
        DB::table('interests')->where('interests.user_id', Auth::id())->delete();
        sleep(1);
        DB::table('liked_allergic_animals')->where('liked_allergic_animals.user_id', Auth::id())->delete();
        DB::table('pet_memories')->where('pet_memories.user_id', Auth::id())->delete();
        DB::table('posts')->where('posts.created_by', Auth::id())->delete();
        DB::table('pet_memories')->where('pet_memories.user_id', Auth::id())->delete();
        sleep(1);
        DB::table('report_posts')->where('report_posts.user_id', Auth::id())->delete();
        DB::table('user_like_posts')->where('user_like_posts.liked_by', Auth::id())->delete();
        sleep(1);

        return Helper::jsonResponse(1, 'User has been deleted', '','');

//        Conversation::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->delete();
        //chat conversation gallery images profile actions


//            return Helper::jsonResponse(1, 'User has been deleted', $s,'');

//            User::where('id', $user->id)->delete();
//            sleep(1);
//            AboutMe::where('user_id', $user->id)->delete();
//            sleep(1);
//            AboutPet::where('user_id', $user->id)->delete();
//            sleep(1);
//            Alert::where('created_by', $user->id)->delete();
//            sleep(1);
//            AlertGallery::where('user_id', $user->id)->delete();
//            sleep(1);
//            Comment::where('user_id', $user->id)->delete();
//            sleep(1);
//            Conversation::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
//            sleep(1);
//            FeedBack::where('user_id', $user->id)->delete();
//            sleep(1);
//            Hobbie::where('user_id', $user->id)->delete();
//            sleep(1);
//            InterestedAnimal::where('user_id', $user->id)->delete();
//            sleep(1);
//            Interest::where('user_id', $user->id)->delete();
//            sleep(1);
//            LikedAllergicAnimal::where('user_id', $user->id)->delete();
//            sleep(1);
//            PetMemories::where('user_id', $user->id)->delete();
//            sleep(1);
//            Post::where('created_by', $user->id)->delete();
//            sleep(1);
//            ReportPost::where('created_by', $user->id)->delete();
//            sleep(1);
//            UserLikePost::where('liked_by', $user->id)->delete();
//            sleep(1);


//            return Helper::jsonResponse(1, 'User has been deleted', '','');
//        }else{
//            return Helper::jsonResponse(0, '', '','User not found');
//        }
    }

    public function sendFeedback($request){
        $errorMessages = [
            'feedback.required' => 'Feedback is required.'
        ];
        $validator = Validator::make($request->all(),
            [
                'feedback' => 'required|string'
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }
        $feedback = FeedBack::where('user_id', Auth::id())->first();
        if(!$feedback){
            $feedback = new FeedBack();
        }
        $feedback->feedback = $request->feedback;
        $feedback->user_id = Auth::user()->id;
        $feedback->save();
        if($feedback){
            return Helper::jsonResponse(1, 'Feedback has been sent', $feedback, '');
        }else{
            return Helper::jsonResponse(0, '', '', 'Could not sent feedback');
        }

    }

    public function deleteImage($request){
        $errorMessages = [
            'image.required' => 'image is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'image' => 'required|string'
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        return Helper::deleteImage($request->image);


    }
}
