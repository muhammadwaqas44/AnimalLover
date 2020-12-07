@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')
@section('css')
    <style>
        .tab {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
        }
        .tab a {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 17px;
            color: black;
        }
        .tab a:hover {
            background-color: #ddd;
        }
        .tab a.active {
            background-color: #ccc;
        }
    </style>
@endsection
@section('content')
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="breadcrumb-holder">
                            <h1 class="main-title float-left">Dashboard</h1>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item">Home</li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>


                <div class="tab">
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'posts'])}}">Posts &nbsp;&nbsp;<small>{{$user->posts->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'pets'])}}">Pets  &nbsp;&nbsp;<small>{{$user->pets->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'hobbies'])}}">Hobbies  &nbsp;&nbsp;<small>{{$user->hobbies->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'interests'])}}">Interests  &nbsp;&nbsp;<small>{{$user->interests->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'LikedAllergicAnimal'])}}">Liked Allergic Animals &nbsp;&nbsp; <small>{{$user->LikedAllergicAnimal->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'alerts'])}}">Alerts &nbsp;&nbsp; <small>{{$user->alerts->count()}}</small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'liked-listing'])}}">Like list &nbsp;&nbsp; <small></small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'dont-like-listing'])}}">Dont like list &nbsp;&nbsp; <small></small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'clicked-listing'])}}">Clicked list &nbsp;&nbsp; <small></small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'not-sure-listing'])}}">Not sure list &nbsp;&nbsp; <small></small></a>
                    <a class="tablinks" href="{{route('show-user-data', ['userid' => $user->id,'option' =>'block-listing'])}}">Block list &nbsp;&nbsp; <small></small></a>
                </div>
                <div class="row mt-4">

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3><i class="fa fa-user"></i> Profile details</h3>
                            </div>

                            <div class="card-body">


                                <form id="update-profile-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">

                                        <div class="col-lg-9 col-xl-9">

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>First Name (required)</label>
                                                        <input class="form-control" name="first_name" type="text" value="{{$user->first_name}}" required="" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Last Name (required)</label>
                                                        <input class="form-control" name="last_name" type="text" value="{{$user->last_name}}" required="" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Password (leave empty not to change)</label>
                                                        <input class="form-control" name="password" type="password" value="{{$user->password}}" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Phone Number</label>
                                                        <input class="form-control" name="phone" type="text" value="{{$user->phone}}" disabled>
                                                    </div>
                                                </div>
                                            </div>

{{--                                            <input type="hidden" name="userid" value="{{$user->id}}">--}}
{{--                                            <input type="hidden" name="user_personal_infoid" value="{{$user->user_personal_info->id}}">--}}




                                        </div>
                                        <div class="col-lg-3 col-xl-3 border-left">


                                            <div class="m-b-10"></div>

                                            <div id="avatar_image" class="float-left">
                                                <input type="image" class="float-left" id="profile_image_preview" style="max-width:100px; height:auto;"

                                                       src="{{isset($user->profile_image) ? asset('project-assets/images/'.$user->profile_image) : asset('dashboard_assets/images/avatars/admin.png')}}">

                                                <span class="float-left ml-2 w-50">{{$user->role->name}}</span>
                                                <span class="float-left ml-2 w-50">  status:  {{$user->status = 1 ? 'Active' : ($user->status = 0 && 'Deactive')}}</span>
                                            </div>

                                            <div class="form-group mt-3 float-left">
                                                <i class="far fa-trash-alt"></i> <span class="delete_image" href="#">Change profile pic</span>
                                                <input type="file" name="profile_image" class="form-control" id="profile_img" disabled>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Email (required)</label>
                                                <input class="form-control" name="email" type="email" value="{{$user->email}}" required="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Username (required)</label>
                                                <input class="form-control" name="username" type="text" value="{{$user->username}}" required="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>City (required)</label>
                                                <input class="form-control" name="city" type="text" value="{{$user->city}}" required="" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>State (required)</label>
                                                <input class="form-control" name="state" type="state" value="{{$user->state}}" required="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Zipcode (required)</label>
                                                <input class="form-control" name="zipcode" type="text" value="{{$user->zipcode}}" required="" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Gender (required)</label>
                                                <input class="form-control" name="gender" type="text" value="{{isset($user->user_personal_info->gender) ? $user->user_personal_info->gender : ''}}" required="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Age (required)</label>
                                                <input class="form-control" name="age" type="number" value="{{isset($user->user_personal_info->age) ? $user->user_personal_info->age : ''}}" required="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Occupation (required)</label>
                                                <input class="form-control" name="accupation" type="text" value="{{isset($user->user_personal_info->occupation) ? $user->user_personal_info->occupation : ''}}" disabled required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Relationship status (required)</label>
                                                <input class="form-control" name="relationship_status" disabled type="text" value="{{isset($user->user_personal_info->relationship_status) ? $user->user_personal_info->relationship_status : ''}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Interested  (required)</label>
                                                <input class="form-control" name="interested_animal" type="number" disabled value="{{isset($user->user_personal_info->interested_animal) ? $user->user_personal_info->interested_animal : ''}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>About Me (required)</label>
                                                <input class="form-control" name="about_me" type="text" disabled value="{{isset($user->user_personal_info->about_me) ? $user->user_personal_info->about_me : ''}}" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Interested Animals (required)</label>
                                                <input class="form-control" name="interested_animal" type="text" disabled value="{{isset($user->user_personal_info->interested_animal) ? $user->user_personal_info->interested_animal : ''}}" required="">
                                            </div>
                                        </div>
                                    </div>

{{--                                    <div class="row">--}}
{{--                                        <div class="col-lg-12 float-left">--}}
{{--                                            <button type="button" id="btn_edit_profile" class="btn btn-primary btn-chng1">Edit profile</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </form>

                            </div>
                            <!-- end card-body -->

                        </div>
                        <!-- end card -->

                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- END container-fluid -->

        </div>
        <!-- END content -->
    </div>
@endsection





