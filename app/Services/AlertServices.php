<?php

namespace App\Services;

use App\Alert;
use App\AlertGallery;
use App\Conversation;
use App\Notification;
use App\User;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AlertServices
{

    public function create_alert($request){
        $errorMessage = [
            'title.required' => 'Title  is required',
            'description.required' => 'description is required',
            'lat.required' => 'latitude is required',
            'long.required' => 'longitude is required',
            'type.required' => 'Type is required',
            'image.max' => 'Please images upto 6'
        ];
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'type' => 'required|string|in:adoption,lost,found',
                'image' => 'array||max:6',
                "image.*"  => "string|distinct",
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }

        $alert = new Alert();
        $alert->title = $request->title;
        $alert->description = $request->description;
        $alert->lat = $request->lat;
        $alert->long = $request->long;
        $alert->type = $request->type;
        $alert->created_by = Auth::user()->id;
         $alert->save();

        if(!$alert){
            return Helper::jsonResponse(0, '','','Can not create alert');
        }
        if($request->image){
            foreach ($request->image as $image){
                $s = new AlertGallery();
                $s->name = $image;
                $s->alert_id = $alert->id;
                $s->user_id = Auth::user()->id;
                $s->save();
            }
        }
        $circle_radius = 3958.748;  // for miles
        $mile = "20";
        $haverClause = '(' . $circle_radius . ' * acos(cos(radians(' . $request->lat . ')) * cos(radians(users.lat)) * cos(radians(users.long)- radians(' . $request->long . ') )+sin(radians(' . $request->lat . ')) * sin(radians(users.lat))))';
        $users = User::select('users.*', DB::raw($haverClause . ' AS distance'))
            ->whereRaw($haverClause . ' <? ', [$mile])->get();
        $users = $users->pluck('id');


        if($request->type == 'adoption'){
            foreach ($users as  $user ){
                $noti = new Notification();
                $noti->message = Auth::user()->first_name.' '. Auth::user()->last_name .' reported pets to be adopted';
                $noti->type = $alert->type;
                $noti->action_to = $user;
                $noti->action_by = Auth::user()->id;
                $noti->save();

                $fcm = array($user->fcm_token);
                $dataBody = array();
                $dataBody['title'] = $noti->message;
                $dataBody['message'] = '';
                $dataBody['data'] = [];
                $dataNoti = array();
                $dataNoti['title'] = $noti->message;
                $dataNoti['body'] = '';
                $dataNoti['data'] = [];
                $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
            }
        }
        if($request->type == 'lost' || $alert->type == 'found'){
            foreach ($users as $user ){
                $noti = new Notification();
                $noti->message = Auth::user()->first_name.' '. Auth::user()->last_name .' reported lost/found pet in your area';
                $noti->type = $alert->type;
                $noti->action_to = $user;
                $noti->action_by = Auth::user()->id;
                $noti->save();

                $fcm = array($user->fcm_token);
                $dataBody = array();
                $dataBody['title'] = $noti->message;
                $dataBody['message'] = '';
                $dataBody['data'] = [];
                $dataNoti = array();
                $dataNoti['title'] = $noti->message;
                $dataNoti['body'] = '';
                $dataNoti['data'] = [];
                $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
            }
        }

        return Helper::jsonResponse(1, 'Alert has been created',$alert,'');

    }

    public function alerts($request){
        $errorMessage = [
            'type.required' => 'Type is required',
            'lat.required' => 'Lat is required',
            'long.required' => 'Long is required',
            'distance.required' => 'Distance is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'type' => 'required|string|in:adoption,lost,found',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'distance' => 'required|numeric'
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }

        $lat = $request->lat;
        $lng = $request->long;
        $circle_radius = 3959;  //miles
//        $circle_radius = 6371; // km
        $max_distance = $request->distance;
        $haverClause = '(' . $circle_radius . ' * acos(cos(radians(' . $lat . ')) * cos(radians(alerts.lat)) * cos(radians(alerts.long)-
        radians(' . $lng . ') )+sin(radians(' . $lat . ')) * sin(radians(alerts.lat))))';

            $alerts = Alert::select('alerts.*', DB::raw($haverClause . ' AS distance'))->where('status',1)
                ->whereRaw($haverClause .'<? ', [$max_distance])
                ->where('type','=',$request->type)->where('created_by', '!=',Auth::user()->id )->with('alerts_images', 'user:id,email,first_name,last_name')->orderBy('id', 'DESC')->get();

            $my_alerts = Alert::select('alerts.*', DB::raw($haverClause . ' AS distance'))->where('status',1)
               ->whereRaw($haverClause .'<? ', [$max_distance])
                ->where('type','=',$request->type)->where('created_by', '=',Auth::user()->id )->with('alerts_images', 'user:id,email,first_name,last_name')->orderBy('id', 'DESC')->get();

            $alerts->map(function ($alert) {
                $alert->user_fullname = $alert->user->first_name.' '.$alert->user->last_name;
                $alert->user_id = $alert->user->id;
                $alert->user_email = $alert->user->email;
                $alert->user_image = asset('/project-assets/images/'.$alert->user->profile_image);
                $alert->time_1 = $alert->created_at->timeZone(Auth::user()->time_zone)->diffForHumans();
                $alert->time_2 = $alert->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                $alert->distance = round($alert->distance, 2).' iKM';
                $alert->alert_type = 'other';

                $conversation = Conversation::where(['sender_id'=> $alert->created_by,'receiver_id' => Auth::id()])->orWhere(function($u) use ($alert){
                    $u->where(['sender_id'=> Auth::id(),'receiver_id'=> $alert->created_by]);
                })->first();
                if(!$conversation){
                    $alert->conversation_id = null;
                }else{
                    $alert->conversation_id = $conversation->id;
                }
                return $alert;
            });
            $my_alerts->map(function ($alert) {
                $alert->user_fullname = $alert->user->first_name.' '.$alert->user->last_name;
                $alert->user_id = $alert->user->id;
                $alert->user_email = $alert->user->email;
                $alert->user_image = asset('/project-assets/images/'.$alert->user->profile_image);
                $alert->time_1 = $alert->created_at->timeZone(Auth::user()->time_zone)->diffForHumans();
                $alert->time_2 = $alert->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                $alert->distance = round($alert->distance, 2).' in KM';
                $alert->alert_type = 'my alerts';
                return $alert;
            });

            $all = $my_alerts->concat($alerts);
            return Helper::jsonResponse(1, 'All alerts',$all,'');
