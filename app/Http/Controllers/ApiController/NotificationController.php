<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Notification;
use App\Services\FirebaseService;
use App\Services\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function clearNotification(){
        $result= Notification::where('action_to',Auth::user()->id)->update(['is_seen'=>1]);
        if($result){
           return Helper::jsonResponse(1, 'clear all notification',['token'=> Auth::user()->token]);
        }
    }

    public function getNotification(Request $request){
        $a = Notification::where(['action_to' => Auth::user()->id, 'is_seen' => "0" ])->whereIn('type', ['like','post_like','post_cooment', 'click'])
                            ->with('users.user_personal_info','users.pets','users.hobbies','users.LikedAllergicAnimal','users.interests')->orderBy('id', 'DESC')->get();

        $a->map(function ($u) {
            $u->users->profile_image = asset('/project-assets/images/'.$u->users->profile_image);
            return $u;
        });

        $b = Notification::where(['action_to' => Auth::user()->id, 'is_seen' => "0" ])->whereIn('type', ['lost','found','adoption'])
            ->with('users.user_personal_info','users.pets','users.hobbies','users.LikedAllergicAnimal','users.interests')->orderBy('id', 'DESC')->get();

        $b->map(function ($w) {
            $w->users->profile_image = asset('/project-assets/images/'.$w->users->profile_image);
            return $w;
        });

        return Helper::jsonResponse(1,'List of all notifications', ['token'=> Auth::user()->token, 'others' => $a, 'alerts' => $b ]);
    }

    public function unseenSingleNotification(Request $request){
        $errorMessage = [
            'id.required' => 'Id  is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'id' => 'required|numeric'
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }

        $noti = Notification::where(['id' => $request->id, 'action_to' => Auth::user()->id ])->first();
        if(!$noti){
            return Helper::jsonResponse(0, 'error','','Invalid id or not authorize');
        }

        $noti->is_seen = 1;
        $noti->save();
        return Helper::jsonResponse(1, 'seen marked',[],'');
    }

    public function countNotification(){
        $total = Notification::where('action_to',Auth::user()->id)->count();
        $unseen = Notification::where(['action_to'=> Auth::user()->id, 'is_seen' => 0])->count();
        $seen = Notification::where(['action_to'=> Auth::user()->id, 'is_seen' => 1])->count();
        return Helper::jsonResponse(1, 'clear all notification',['token'=> Auth::user()->token, 'total' => $total, 'unseen' => $unseen, 'seen' =>  $seen]);
    }

    public function firebaseNotify(Request $request){
        $data = FirebaseService::sendPushNotification($request);
//        dd($data)
        return $data;
    }
}
