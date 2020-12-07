@extends('layout.dashboard.dashboard')

@section('title')
    Edit User
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
                <!-- end row -->

                <div class="row">

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
                                                        <input class="form-control" name="first_name" type="text" value="{{$user->first_name}}" required="">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Last Name (required)</label>
                                                        <input class="form-control" name="last_name" type="text" value="{{$user->last_name}}" required="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Password (leave empty not to change)</label>
                                                        <input class="form-control" name="password" type="password" value="{{$user->password}}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Phone Number</label>
                                                        <input class="form-control" name="phone" type="text" value="{{$user->phone}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="userid" value="{{$user->id}}">
                                            <input type="hidden" name="user_personal_infoid" value="{{$user->user_personal_info->id}}">




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
                                                <input type="file" name="profile_image" class="form-control" id="profile_img">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Email (required)</label>
                                                <input class="form-control" name="email" type="email" value="{{$user->email}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Username (required)</label>
                                                <input class="form-control" name="username" type="text" value="{{$user->username}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>City (required)</label>
                                                <input class="form-control" name="city" type="text" value="{{$user->city}}" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>State (required)</label>
                                                <input class="form-control" name="state" type="state" value="{{$user->state}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Zipcode (required)</label>
                                                <input class="form-control" name="zipcode" type="text" value="{{$user->zipcode}}" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Gender (required)</label>
                                                <input class="form-control" name="gender" type="text" value="{{$user->user_personal_info->gender}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Age (required)</label>
                                                <input class="form-control" name="age" type="number" value="{{$user->user_personal_info->age}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Occupation (required)</label>
                                                <input class="form-control" name="accupation" type="text" value="{{$user->user_personal_info->occupation}}" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Relationship status (required)</label>
                                                <input class="form-control" name="relationship_status" type="text" value="{{$user->user_personal_info->relationship_status}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Interested  (required)</label>
                                                <input class="form-control" name="interested_animal" type="number" value="{{$user->user_personal_info->interested_animal}}" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>About Me (required)</label>
                                                <input class="form-control" name="about_me" type="text" value="{{$user->user_personal_info->about_me}}" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label>Interested Animals (required)</label>
                                                <input class="form-control" name="interested_animal" type="text" value="{{$user->user_personal_info->interested_animal}}" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 float-left">
                                            <button type="button" id="btn_edit_profile" class="btn btn-primary btn-chng1">Edit profile</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <!-- end card-body -->

                        </div>
                        <!-- end card -->

                    </div>
                    <!-- end col -->

                </div>
            </div>
            <!-- END container-fluid -->

        </div>
        <!-- END content -->

    </div>
@endsection

@section('script')
    <script>
        $("#profile_img").change(function () {
            readiconURL(this,this.id);
        });
        function readiconURL(input,id) {
            if (input.files && input.files[0]) {
                var fileName = input.files[0].name;
                var filesize = input.files[0].size;
                var extention = fileName.split('.').pop().toLowerCase();
                if(extention == 'jpg' || extention == 'jpeg' || extention == 'png')
                {
                    if (filesize < 5000000){
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            console.log(e.target.result);
                            $('#profile_image_preview').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }else{
                        $('#'+id).val('');
                        window.toastr.error("Please upload a file less then 5MB");
                    }
                }else{
                    $('#'+id).val('');
                    window.toastr.error("Please upload only jpg/jpeg or png file");
                }
            }
        }

        $(document).ready(function(){
            $("#btn_edit_profile").click(function(e){
                e.preventDefault();
                var formdata=new FormData($('#update-profile-form')[0]);
                $.blockUI({
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }
                });

                $.ajax({

                    type: 'POST',
                    url: "{{route('edit-profile-post')}}",
                    data:formdata,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response, status) {

                        if (response.result == 'success') {

                            $.unblockUI();
                            window.toastr.success(response.message);
                            window.location.reload();

                        } else if (response.result == 'error') {
                            $.unblockUI();
                            window.toastr.error(response.message);
                        }
                    },
                    error: function (data) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            $.unblockUI();
                            errorMsg(value);
                        });


                    }

                });
            });
        });

    </script>

@endsection


