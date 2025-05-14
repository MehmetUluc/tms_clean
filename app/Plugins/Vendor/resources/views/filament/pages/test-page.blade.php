<x-filament::page>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Vendor Plugin Test Page</h2>
        
        <div class="bg-blue-50 p-4 border border-blue-200 rounded-md mb-4">
            <p class="text-blue-800 font-medium">If you can see this page, the view resolution is working correctly.</p>
        </div>
        
        <div class="prose max-w-none">
            <p>This is a simple test page to verify that the Vendor plugin can correctly resolve views.</p>
            <p>Path information:</p>
            <ul>
                <li><strong>View file:</strong> app/Plugins/Vendor/resources/views/filament/pages/test-page.blade.php</li>
                <li><strong>Page class:</strong> App\Plugins\Vendor\Filament\Pages\TestPage</li>
                <li><strong>View namespace:</strong> vendor::filament.pages.test-page</li>
            </ul>
        </div>
        
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <h3 class="font-semibold text-green-800">Debug Information</h3>
            <p>Page rendered at: {{ now() }}</p>
            <p>View exists check: {{ view()->exists('vendor::filament.pages.test-page') ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</x-filament::page>