{{--@component('mail::message')--}}
{{--    <div>--}}
{{--        <p>Please use this token to reset your password</p>--}}

{{--        <h1> {{$data}} </h1>--}}
{{--    </div>--}}


{{--@endcomponent--}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Document</title>

</head>
<body style="font-family: Arial;">
<div style="">
    <div
        style="background-color:#fbf4f4;max-width: 600px;width:100%;margin:0 auto;"
    >
        <header style="padding: 20px 50px;">
            <div style="display: flex;justify-content: space-between;">
                <p>Please use this token to reset your password</p>
            </div>
            <div style="display: flex;text-align: center;width: 100%">
                <p><h1> {{$data}} </h1></p>
            </div>
        </header>
    </div>
    <!-- -------------- -->
    <div
        style="width: 100%;text-align:center;color:#fff;background-color: #d02619;padding: 10px 0px;max-width: 600px;margin: 0 auto;"
    >
        Copyright @ 2020. All rights reserved.
    </div>
</div>
</div>
</body>
</html>
