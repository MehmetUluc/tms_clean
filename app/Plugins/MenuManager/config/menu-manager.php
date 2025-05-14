<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Menu Manager Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the Menu Manager plugin.
    |
    */

    // Default menu locations
    'locations' => [
        'header' => 'Header Navigation',
        'footer' => 'Footer Navigation',
        'sidebar' => 'Sidebar Navigation',
        'mobile' => 'Mobile Navigation',
    ],
    
    // Menu types available
    'types' => [
        'default' => 'Default Menu',
        'mega' => 'Mega Menu',
        'dropdown' => 'Dropdown Menu',
        'sidebar' => 'Sidebar Menu',
    ],
    
    // Link types available for menu items
    'link_types' => [
        'url' => 'Custom URL',
        'route' => 'Named Route',
        'model' => 'Dynamic Model',
        'page' => 'Static Page',
    ],
    
    // Target options for links
    'targets' => [
        '_self' => 'Same Window',
        '_blank' => 'New Window/Tab',
    ],
    
    // Model types that can be linked to menu items
    'linkable_models' => [
        'App\Plugins\Accommodation\Models\Hotel' => 'Hotel',
        'App\Plugins\Accommodation\Models\Region' => 'Region',
        'App\Plugins\Accommodation\Models\Room' => 'Room',
    ],
    
    // Cache settings
    'cache' => [
        'enabled' => true,
        'ttl' => 60 * 24, // 1 day in minutes
    ],
    
    // Available templates for menu items
    'templates' => [
        'default' => 'Default Item',
        'featured' => 'Featured Item',
        'list' => 'List Item',
        'gallery' => 'Gallery Item',
        'promo' => 'Promotional Item',
        'heading' => 'Section Heading',
    ],
    
    // Icon sets available
    'icon_sets' => [
        'heroicons' => 'Heroicons',
        'fontawesome' => 'Font Awesome',
    ],
];