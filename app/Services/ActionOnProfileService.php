<?php


namespace App\Services;
use App\Conversation;
use App\Notification;
use App\ProfileAction;
use App\Traits\FormatDates;
use App\User;
use Auth;
use Carbon\Carbon;
use DateTime;

class ActionOnProfileService
{


    public function action($request){
        if(empty($request->id) || empty($request->action)){
            return Helper::jsonResponse(0, 'add id and action', []);
        }
        $check = ProfileAction::where('action_by', Auth::user()->id)->where('action_to', $request->id)->first();
        if(!$check){
            $check = new ProfileAction();
        }

        switch ($request->action){
            case 'like': return $this->action_like($request, $check); break;
            case 'dont_like': return $this->action_dont_like($request, $check); break;
            case 'not_sure': return $this->action_not_sure($request, $check); break;
            case 'block': return $this->action_block($request, $check); break;
        }
    }
    public function action_like($request, $profileAction){
        $profileAction->action_by = Auth::user()->id;
        $profileAction->action_to = $request->id;
        $profileAction->like = 1;
        $profileAction->dont_like = 0;
        $profileAction->not_sure = 0;
        $profileAction->block = 0;
//        $c = Carbon::now();
//        $c->timezone = Auth::user()->time_zone;
//        $c->timezone('UTC');
//        $profileAction->created_at = $c;
        $profileAction->save();

        $check = Notification::where('action_to', $request->id)->where('action_by',Auth::user()->id)->get();
//        dd($check);
        if($check->isEmpty()){
            $user =  User::where('id',$request->id)->first();
            $notification = new Notification();
            $notification->message = $user->first_name.' '.$user->last_name.' liked you';
            $notification->type = 'like';
            $notification->action_to = $request->id;
            $notification->action_by = Auth::user()->id;
//            $notification->created_at = $c;
            $notification->save();

            $fcm = array($user->fcm_token);
            $dataBody = array();
            $dataBody['title'] = $notification->message;
            $dataBody['message'] = '';
            $dataBody['data'] = [];
            $dataNoti = array();
            $dataNoti['title'] = $notification->message;
            $dataNoti['body'] = '';
            $dataNoti['data'] = [];
            $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
//            dd($no);
        }


        $checkClick = ProfileAction::where(['action_by' => $request->id, 'action_to' => Auth::user()->id, 'like' => 1])->first();
        if($checkClick){
            $user =  User::where('id',$request->id)->first();
            $notification = new Notification();
            $notification->message = $user->first_name.' '.$user->last_name.' clicked with you';
            $notification->type = 'click';
            $notification->action_to = $request->id;
            $notification->action_by = Auth::user()->id;;
            $notification->save();

            $fcm = array($user->fcm_token, Auth::user()->fcm_token);
            $dataBody = array();
            $dataBody['title'] = $notification->message;
            $dataBody['message'] = '';
            $dataBody['data'] = [];
            $dataNoti = array();
            $dataNoti['title'] = $notification->message;
            $dataNoti['body'] = '';
            $dataNoti['data'] = [];
            $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
//            dd($no);
        }
        return Helper::jsonResponse(1, 'liked', ['token'=> Auth::user()->token]);
    }
    public function action_dont_like($request, $profileAction){
        $profileAction->action_by = Auth::user()->id;
        $profileAction->action_to = $request->id;
        $profileAction->like = 0;
        $profileAction->dont_like = 1;
        $profileAction->not_sure = 0;
        $profileAction->block = 0;
        $profileAction->save();
        return Helper::jsonResponse(1, 'disliked', ['token'=> Auth::user()->token]);
    }
    public function action_not_sure($request, $profileAction){
        $profileAction->action_by = Auth::user()->id;
        $profileAction->action_to = $request->id;
        $profileAction->like = 0;
        $profileAction->dont_like = 0;
        $profileAction->not_sure = 1;
        $profileAction->block = 0;
        $profileAction->save();
        return Helper::jsonResponse(1, 'not sure', ['token'=> Auth::user()->token]);
    }
    public function action_block($request, $profileAction){
        $profileAction->action_by = Auth::user()->id;
        $profileAction->action_to = $request->id;
        $profileAction->like = 0;
        $profileAction->dont_like = 0;
        $profileAction->not_sure = 0;
        $profileAction->block = 1;
        $profileAction->save();
        return Helper::jsonResponse(1, 'user block successfully', ['token'=> Auth::user()->token]);
    }
}
