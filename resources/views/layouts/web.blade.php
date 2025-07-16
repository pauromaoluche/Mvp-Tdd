<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVP TDD</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body class="bg-gradient-to-b from-gray-900 to-black text-white min-h-screen flex items-center justify-center">
    <main>
        @yield('content')
    </main>
    @livewireScripts
    @vite('resources/js/app.js')
</body>

</html>
