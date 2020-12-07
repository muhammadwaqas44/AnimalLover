<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @include('layout.dashboard.css')
    @yield('css')
</head>
<body class="adminbody">
<div id="main">
    @include('layout.dashboard.header.header')

    @include('layout.dashboard.header.left-sidebar')

    @yield('content')

    @include('layout.dashboard.footer.footer')

    @include('layout.dashboard.js')

    @yield('script')
</div>
</body>
</html>
