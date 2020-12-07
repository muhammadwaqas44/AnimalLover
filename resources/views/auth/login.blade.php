@extends('layout.auth.layout')

@section('title', "Login")

@section('content')

    <div class="container h-100">
        <div class="row align-items-center justify-content-center m-2">
{{--            <img src="{{asset('dashboard_assets/images/F&G.png')}}" alt="">--}}
            Animal lovers logo
        </div>
        <div class="row h-100 justify-content-center align-items-center">
            <div class="card" style="width: 350px; padding-bottom: 20px;">
                <h4 class="card-header" style="background-color: #002e5b !important; color: white;">Login</h4>

                <div class="card-body">

                    <form data-toggle="validator" role="form" id="sign-in-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Login Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                  style="background-color: #B7A240!important; color:white"><i
                                                    class="far fa-envelope-open"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email"
                                               data-error="Input valid email" required>
                                    </div>
                                    <div class="help-block with-errors text-danger"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              style="background-color: #B7A240 !important;color:white"><i
                                                class="fa fa-unlock"
                                                aria-hidden="true"></i></span>
                                        </div>
                                        <input type="password" id="inputPassword" data-minlength="6"
                                               name="password"
                                               class="form-control" data-error="Password to short" required>
                                    </div>
                                    <div class="help-block with-errors text-danger"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="checkbox checkbox-primary">
                                <input id="checkbox_remember" type="checkbox" name="remember">
                                <label for="checkbox_remember"> Remember me</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                {{--                                <input type="hidden" name="redirect" value="">--}}
                                <input type="submit" class="btn btn-primary btn-lg btn-block"
                                       style="background-color: #002e5b!important; border-color:#002e5b; color:white"
                                       value="Login" name="submit">
                            </div>
                        </div>
                    </form>

                    <div class="clear"></div>

                    <i class="fa fa-undo fa-fw"></i> Forgot password? <a href="">Reset
                        password</a>

                </div>

            </div>

        </div>
    </div>

@endsection

@section('js')
    <script>
        $('form#sign-in-form').on('submit', function (e) {
            e.preventDefault();
            var form = $('form#sign-in-form').serialize();
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
                url: "{{route('login-post')}}",
                data: form,
                success: function (response, status) {
                    if (response.result == 'success') {
                        $.unblockUI();
                        window.toastr.success(response.message);
                        // window.location.reload();
                        window.location.href="{{route('admin-dashboard')}}";
                    } else if (response.result == 'error') {
                        $.unblockUI();
                        window.toastr.error(response.message);
                    }
                },
                error: function (data) {
                    $.each(data.responseJSON.errors, function (key, value) {
                        $.unblockUI();
                        window.toastr.error(value);
                    });
                }
            });
        });

    </script>
@endsection
