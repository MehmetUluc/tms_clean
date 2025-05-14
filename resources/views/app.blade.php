<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TravelManager') }}</title>
        <meta name="description" content="Book your perfect stay with TravelManager - the best hotel booking platform">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" href="/favicon.ico" />
        
        <!-- Fallback CSS - Will always load regardless of Vite -->
        <link rel="stylesheet" href="http://localhost:8000/css/inertia-theme.css">

        <!-- Scripts and Styles -->
        @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        <link rel="stylesheet" href="http://localhost:8000/build/{{ $manifest['resources/css/app.css']['file'] }}">
        <script type="module" src="http://localhost:8000/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-gray-50">
        @inertia
    </body>
</html>