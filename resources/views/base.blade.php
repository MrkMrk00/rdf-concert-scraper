<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu">
    <title>@yield('title', $title ?? '' ? $title.' - ' : '')@hasSection('title') - @endif Jazzové koncerty</title>

    @vite(['resources/js/app.js'])
    @yield('js')
</head>
<body>
    <x-Navbar />
    <main class="w-full h-full flex flex-row justify-center items-center">@yield('body')</main>
</body>
</html>
