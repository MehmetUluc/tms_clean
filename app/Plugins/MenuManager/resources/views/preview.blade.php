<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Preview: {{ $menu->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
        }
        
        /* Default Menu Styles */
        .menu-default {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .menu-default li {
            position: relative;
        }
        
        .menu-default li a {
            display: block;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .menu-default li a:hover {
            background-color: #f3f4f6;
        }
        
        .menu-default li.active > a {
            background-color: #e5e7eb;
            font-weight: 600;
        }
        
        .menu-default li.has-children > a:after {
            content: '';
            display: inline-block;
            margin-left: 0.5rem;
            vertical-align: middle;
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        
        .menu-default li ul {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 100;
            display: none;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0;
            list-style: none;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .menu-default li:hover > ul {
            display: block;
        }
        
        .menu-default li ul li ul {
            top: 0;
            left: 100%;
            margin-top: -0.5rem;
        }
        
        /* Mobile Menu */
        .menu-mobile {
            display: none;
        }
        
        @media (max-width: 768px) {
            .menu-default {
                display: none;
            }
            
            .menu-mobile {
                display: block;
            }
            
            .menu-mobile-content {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 75%;
                max-width: 20rem;
                z-index: 50;
                background-color: white;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .menu-mobile-content.open {
                transform: translateX(0);
            }
            
            .menu-mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 40;
                background-color: rgba(0, 0, 0, 0.5);
                display: none;
            }
            
            .menu-mobile-overlay.open {
                display: block;
            }
            
            .menu-mobile ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            
            .menu-mobile li a {
                display: block;
                padding: 0.75rem 1rem;
                color: #374151;
                text-decoration: none;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .menu-mobile li.has-children > a {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .menu-mobile li.has-children > a:after {
                content: '';
                display: inline-block;
                border-top: 0.3em solid;
                border-right: 0.3em solid transparent;
                border-bottom: 0;
                border-left: 0.3em solid transparent;
            }
            
            .menu-mobile li ul {
                display: none;
                background-color: #f3f4f6;
            }
            
            .menu-mobile li.active > ul {
                display: block;
            }
            
            .menu-mobile li ul li a {
                padding-left: 2rem;
            }
            
            .menu-mobile li ul li ul li a {
                padding-left: 3rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-gray-800">
                        Menu Preview
                    </div>
                    
                    <div class="hidden md:block">
                        <!-- Desktop Menu -->
                        <ul class="menu-default">
                            @foreach($menu->items as $item)
                                @php
                                    $hasChildren = !empty($item->children) && $item->children->count() > 0;
                                    $isActive = false; // Determine if active
                                @endphp
                                <li class="{{ $hasChildren ? 'has-children' : '' }} {{ $isActive ? 'active' : '' }}">
                                    <a href="{{ $item->url ?: '#' }}" target="{{ $item->target }}">
                                        @if($item->icon)
                                            <span class="icon">{!! $item->icon !!}</span>
                                        @endif
                                        {{ $item->title }}
                                    </a>
                                    
                                    @if($hasChildren)
                                        <ul>
                                            @foreach($item->children as $child)
                                                @php
                                                    $hasGrandchildren = !empty($child->children) && $child->children->count() > 0;
                                                    $isChildActive = false; // Determine if active
                                                @endphp
                                                <li class="{{ $hasGrandchildren ? 'has-children' : '' }} {{ $isChildActive ? 'active' : '' }}">
                                                    <a href="{{ $child->url ?: '#' }}" target="{{ $child->target }}">
                                                        @if($child->icon)
                                                            <span class="icon">{!! $child->icon !!}</span>
                                                        @endif
                                                        {{ $child->title }}
                                                    </a>
                                                    
                                                    @if($hasGrandchildren)
                                                        <ul>
                                                            @foreach($child->children as $grandchild)
                                                                <li>
                                                                    <a href="{{ $grandchild->url ?: '#' }}" target="{{ $grandchild->target }}">
                                                                        @if($grandchild->icon)
                                                                            <span class="icon">{!! $grandchild->icon !!}</span>
                                                                        @endif
                                                                        {{ $grandchild->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden" x-data="{ open: false }">
                        <button @click="open = true" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Mobile Menu -->
                        <div class="menu-mobile">
                            <div class="menu-mobile-overlay" :class="{ 'open': open }" @click="open = false"></div>
                            
                            <div class="menu-mobile-content" :class="{ 'open': open }">
                                <div class="p-4 border-b flex justify-between items-center">
                                    <div class="font-medium">Menu</div>
                                    <button @click="open = false" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <ul x-data="{
                                    activeItem: null,
                                    toggle(id) {
                                        this.activeItem = this.activeItem === id ? null : id;
                                    }
                                }">
                                    @foreach($menu->items as $item)
                                        @php
                                            $hasChildren = !empty($item->children) && $item->children->count() > 0;
                                            $isActive = false; // Determine if active
                                        @endphp
                                        <li class="{{ $hasChildren ? 'has-children' : '' }} {{ $isActive ? 'active' : '' }}"
                                            :class="{ 'active': activeItem === {{ $item->id }} }">
                                            
                                            @if($hasChildren)
                                                <a href="javascript:void(0);" @click="toggle({{ $item->id }})">
                                                    @if($item->icon)
                                                        <span class="icon">{!! $item->icon !!}</span>
                                                    @endif
                                                    {{ $item->title }}
                                                </a>
                                            @else
                                                <a href="{{ $item->url ?: '#' }}" target="{{ $item->target }}">
                                                    @if($item->icon)
                                                        <span class="icon">{!! $item->icon !!}</span>
                                                    @endif
                                                    {{ $item->title }}
                                                </a>
                                            @endif
                                            
                                            @if($hasChildren)
                                                <ul x-show="activeItem === {{ $item->id }}">
                                                    @foreach($item->children as $child)
                                                        @php
                                                            $hasGrandchildren = !empty($child->children) && $child->children->count() > 0;
                                                            $isChildActive = false; // Determine if active
                                                        @endphp
                                                        <li class="{{ $hasGrandchildren ? 'has-children' : '' }} {{ $isChildActive ? 'active' : '' }}">
                                                            <a href="{{ $child->url ?: '#' }}" target="{{ $child->target }}">
                                                                @if($child->icon)
                                                                    <span class="icon">{!! $child->icon !!}</span>
                                                                @endif
                                                                {{ $child->title }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <main class="container mx-auto px-4 py-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold mb-4">{{ $menu->name }}</h1>
                <p class="text-gray-600 mb-6">This is a preview of the menu. The styling may differ from your actual site.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h2 class="text-lg font-medium mb-3">Menu Information</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Menu ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->location ?? 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->is_active ? 'Active' : 'Inactive' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Item Count</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $menu->allItems->count() }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-lg font-medium mb-4">Menu Structure</h2>
                    
                    <div class="bg-gray-100 p-4 rounded-lg overflow-auto">
                        <pre class="text-xs">{{ json_encode($menu->items->toArray(), JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-lg font-medium mb-4">Usage in Blade</h2>
                    
                    <div class="bg-gray-800 text-white p-4 rounded-lg overflow-auto">
                        <code class="text-xs">
                            &lt;x-menu-manager::menu slug="{{ $menu->slug }}" /&gt;
                            <br><br>
                            &lt;!-- Or by location --&gt;<br>
                            &lt;x-menu-manager::menu location="{{ $menu->location ?? 'header' }}" /&gt;
                            <br><br>
                            &lt;!-- With custom class --&gt;<br>
                            &lt;x-menu-manager::menu slug="{{ $menu->slug }}" class="my-custom-menu" /&gt;
                        </code>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>