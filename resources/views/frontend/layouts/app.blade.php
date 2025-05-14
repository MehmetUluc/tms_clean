<\!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Travel Manager') }}</title>
    
    <\!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <\!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
          }
        }
      }
    </script>
    
    <\!-- Styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 min-h-screen flex flex-col">
    <\!-- Header -->
    @include('frontend.components.header')
    
    <\!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>
    
    <\!-- Footer -->
    @include('frontend.components.footer')
    
    <\!-- Scripts -->
    @stack('scripts')
</body>
</html>
