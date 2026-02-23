<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta charset="UTF-8">
    <link rel="icon" href="{{ secure_asset('/favicon.ico') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ITS | Informasi Banjir
    </title>

</head>

<body class="bg-[#F8FCFF]">
    @include('partials/navbar')
    @yield('container')
    @include('partials/footer')
</body>

</html>
