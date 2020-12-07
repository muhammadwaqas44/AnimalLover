<?php

namespace App\Http\Controllers\Admin;

use App\AboutPet;
use App\Alert;
use App\Hobbie;
use App\Http\Controllers\Controller;
use App\Interest;
use App\LikedAllergicAnimal;
use App\ProfileAction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UserDetailController extends Controller
{
    public function userDetails($userId){
        $user = User::where('id', $userId)->with('posts', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal','user_personal_info', 'alerts')->first();

        if($user){
//            User::with('posts', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal')
            return view('dashboard.users.user-detail', compact('user'));
        }
        return redirect()->route('admin-dashboard')->with(['result'=>'error','message'=>'This id does not exists']);
    }

    public function showUserData($userid, $option, Request $request){
        $data = in_array($option, ['posts', 'pets', 'hobbies', 'interests', 'LikedAllergicAnimal', 'alerts','liked-listing', 'dont-like-listing', 'clicked-listing', 'not-sure-listing', 'block-listing'], );

        if(!$data){
            return redirect()->route('admin-dashboard')->with(['result'=>'error','message'=>'option is not correct']);
        }
        $user = User::where('id', $userid)->first();
        if(!$user){
            return redirect()->route('admin-dashboard')->with(['result'=>'error','message'=>'option is not correct']);
        }
        if($user){
            if($option == 'pets'){
                $data = AboutPet::where('user_id', $userid)->orderBy('id', 'DESC')->get();
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->make(true);
                }
                return view('dashboard.users.showdata_pets',['option'=> $option, 'id'=> $user->id]);
            }

            if($option == 'hobbies'){
                $data = Hobbie::where('user_id', $userid)->orderBy('id', 'DESC')->get();
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->make(true);
                }
                return view('dashboard.users.showdata_hobbies',['option'=> $option, 'id'=> $user->id]);
            }

            if($option == 'interests'){
                $data = Interest::where('user_id', $userid)->orderBy('id', 'DESC')->get();
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->make(true);
                }
                return view('dashboard.users.showdata_interests',['option'=> $option, 'id'=> $user->id]);
            }

            if($option == 'LikedAllergicAnimal'){
                $data = LikedAllergicAnimal::where('user_id', $userid)->orderBy('id', 'DESC')->get();
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->make(true);
                }
                return view('dashboard.users.showdata_likedAllargicAnimals',['option'=> $option, 'id'=> $user->id]);
            }

            if($option == 'alerts'){
                $data = Alert::where('created_by', $userid)->orderBy('id', 'DESC')->get();
//                if()
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('status', function (Alert $alert) {
                            if($alert->status == 1){
                                return 'active';
                            }
                            elseif ($alert->status == 0){
                                return 'Not active';
                            }
                        })
                        ->make(true);
                }
                return view('dashboard.users.showdata_alerts',['id'=> $user->id]);
            }

            if($option == 'liked-listing'){
                $list = ProfileAction::where('action_by', $userid)->where('like', 1)->select('action_to','created_at')->get();
                $list = collect($list);
                $data = User::whereIn('id', $list->pluck('action_to'))->with('timeOfAction')->get();

                $data = $data->map(function ($user) {
                    $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                    return $user;
                });
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('full_name', function (User $user) {
                            return $user->first_name.' '.$user->last_name;
                        })
                        ->addColumn('time_of_like', function (User $user) {
                            return $user->timeOfAction->created_at.'   UTC time zone';
                        })
                        ->make(true);
                }
                return view('dashboard.users.showdata_likeListing',['id'=> $user->id]);
            }

            if($option == 'dont-like-listing'){
                $list = ProfileAction::where('action_by', $userid)->where('dont_like', 1)->select('action_to','created_at')->get();
                $list = collect($list);
                $data = User::whereIn('id', $list->pluck('action_to'))->with('timeOfAction')->get();

                $data = $data->map(function ($user) {
                    $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                    return $user;
                });
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('full_name', function (User $user) {
                            return $user->first_name.' '.$user->last_name;
                        })
                        ->addColumn('time_of_like', function (User $user) {
                            return $user->timeOfAction->created_at.'   UTC time zone';
                        })
                        ->make(true);
                }
                return view('dashboard.users.showdata_dontLikeListing',['id'=> $user->id]);
            }

            if($option == 'not-sure-listing'){
                $list = ProfileAction::where('action_by', $userid)->where('not_sure', 1)->select('action_to','created_at')->get();
                $list = collect($list);
                $data = User::whereIn('id', $list->pluck('action_to'))->with('timeOfAction')->get();

                $data = $data->map(function ($user) {
                    $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                    return $user;
                });
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('full_name', function (User $user) {
                            return $user->first_name.' '.$user->last_name;
                        })
                        ->addColumn('time_of_like', function (User $user) {
                            return $user->timeOfAction->created_at.'   UTC time zone';
                        })
                        ->make(true);
                }
                return view('dashboard.users.showdata_notSureListing',['id'=> $user->id]);
            }

            if($option == 'block-listing'){
                $list = ProfileAction::where('action_by', $userid)->where('block', 1)->select('action_to','created_at')->get();
                $list = collect($list);
                $data = User::whereIn('id', $list->pluck('action_to'))->with('timeOfAction')->get();

                $data = $data->map(function ($user) {
                    $user->timeOfAction->human_read_date = $user->timeOfAction->created_at->diffForHumans();
                    return $user;
                });
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('full_name', function (User $user) {
                            return $user->first_name.' '.$user->last_name;
                        })
                        ->addColumn('time_of_like', function (User $user) {
                            return $user->timeOfAction->created_at.'   UTC time zone';
                        })
                        ->make(true);
                }
                return view('dashboard.users.blockListing',['id'=> $user->id]);
            }

            if($option == 'clicked-listing'){
                $likedByMe = ProfileAction::where('action_by', $userid)->where('like', 1)->get();
                $userLikedMe = ProfileAction::whereIn('action_by', $likedByMe->pluck('action_to'))->where('action_to', $userid)->where('like', 1)->get();

                $newArray = [];
                foreach($userLikedMe as $likedMe){
                    foreach($likedByMe as $byMe){
                        if($likedMe->action_by == $byMe->action_to){
                            if($likedMe->created_at > $byMe->created_at){
                                $latestDate = $likedMe->created_at;
                            } else {
                                $latestDate = $byMe->created_at;
                            }
                            $newArray[$byMe->action_to] = $latestDate;
                        }
                    }
                }

                $users = User::whereIn('id',$userLikedMe->pluck('action_by'))->get();

                 $users->map(function ($user) use ($newArray) {
//                dd($newArray);
                    $pushArr = [
                        "created_at" => isset($newArray[$user->id])? $newArray[$user->id]: "",
                        "human_read_date" => isset($newArray[$user->id])? $newArray[$user->id]->diffForHumans(): "",
                    ];
                    $user['timeOfAction'] = $pushArr;
                    return $user;
                });
                 $data = $users;
                if ($request->ajax()) {
                    return DataTables::of($data)
                        ->addColumn('full_name', function (User $user) {
                            return $user->first_name.' '.$user->last_name;
                        })
                        ->addColumn('time_of_like', function (User $user) {
                            return $user->timeOfAction['created_at'].'   UTC time zone';
                        })
                        ->make(true);
                }
                return view('dashboard.users.showdata_clickedList',['id'=> $user->id]);
            }

            dd('not');

        }else{
            return redirect()->route('admin-dashboard')->with(['result'=>'error','message'=>'This id does not exists']);
        }

    }

    public function userDataInTable(){

    }
}
