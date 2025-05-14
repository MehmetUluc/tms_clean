@php
    $menuId = $id ?: 'menu-' . $menu->id;
    $menuClass = 'menu-mega ' . ($class ?? '');
@endphp

<nav id="{{ $menuId }}" class="{{ $menuClass }}" aria-label="{{ $menu->name }}">
    <ul class="flex items-center space-x-6">
        @foreach($menu->items as $item)
            @php
                $hasChildren = !empty($item->children) && $item->children->count() > 0;
                $isMegaMenu = $item->is_mega_menu;
                $isActive = false; // Determine if active based on your routing logic
                $itemClass = '';
                
                // Build item classes
                if ($hasChildren) {
                    $itemClass .= ' has-children';
                }
                
                if ($isMegaMenu) {
                    $itemClass .= ' has-mega-menu';
                }
                
                if ($isActive) {
                    $itemClass .= ' active';
                }
                
                if ($item->is_featured) {
                    $itemClass .= ' featured';
                }
                
                if (!empty($item->class)) {
                    $itemClass .= ' ' . $item->class;
                }
                
                // Get attributes
                $attributes = !empty($item->attributes) ? $item->attributes : [];
                $attrs = '';
                foreach ($attributes as $key => $value) {
                    $attrs .= ' ' . $key . '="' . $value . '"';
                }
                
                // Get mega menu styles
                $megaMenuStyles = '';
                if ($isMegaMenu) {
                    $styles = [];
                    
                    if ($item->mega_menu_background) {
                        $styles[] = "background-color: {$item->mega_menu_background}";
                    }
                    
                    if (!empty($item->mega_menu_bg_image)) {
                        $styles[] = "background-image: url('" . asset('storage/' . $item->mega_menu_bg_image) . "')";
                        $styles[] = "background-size: cover";
                        $styles[] = "background-position: center";
                    }
                    
                    $megaMenuStyles = !empty($styles) ? ' style="' . implode('; ', $styles) . '"' : '';
                }
                
                // Get mega menu width
                $megaMenuWidth = 'container mx-auto';
                if ($isMegaMenu && $item->mega_menu_width) {
                    if ($item->mega_menu_width === 'full') {
                        $megaMenuWidth = 'w-full';
                    } elseif ($item->mega_menu_width === 'custom') {
                        $megaMenuWidth = 'w-[1200px] mx-auto';
                    }
                }
                
                // Get mega menu columns
                $columns = $item->mega_menu_columns ?? 4;
            @endphp
            
            <li class="{{ trim($itemClass) }}" {!! $attrs !!}>
                <a href="{{ $item->url ?: '#' }}" target="{{ $item->target }}" class="block px-4 py-4 hover:text-primary-600 font-medium">
                    @if($item->icon)
                        <span class="icon mr-1">{!! $item->icon !!}</span>
                    @endif
                    {{ $item->title }}
                    
                    @if($hasChildren)
                        <svg class="w-4 h-4 ml-1 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
                
                @if($isMegaMenu && !empty($item->mega_menu_content))
                    <div class="mega-menu absolute left-0 mt-1 w-full z-50 bg-white shadow-xl border-t border-gray-200 py-6 hidden group-hover:block"{!! $megaMenuStyles !!}>
                        <div class="{{ $megaMenuWidth }} px-4">
                            <div class="grid grid-cols-{{ $columns }} gap-6">
                                @foreach($item->mega_menu_content as $column)
                                    @php
                                        $columnWidth = isset($column['width']) ? $column['width'] : 'equal';
                                        $contentType = isset($column['content_type']) ? $column['content_type'] : 'links';
                                        $colSpan = 1;
                                        
                                        if ($columnWidth === 'narrow') $colSpan = 1;
                                        elseif ($columnWidth === 'medium') $colSpan = 2;
                                        elseif ($columnWidth === 'wide') $colSpan = 3;
                                        elseif ($columnWidth === 'extra-wide') $colSpan = 4;
                                        elseif ($columnWidth === 'full') $colSpan = $columns;
                                        
                                        // Ensure we don't exceed grid size
                                        $colSpan = min($colSpan, $columns);
                                    @endphp
                                    
                                    <div class="col-span-{{ $colSpan }}">
                                        @if(isset($column['title']))
                                            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">{{ $column['title'] }}</h3>
                                        @endif
                                        
                                        @if($contentType === 'links' && !empty($column['links']))
                                            <ul class="space-y-2">
                                                @foreach($column['links'] as $link)
                                                    <li>
                                                        <a href="{{ $link['url'] }}" class="text-gray-700 hover:text-primary-600 {{ !empty($link['featured']) ? 'font-semibold text-primary-600' : '' }}">
                                                            @if(!empty($link['icon']))
                                                                <i class="{{ $link['icon'] }} mr-1"></i>
                                                            @endif
                                                            {{ $link['title'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif($contentType === 'featured' && !empty($column['featured_image']))
                                            <div class="featured-content-wrapper">
                                                <div class="mb-3 rounded overflow-hidden">
                                                    <img src="{{ asset('storage/' . $column['featured_image']) }}" alt="{{ $column['featured_title'] ?? '' }}" class="w-full h-auto">
                                                </div>
                                                
                                                @if(!empty($column['featured_title']))
                                                    <h4 class="font-bold text-gray-800 mb-1">{{ $column['featured_title'] }}</h4>
                                                @endif
                                                
                                                @if(!empty($column['featured_description']))
                                                    <p class="text-sm text-gray-600 mb-2">{{ $column['featured_description'] }}</p>
                                                @endif
                                                
                                                @if(!empty($column['featured_url']))
                                                    <a href="{{ $column['featured_url'] }}" class="text-sm font-medium text-primary-600 hover:underline">
                                                        Learn More <span aria-hidden="true">&rarr;</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @elseif($contentType === 'image' && !empty($column['featured_image']))
                                            <div class="image-wrapper">
                                                <a href="{{ $column['featured_url'] ?? '#' }}" class="block">
                                                    <img src="{{ asset('storage/' . $column['featured_image']) }}" alt="{{ $column['title'] ?? '' }}" class="w-full h-auto rounded">
                                                </a>
                                            </div>
                                        @elseif($contentType === 'html' && !empty($column['html_content']))
                                            <div class="html-content">
                                                {!! $column['html_content'] !!}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($hasChildren && !$isMegaMenu)
                    <ul class="absolute z-10 hidden group-hover:block bg-white border border-gray-200 rounded-md shadow-lg py-1 min-w-48">
                        @foreach($item->children as $child)
                            @php
                                $hasGrandchildren = !empty($child->children) && $child->children->count() > 0;
                                $isChildActive = false; // Determine if active
                                $childClass = '';
                                
                                // Build child classes
                                if ($hasGrandchildren) {
                                    $childClass .= ' has-children';
                                }
                                
                                if ($isChildActive) {
                                    $childClass .= ' active';
                                }
                                
                                if ($child->is_featured) {
                                    $childClass .= ' featured';
                                }
                                
                                if (!empty($child->class)) {
                                    $childClass .= ' ' . $child->class;
                                }
                                
                                // Get attributes
                                $childAttributes = !empty($child->attributes) ? $child->attributes : [];
                                $childAttrs = '';
                                foreach ($childAttributes as $key => $value) {
                                    $childAttrs .= ' ' . $key . '="' . $value . '"';
                                }
                            @endphp
                            
                            <li class="{{ trim($childClass) }}" {!! $childAttrs !!}>
                                <a href="{{ $child->url ?: '#' }}" target="{{ $child->target }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                                    @if($child->icon)
                                        <span class="icon mr-1">{!! $child->icon !!}</span>
                                    @endif
                                    {{ $child->title }}
                                    
                                    @if($hasGrandchildren)
                                        <svg class="w-4 h-4 ml-1 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </a>
                                
                                @if($hasGrandchildren)
                                    <ul class="absolute left-full top-0 hidden group-hover:block bg-white border border-gray-200 rounded-md shadow-lg py-1 min-w-48">
                                        @foreach($child->children as $grandchild)
                                            @php
                                                $grandchildClass = '';
                                                
                                                if ($grandchild->is_featured) {
                                                    $grandchildClass .= ' featured';
                                                }
                                                
                                                if (!empty($grandchild->class)) {
                                                    $grandchildClass .= ' ' . $grandchild->class;
                                                }
                                                
                                                // Get attributes
                                                $grandchildAttributes = !empty($grandchild->attributes) ? $grandchild->attributes : [];
                                                $grandchildAttrs = '';
                                                foreach ($grandchildAttributes as $key => $value) {
                                                    $grandchildAttrs .= ' ' . $key . '="' . $value . '"';
                                                }
                                            @endphp
                                            
                                            <li class="{{ trim($grandchildClass) }}" {!! $grandchildAttrs !!}>
                                                <a href="{{ $grandchild->url ?: '#' }}" target="{{ $grandchild->target }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                                                    @if($grandchild->icon)
                                                        <span class="icon mr-1">{!! $grandchild->icon !!}</span>
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
</nav>

<style>
    /* Mega Menu Styling */
    .menu-mega {
        position: relative;
    }
    
    .menu-mega li {
        position: relative;
    }
    
    .menu-mega li.has-children,
    .menu-mega li.has-mega-menu {
        position: static; /* Full width mega menu needs static positioning on parent */
    }
    
    .menu-mega li.has-mega-menu > a:hover + .mega-menu,
    .menu-mega li.has-mega-menu:hover > .mega-menu {
        display: block;
    }
    
    .menu-mega .mega-menu {
        display: none;
        position: absolute;
        left: 0;
        width: 100%;
        z-index: 1000;
        padding: 1.5rem 0;
    }
    
    /* Mobile-friendly styling */
    @media (max-width: 1023px) {
        .menu-mega .mega-menu {
            position: relative;
            left: auto;
            width: auto;
            overflow: auto;
            max-height: 80vh;
        }
        
        .menu-mega .mega-menu .grid {
            grid-template-columns: 1fr !important;
        }
        
        .menu-mega .mega-menu .grid > div {
            grid-column: span 1 !important;
        }
    }
</style>