<x-filament::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Static Test Page</h2>
        
        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 border border-purple-200 dark:border-purple-800 rounded-md mb-4">
            <p class="text-purple-800 dark:text-purple-300 font-medium">
                This is a simple static test page with a dedicated view file.
            </p>
        </div>
        
        <div class="prose dark:prose-invert max-w-none">
            <p>This page uses a fixed view file instead of dynamic rendering.</p>
            <p>If you can see this page, it means that:</p>
            <ul>
                <li>The VendorPlugin is correctly registered in the AdminPanelProvider</li>
                <li>The Vendor ServiceProvider is being loaded</li>
                <li>The page class is being found and instantiated</li>
                <li>The view resolution is working correctly</li>
            </ul>
        </div>
        
        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md">
            <h3 class="font-semibold text-amber-800 dark:text-amber-300">Possible issues with views:</h3>
            <ul class="list-disc ml-5 text-amber-700 dark:text-amber-400">
                <li>View caching (try running php artisan view:clear)</li>
                <li>Incorrect view namespace registration in VendorServiceProvider</li>
                <li>Incorrect view path in the view property of page classes</li>
                <li>Missing directory structure in resources/views</li>
            </ul>
        </div>
    </div>
</x-filament::page>