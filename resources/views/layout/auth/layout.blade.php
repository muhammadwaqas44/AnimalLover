
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Animal lovers') }} - @yield('title')</title>

    <!-- Core CSS -->
    @include('layout.auth.css')
    @yield('css')
</head>

<body>

@yield('content')

@include('layout.auth.js')

@yield('js')

</body>
</html>