//        }
    }

    public function delete_alert($request){
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
        $alert = Alert::where('id', $request->id)->first();
        if(!$alert){
            return Helper::jsonResponse(0, '','','Can not delete alert');
        }
        if($alert->created_by !== Auth::user()->id){
            return Helper::jsonResponse(0, '','','Not authorize delete alert');
        }
        if($alert->status == 0){
            return Helper::jsonResponse(0, '','','Alert already has been resolved');
        }
        $alert->status = 0;
        $alert->save();
//        AlertGallery::where('alert_id', $alert->id)->delete();
        return Helper::jsonResponse(1, 'Alert has been resolved','','');

    }

    public function delete_alert_image($request){
        $errorMessage = [
            'alert_id.required' => 'Alert id  is required',
            'image_id.required' => 'Image id is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'alert_id' => 'required|numeric',
                'image_id' => 'required|numeric'
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }
        $alert = Alert::where('id', $request->alert_id)->first();
        if(!$alert){
            return Helper::jsonResponse(0, '','','Alert not found');
        }
        if($alert->created_by != Auth::user()->id){
            return Helper::jsonResponse(0, '','','Not authorize edit alert');
        }
        AlertGallery::where('alert_id', $alert->id)->where('id', $request->image_id)->delete();
        return Helper::jsonResponse(1, 'Image removed','','');

    }

    public function edit_alert($request){

        $errorMessage = [
            'title.required' => 'Title  is required',
            'description.required' => 'description is required',
            'lat.required' => 'Lat is required',
            'long.required' => 'Long is required',
            'id.required' => 'id is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'id' => 'required|numeric',
            ],$errorMessage
        );
        if ($validator->fails()){
            return Helper::jsonResponse(0, 'error','',$validator->messages()->first());
        }
        $alert = Alert::where('id', $request->id)->first();
        if($alert){
            if($alert->created_by != Auth::user()->id){
                return Helper::jsonResponse(0, '','','Not authoirzed');
            }
            $alert->title = $request->title;
            $alert->description = $request->description;
            $alert->lat = $request->lat;
            $alert->long = $request->long;
            $alert->save();
            if($request->image){
                foreach ($request->image as $image){
                    $s = new AlertGallery();
                    $s->name = $image;
                    $s->alert_id = $alert->id;
                    $s->user_id = Auth::user()->id;
                    $s->save();
                }
            }

            return Helper::jsonResponse(1, 'Alert edited successfully','','');
        }

        return Helper::jsonResponse(0, '','','Alert with this id not exists');

    }

}
