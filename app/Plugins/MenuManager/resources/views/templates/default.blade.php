@php
    $menuId = $id ?: 'menu-' . $menu->id;
    $menuClass = 'menu-default ' . ($class ?? '');
@endphp

<nav id="{{ $menuId }}" class="{{ $menuClass }}" aria-label="{{ $menu->name }}">
    <ul class="flex flex-wrap items-center space-x-4">
        @foreach($menu->items as $item)
            @php
                $hasChildren = !empty($item->children) && $item->children->count() > 0;
                $isActive = false; // Determine if active based on your routing logic
                $itemClass = '';
                
                // Build item classes
                if ($hasChildren) {
                    $itemClass .= ' has-children';
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
            @endphp
            
            <li class="{{ trim($itemClass) }}" {!! $attrs !!}>
                <a href="{{ $item->url ?: '#' }}" target="{{ $item->target }}" class="block px-4 py-2 hover:text-primary-600">
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
                
                @if($hasChildren)
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