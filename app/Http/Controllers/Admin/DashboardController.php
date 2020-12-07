<?php

namespace App\Http\Controllers\Admin;

use App\AboutMe;
use App\FeedBack;
use App\Http\Controllers\Controller;
use App\Services\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = User::with('role')->orderBy('id', 'DESC')->get();

            return DataTables::of($data)
                ->addColumn('full_name', function (User $user) {
                    return $user->first_name.' '.$user->last_name;
                })
                ->addColumn('action', function (User $user) {

//                    $btn = '<a href="' . route('edit-company', ['companyId' => $user->id]) . '"
                    $btn = '
                    <a href="'. route('user-detail', ['userId' => $user->id])  .'"
                    class="btn btn-primary btn-sm viw-btn-sh">View</a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.dashboard');
    }

    public function editProfile($userId){
        $user = User::where('id',$userId)->first();
        if($user){
            return view('dashboard.users.edit-profile',compact('user'));
        }
        return redirect()->route('admin-dashboard')->with(['result'=>'error','message'=>'This id does not exists']);
    }

    public function editProfile_post(Request $request){

        $errorMessage = [
            'first.required' => 'Title  is required',
            'gender.required' => 'Gender  is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'phone' => 'numeric|nullable',
                'email' => 'email|nullable',
                'password' => 'min:6|nullable',
                'username' => 'unique:users|nullable',
                'zipcode' => 'numeric|nullable',
                'age' => 'numeric|nullable',
                'gender' => 'required|in:male,Male,female,Female',
            ],$errorMessage
        );
        if ($validator->fails()){
            return response()->json(['result' => 'error', 'message' => $validator->messages()->first()]);
        }

        $user = User::where('id', $request->userid)->first();
        if($user){
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->zip_code = $request->zipcode;
            if (!empty($request->profile_image)) {
                $file = $request->profile_image;
                $fileName = $file->getClientOriginalName();
                $fileSize = ($file->getSize()) / 1000;    //Size in kb
                $explodeImage = explode('.', $fileName);
                $fileName = $explodeImage[0];
                $extension = end($explodeImage);
                $fileName = time() . "-" . $fileName . "." . $extension;
                $imageExtensions = ['jpg', 'jpeg', 'png'];

                if (in_array($extension, $imageExtensions)) {
                    $folderName = "project-assets/images/";
                    $file->move($folderName, $fileName);
                    unlink(public_path('project-assets/images/'.$user->profile_image));
                    $user->profile_image = $fileName;
                } else {
                    response()->json(['result' => 'error', 'message' => 'Upload image for profile image']);
                }
            }

            $user->save();
            $aboutme = AboutMe::where('id', $request->user_personal_infoid)->first();
            if($aboutme){
                $aboutme->gender = $request->gender;
                $aboutme->relationship_status = $request->relationship_status;
                $aboutme->interested_animal = $request->interested_animal;
                $aboutme->about_me = $request->about_me;
                $aboutme->interested_animal = $request->interested_animal;
                $aboutme->save();
            }
        }

        return response()->json(['result' => 'success', 'message' => 'Profile update succesfully']);







    }

    public function chat(){
        return view('dashboard.chat');
    }

    public function feedbacks(Request $request){
        if ($request->ajax()) {
            $data = FeedBack::with('user')->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addColumn('full_name', function (FeedBack $feedBack) {
                    return $feedBack->user->first_name.' '.$feedBack->user->last_name;
                })
                ->make(true);
        }
        return view('dashboard.users.feedbacks');
    }


}
