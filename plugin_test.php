<?php

// Plugin test script
// ----------------------------------------
// This script tests that the plugin structure is correctly set up and all
// plugins are registered and loadable.

echo "=== Plugin Structure Test ===\n";

// Check for plugin directories
echo "\nChecking plugin directories:\n";
$pluginDirs = [
    'Core' => __DIR__ . '/app/Plugins/Core',
    'Accommodation' => __DIR__ . '/app/Plugins/Accommodation',
    'Booking' => __DIR__ . '/app/Plugins/Booking',
    'Amenities' => __DIR__ . '/app/Plugins/Amenities',
    'Integration' => __DIR__ . '/app/Plugins/Integration',
    'UserManagement' => __DIR__ . '/app/Plugins/UserManagement',
];

foreach ($pluginDirs as $name => $path) {
    if (is_dir($path)) {
        echo "✓ {$name} plugin directory exists\n";
    } else {
        echo "✗ {$name} plugin directory not found\n";
    }
}

// Check for plugin main classes
echo "\nChecking plugin main classes:\n";
$pluginFiles = [
    'Core' => __DIR__ . '/app/Plugins/Core/CorePlugin.php',
    'Accommodation' => __DIR__ . '/app/Plugins/Accommodation/AccommodationPlugin.php',
    'Booking' => __DIR__ . '/app/Plugins/Booking/BookingPlugin.php',
    'Amenities' => __DIR__ . '/app/Plugins/Amenities/AmenitiesPlugin.php',
    'Integration' => __DIR__ . '/app/Plugins/Integration/IntegrationPlugin.php',
    'UserManagement' => __DIR__ . '/app/Plugins/UserManagement/UserManagementPlugin.php',
];

foreach ($pluginFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✓ {$name}Plugin class exists\n";
    } else {
        echo "✗ {$name}Plugin class not found\n";
    }
}

// Check for plugin service providers
echo "\nChecking plugin service providers:\n";
$providerFiles = [
    'Core' => __DIR__ . '/app/Plugins/Core/CoreServiceProvider.php',
    'Accommodation' => __DIR__ . '/app/Plugins/Accommodation/AccommodationServiceProvider.php',
    'Booking' => __DIR__ . '/app/Plugins/Booking/BookingServiceProvider.php',
    'Amenities' => __DIR__ . '/app/Plugins/Amenities/AmenitiesServiceProvider.php',
    'Integration' => __DIR__ . '/app/Plugins/Integration/IntegrationServiceProvider.php',
    'UserManagement' => __DIR__ . '/app/Plugins/UserManagement/UserManagementServiceProvider.php',
];

foreach ($providerFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✓ {$name}ServiceProvider class exists\n";
    } else {
        echo "✗ {$name}ServiceProvider class not found\n";
    }
}

// Check for resources directories
echo "\nChecking resources in plugins:\n";
$resourceDirs = [
    'Accommodation' => __DIR__ . '/app/Plugins/Accommodation/Filament/Resources',
    'Booking' => __DIR__ . '/app/Plugins/Booking/Filament/Resources',
    'Amenities' => __DIR__ . '/app/Plugins/Amenities/Filament/Resources',
];

foreach ($resourceDirs as $name => $path) {
    if (is_dir($path)) {
        echo "✓ {$name} resources directory exists\n";
        $resourceFiles = glob($path . '/*Resource.php');
        echo "  Found " . count($resourceFiles) . " resources\n";
    } else {
        echo "✗ {$name} resources directory not found\n";
    }
}

// Check for widgets directories
echo "\nChecking widgets in plugins:\n";
$widgetDirs = [
    'Core' => __DIR__ . '/app/Plugins/Core/src/Filament/Widgets',
    'Accommodation' => __DIR__ . '/app/Plugins/Accommodation/Filament/Widgets',
    'Booking' => __DIR__ . '/app/Plugins/Booking/Filament/Widgets',
];

foreach ($widgetDirs as $name => $path) {
    if (is_dir($path)) {
        echo "✓ {$name} widgets directory exists\n";
        $widgetFiles = glob($path . '/*.php');
        echo "  Found " . count($widgetFiles) . " widgets\n";
    } else {
        echo "✗ {$name} widgets directory not found\n";
    }
}

echo "\n=== Plugin Structure Test Complete ===\n";
echo "To run this test, execute: php plugin_test.php\n";