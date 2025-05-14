<x-filament::page>
    <div style="border: 2px solid red; padding: 20px; margin: 20px 0; border-radius: 8px;">
        <h1 style="color: red; font-size: 24px; font-weight: bold;">Forced Test Page</h1>
        <p>This view is being dynamically created and loaded. If you can see this page with red borders, the view system is working for direct file paths.</p>
        
        <div style="margin-top: 20px; background-color: #ffeeee; padding: 10px; border-radius: 4px;">
            <h3 style="font-weight: bold;">Debug Information:</h3>
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Current Time: {{ now() }}</li>
                <li>Path to view file: {{ __FILE__ }}</li>
                <li>Environment: {{ App::environment() }}</li>
            </ul>
        </div>
    </div>
</x-filament::page>