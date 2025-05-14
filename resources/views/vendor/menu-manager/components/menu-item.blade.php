<div class="menu-item bg-white border rounded-lg mb-2 shadow-sm" data-item-id="{{ $item['id'] }}">
    <div class="p-3 flex items-center justify-between">
        <div class="flex items-center">
            <div class="drag-handle p-2 mr-2 text-gray-400 hover:text-gray-600 cursor-move">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            
            @if(!empty($item['children']))
                <button 
                    id="toggle-button-{{ $item['id'] }}" 
                    type="button" 
                    class="mr-2 text-gray-500 hover:text-gray-700"
                    @click="toggleChildren({{ $item['id'] }})"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            @else
                <div class="w-5 mr-2"></div>
            @endif
            
            <div class="flex flex-col">
                <span class="font-medium">{{ $item['title'] }}</span>
                <span class="text-xs text-gray-500">
                    @if($item['link_type'] === 'url')
                        {{ \Illuminate\Support\Str::limit($item['url'], 30) }}
                    @elseif($item['link_type'] === 'route')
                        Route: {{ $item['route_name'] }}
                    @elseif($item['link_type'] === 'model')
                        {{ $item['model_type'] ? class_basename($item['model_type']) : '' }} #{{ $item['model_id'] }}
                    @endif
                </span>
            </div>
            
            @if(!empty($item['is_featured']))
                <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs rounded-full">Featured</span>
            @endif
            
            @if(!empty($item['is_mega_menu']))
                <span class="ml-2 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs rounded-full">Mega Menu</span>
            @endif
            
            @if(empty($item['is_active']))
                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-800 text-xs rounded-full">Inactive</span>
            @endif
        </div>
        
        <div class="flex items-center space-x-1">
            <a 
                href="#edit-item-{{ $item['id'] }}" 
                class="p-1 text-primary-600 hover:text-primary-800"
                title="Edit"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            
            <button 
                type="button" 
                class="p-1 text-danger-600 hover:text-danger-800"
                title="Delete"
                @click="deleteItem({{ $item['id'] }})"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
    
    @if(!empty($item['children']))
        <div id="children-container-{{ $item['id'] }}" class="menu-item-children" data-parent-id="{{ $item['id'] }}">
            @foreach($item['children'] as $child)
                @include('vendor.menu-manager.components.menu-item', ['item' => $child, 'menu' => $menu])
            @endforeach
        </div>
    @endif
</div>