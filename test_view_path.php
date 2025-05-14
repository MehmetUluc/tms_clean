<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the view finder
$viewFinder = app()->make('view.finder');

// Get all view paths
$viewPaths = $viewFinder->getPaths();

echo "Current View Paths:\n";
foreach ($viewPaths as $path) {
    echo "- $path\n";
}

// Get all view namespaces
$viewFactory = app()->make('view');
$hints = $viewFactory->getFinder()->getHints();

echo "\nView Namespaces:\n";
foreach ($hints as $namespace => $paths) {
    echo "Namespace: $namespace\n";
    foreach ($paths as $path) {
        echo "  - $path\n";
    }
}

// Check if specific views exist
$views = [
    'vendor::filament.pages.test-page',
    'vendor::filament.pages.financial-summary',
    'vendor::filament.pages.simple-test-page',
    'vendor::filament.pages.basic-test-page',
    'vendor::filament.pages.simplified-financial-summary',
    'vendor::filament.pages.forced-test-page',
    'filament.pages.financial-summary',
    'filament.pages.raw-blade-output',
];

echo "\nView Existence Check:\n";
foreach ($views as $view) {
    $exists = $viewFactory->exists($view);
    echo "View '$view': " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
    
    if ($exists) {
        try {
            $finder = $viewFactory->getFinder();
            $reflectionFinder = new ReflectionClass($finder);
            $reflectionMethod = $reflectionFinder->getMethod('find');
            $reflectionMethod->setAccessible(true);
            $path = $reflectionMethod->invoke($finder, $view);
            echo "  File path: $path\n";
        } catch (Exception $e) {
            echo "  Error finding path: " . $e->getMessage() . "\n";
        }
    }
}

// Check the forced file existence
$forcedViewPath = __DIR__ . '/app/Plugins/Vendor/resources/views/filament/pages/forced-test-page.blade.php';
echo "\nForced View File Check:\n";
echo "File '$forcedViewPath': " . (file_exists($forcedViewPath) ? "EXISTS" : "NOT FOUND") . "\n";