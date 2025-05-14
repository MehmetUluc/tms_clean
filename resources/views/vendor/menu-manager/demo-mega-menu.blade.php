<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mega Menu Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4">
            <div class="flex justify-between items-center">
                <a href="#" class="text-xl font-bold text-primary-600">
                    Travel<span class="text-gray-800">Manager</span>
                </a>
                
                <!-- Include the Mega Menu component here -->
                @include('vendor.menu-manager.templates.mega', [
                    'menu' => $menu,
                    'class' => 'hidden md:flex',
                    'id' => 'main-navigation'
                ])
                
                <!-- Mobile menu toggle button -->
                <button type="button" class="md:hidden text-gray-500" x-data @click="$dispatch('open-mobile-menu')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>
    
    <main class="container mx-auto py-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Mega Menu Demo</h1>
            <p class="text-gray-600 mb-6">
                This page demonstrates the mega menu functionality. Hover over menu items to see the mega menu in action.
            </p>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-medium text-blue-700 mb-2">Menu Details</h3>
                <ul class="list-disc pl-5">
                    <li><strong>Menu Name:</strong> {{ $menu->name }}</li>
                    <li><strong>Menu Location:</strong> {{ $menu->location }}</li>
                    <li><strong>Item Count:</strong> {{ $menu->items->count() }}</li>
                </ul>
            </div>
            
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">How to Use</h2>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <pre class="text-sm overflow-auto">
&lt;x-menu-manager-mega 
    slug="main-menu"
    class="your-custom-class"
    id="your-custom-id"
/&gt;

<!-- OR -->

&lt;x-menu-manager-mega 
    location="header"
    class="your-custom-class"
    id="your-custom-id"
/&gt;
                    </pre>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Travel<span class="text-gray-400">Manager</span></h3>
                    <p class="text-gray-400">Providing high-quality travel management solutions.</p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">About</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Support</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} TravelManager. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Include Mega Menu JS -->
    <script src="{{ asset('vendor/menu-manager/js/mega-menu.js') }}"></script>
</body>
</html>