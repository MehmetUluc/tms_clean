@php
    // Define defaults
    $location = $location ?? null;
    $type = $type ?? 'default';
    $class = $class ?? '';
    $id = $id ?? '';
    $wrapperClass = $wrapperClass ?? 'menu-wrapper';
    
    // Get menu from location or slug
    if ($location) {
        $menu = app('menu-manager.service')->getMenuByLocation($location);
    } else {
        $menu = app('menu-manager.service')->getMenu($slug ?? 'main-menu');
    }
    
    // Abort if menu doesn't exist or has no items
    if (!$menu || $menu->items->isEmpty()) {
        return;
    }
    
    // Get menu type template
    $template = $menu->type ?? $type;
@endphp

<div class="{{ $wrapperClass }}">
    @if ($template === 'default')
        @include('menu-manager::templates.default', ['menu' => $menu, 'class' => $class, 'id' => $id])
    @elseif ($template === 'mega')
        @include('menu-manager::templates.mega', ['menu' => $menu, 'class' => $class, 'id' => $id])
    @elseif ($template === 'dropdown')
        @include('menu-manager::templates.dropdown', ['menu' => $menu, 'class' => $class, 'id' => $id])
    @elseif ($template === 'sidebar')
        @include('menu-manager::templates.sidebar', ['menu' => $menu, 'class' => $class, 'id' => $id])
    @else
        @include('menu-manager::templates.default', ['menu' => $menu, 'class' => $class, 'id' => $id])
    @endif
</div>