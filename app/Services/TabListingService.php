<?php


namespace App\Services;


use App\Conversation;
use App\ProfileAction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TabListingService
{

    public function getList($request){
        if($request->list == 'like'){
            $list = ProfileAction::where('action_by', Auth::user()->id)->where('like', 1)->select('action_to','created_at')->get();
            $list = collect($list);
            $users = User::whereIn('id', $list->pluck('action_to'))->with('user_personal_info','pets','hobbies','interests','LikedAllergicAnimal','timeOfAction' )->orderBy('id', 'DESC')->get();

            $users->map(function ($user) {
                $user->profile_image = asset('/project-assets/images/'.$user->profile_image);
                $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                $user->timeOfAction->time_as_user = $user->timeOfAction->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                return $user;
            });
            return Helper::jsonResponse(1,'list of liked user', $users);
        }

        if($request->list == 'dont_like'){
            $list = ProfileAction::where('action_by', Auth::user()->id)->where('dont_like', 1)->select('action_to','created_at')->get();
            $list = collect($list);
            $users = User::whereIn('id', $list)->with('user_personal_info','pets','hobbies','interests','LikedAllergicAnimal','timeOfAction')->orderBy('id', 'DESC')->get();
            $users->map(function ($user) {
                $user->profile_image = asset('/project-assets/images/'.$user->profile_image);
                $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                $user->timeOfAction->time_as_user = $user->timeOfAction->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                return $user;
            });
            return Helper::jsonResponse(1,'list of dont liked user', $users);
        }

        if($request->list == 'not_sure'){
            $list = ProfileAction::where('action_by', Auth::user()->id)->where('not_sure', 1)->select('action_to','created_at')->get();
            $list = collect($list);
            $users = User::whereIn('id', $list)->with('user_personal_info','pets','hobbies','interests','LikedAllergicAnimal','timeOfAction')->orderBy('id', 'DESC')->get();
            $users->map(function ($user) {
                $user->profile_image = asset('/project-assets/images/'.$user->profile_image);
                $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                $user->timeOfAction->time_as_user = $user->timeOfAction->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                return $user;
            });
            return Helper::jsonResponse(1,'list of not sure user', $users);
        }

        if($request->list == 'block'){
            $list = ProfileAction::where('action_by', Auth::user()->id)->where('block', 1)->select('action_to','created_at')->get();
            $list = collect($list);
            $users = User::whereIn('id', $list)->with('user_personal_info','pets','hobbies','interests','LikedAllergicAnimal', 'timeOfAction')->orderBy('id', 'DESC')->get();

            $users->map(function ($user) {
                $user->profile_image = asset('/project-assets/images/'.$user->profile_image);
                $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                $user->timeOfAction->time_as_user = $user->timeOfAction->created_at->timeZone(Auth::user()->time_zone)->toDayDateTimeString();
                return $user;
            });
            return Helper::jsonResponse(1,'list of block user', $users);
        }

        if($request->list == 'clicked'){
            $likedByMe = ProfileAction::where('action_by', Auth::user()->id)->where('like', 1)->get();

            $userLikedMe = ProfileAction::whereIn('action_by', $likedByMe->pluck('action_to'))->where('action_to', Auth::user()->id)->where('like', 1)->get();

            $newArray = [];
            foreach($userLikedMe as $likedMe){
                foreach($likedByMe as $byMe){
                    if($likedMe->action_by == $byMe->action_to){
                        if($likedMe->created_at > $byMe->created_at){
                            $latestDate = $likedMe->created_at;
                        } else {
                            $latestDate = $byMe->created_at;
                        }
                        $newArray[$byMe->action_to] = $latestDate->timeZone(Auth::user()->time_zone);
                    }
                }
            }


            $users = User::whereIn('id', $userLikedMe->pluck('action_by'))->with('user_personal_info','pets','hobbies','interests','LikedAllergicAnimal')->orderBy('id', 'DESC')->get();

            $users->map(function ($user) use ($newArray) {
                $user->profile_image = asset('/project-assets/images/'.$user->profile_image);

//                $conversation = Conversation::where(['sender_id'=> $user->id,'receiver_id' => Auth::id()])->orWhere(['sender_id'=> Auth::id(),'receiver_id'=> $user->id])->toSql();
                $conversation = Conversation::where(['sender_id'=> $user->id,'receiver_id' => Auth::id()])->orWhere(function($u) use ($user){
                    $u->where(['sender_id'=> Auth::id(),'receiver_id'=> $user->id]);
                })->first();

                if(!$conversation){
                    $user['conversation_id'] = null;
                }else{
                    $user['conversation_id'] = $conversation->id;
                }

                $pushArr = [
                    "created_at" => isset($newArray[$user->id])? $newArray[$user->id]->toDayDateTimeString(): "",
                    "human_read_date" => isset($newArray[$user->id])? $newArray[$user->id]->diffForHumans(): "",
                ];
                $user['timeOfAction'] = $pushArr;
                return $user;
            });
            return Helper::jsonResponse(1,'list of clicked user', $users);
        }

        return Helper::jsonResponse(1,'No any list found.', []);

    }

//    public function listFormatterData($user){
//        return [
//          'id' =>  $user->id,
//            'service_id' => $user->service_id,
//
//
//        ];
//    }

}
