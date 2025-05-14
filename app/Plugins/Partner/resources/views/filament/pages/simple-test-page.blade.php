<x-filament::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Simple Test Page</h2>
        
        <div class="bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800 rounded-md mb-4">
            <p class="text-green-800 dark:text-green-300 font-medium">This is a simple test page that uses the conventional view resolution.</p>
        </div>
        
        <div class="prose dark:prose-invert max-w-none">
            <p>This page uses the default convention for view resolution instead of an explicit view property.</p>
            <p>Path information:</p>
            <ul>
                <li><strong>View file:</strong> app/Plugins/Vendor/resources/views/filament/pages/simple-test-page.blade.php</li>
                <li><strong>Page class:</strong> App\Plugins\Vendor\Filament\Pages\SimpleTestPage</li>
                <li><strong>No explicit view property</strong> - using convention-based resolution</li>
            </ul>
        </div>
    </div>
</x-filament::page>