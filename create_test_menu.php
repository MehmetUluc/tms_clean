<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create test menu
use App\Plugins\MenuManager\Models\Menu;
use App\Plugins\MenuManager\Models\MenuItem;

// Create main menu
$menu = Menu::firstOrCreate(
    ['slug' => 'main-navigation'],
    [
        'name' => 'Main Navigation',
        'location' => 'header',
        'type' => 'mega',
        'is_active' => true,
    ]
);

echo "Created menu: {$menu->name} (ID: {$menu->id})\n";

// Create main menu items
$menuItems = [
    [
        'title' => 'Home',
        'url' => '/',
        'link_type' => 'url',
        'order' => 1,
        'is_active' => true,
    ],
    [
        'title' => 'Destinations',
        'url' => '/destinations',
        'link_type' => 'url',
        'order' => 2,
        'is_active' => true,
        'is_mega_menu' => true,
        'mega_menu_columns' => 4,
        'mega_menu_width' => 'container',
        'mega_menu_background' => '#f8fafc',
        'mega_menu_content' => [
            [
                'title' => 'Popular Destinations',
                'content_type' => 'links',
                'width' => 'narrow',
                'links' => [
                    ['title' => 'Istanbul', 'url' => '/destinations/istanbul'],
                    ['title' => 'Antalya', 'url' => '/destinations/antalya'],
                    ['title' => 'Bodrum', 'url' => '/destinations/bodrum'],
                    ['title' => 'Cappadocia', 'url' => '/destinations/cappadocia'],
                    ['title' => 'Pamukkale', 'url' => '/destinations/pamukkale'],
                ]
            ],
            [
                'title' => 'Regions',
                'content_type' => 'links',
                'width' => 'narrow',
                'links' => [
                    ['title' => 'Aegean Coast', 'url' => '/regions/aegean'],
                    ['title' => 'Mediterranean', 'url' => '/regions/mediterranean'],
                    ['title' => 'Black Sea', 'url' => '/regions/black-sea'],
                    ['title' => 'Marmara', 'url' => '/regions/marmara'],
                    ['title' => 'Central Anatolia', 'url' => '/regions/central-anatolia'],
                ]
            ],
            [
                'title' => 'Featured',
                'content_type' => 'featured',
                'width' => 'wide',
                'featured_title' => 'Explore Türkiye',
                'featured_description' => 'Discover the beauty and history of Türkiye with our exclusive travel packages.',
                'featured_url' => '/special-offers/turkiye-tour',
            ]
        ]
    ],
    [
        'title' => 'Hotels',
        'url' => '/hotels',
        'link_type' => 'url',
        'order' => 3,
        'is_active' => true,
        'is_mega_menu' => true,
        'mega_menu_columns' => 3,
        'mega_menu_content' => [
            [
                'title' => 'Hotel Types',
                'content_type' => 'links',
                'width' => 'medium',
                'links' => [
                    ['title' => 'Luxury Hotels', 'url' => '/hotels/luxury'],
                    ['title' => 'Boutique Hotels', 'url' => '/hotels/boutique'],
                    ['title' => 'Resorts', 'url' => '/hotels/resorts'],
                    ['title' => 'Villas', 'url' => '/hotels/villas'],
                ]
            ],
            [
                'title' => 'Top Hotel Brands',
                'content_type' => 'links',
                'width' => 'medium',
                'links' => [
                    ['title' => 'Hilton', 'url' => '/hotels/brands/hilton'],
                    ['title' => 'Marriott', 'url' => '/hotels/brands/marriott'],
                    ['title' => 'Radisson', 'url' => '/hotels/brands/radisson'],
                    ['title' => 'Swissotel', 'url' => '/hotels/brands/swissotel'],
                ]
            ],
            [
                'title' => 'Special Deals',
                'content_type' => 'html',
                'width' => 'medium',
                'html_content' => '<div class="p-4 bg-indigo-50 rounded"><h4 class="font-bold text-indigo-700 mb-2">Summer Special</h4><p class="text-sm">Book now and get 20% off on all hotels. Limited time offer!</p><a href="/special-offers" class="mt-2 inline-block text-indigo-600 font-medium hover:underline">View Offers</a></div>'
            ]
        ]
    ],
    [
        'title' => 'About',
        'url' => '/about',
        'link_type' => 'url',
        'order' => 4,
        'is_active' => true,
    ],
    [
        'title' => 'Contact',
        'url' => '/contact',
        'link_type' => 'url',
        'order' => 5,
        'is_active' => true,
    ],
];

// Create or update menu items
foreach ($menuItems as $itemData) {
    $item = MenuItem::updateOrCreate(
        [
            'menu_id' => $menu->id,
            'title' => $itemData['title'],
        ],
        array_merge(['menu_id' => $menu->id], $itemData)
    );
    
    echo "Created menu item: {$item->title}\n";
}

echo "\nMenu creation complete!\n";
echo "You can view the mega menu at: " . url('/mega-menu-demo/main-navigation') . "\n";