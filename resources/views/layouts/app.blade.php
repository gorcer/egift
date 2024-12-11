<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    @vite('resources/css/app.css') {{-- Подключение стилей через Vite --}}
</head>
<body class="h-screen w-screen overflow-hidden">
<div class="relative h-full w-full">
    {{-- Фоновое изображение --}}
    <div class="absolute inset-0">
        <img src="{{ asset('/img/bg.jpg') }}" alt="Background" class="h-full w-full object-cover">
    </div>

    {{-- Панель контента поверх изображения --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="bg-white/80 shadow-lg p-8 rounded-lg">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
