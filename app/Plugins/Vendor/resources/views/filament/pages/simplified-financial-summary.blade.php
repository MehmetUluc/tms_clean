<x-filament::page>
    <h1 class="text-2xl font-bold">Simplified Financial Summary</h1>
    <p class="mt-2 text-gray-500">A basic version of the financial summary page</p>
    
    <div class="mt-8 p-4" style="border: 1px solid #2563eb; background-color: #eff6ff; border-radius: 8px;">
        <h2 class="text-xl font-semibold" style="color: #2563eb;">Current Vendor Information</h2>
        <ul class="mt-2 space-y-2" style="list-style-type: disc; padding-left: 20px;">
            <li>Vendor ID: {{ $this->vendor->id }}</li>
            <li>Vendor Name: {{ $this->vendor->name }}</li>
            <li>Status: {{ $this->vendor->status }}</li>
        </ul>
    </div>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div style="border: 1px solid #10b981; background-color: #ecfdf5; padding: 16px; border-radius: 8px;">
            <h3 style="color: #10b981; font-weight: 600;">Total Revenue</h3>
            <p style="font-size: 24px; font-weight: 700;">₺1,254,890</p>
        </div>
        
        <div style="border: 1px solid #f59e0b; background-color: #fffbeb; padding: 16px; border-radius: 8px;">
            <h3 style="color: #f59e0b; font-weight: 600;">Occupancy Rate</h3>
            <p style="font-size: 24px; font-weight: 700;">76.4%</p>
        </div>
        
        <div style="border: 1px solid #ef4444; background-color: #fef2f2; padding: 16px; border-radius: 8px;">
            <h3 style="color: #ef4444; font-weight: 600;">Reservations</h3>
            <p style="font-size: 24px; font-weight: 700;">1,284</p>
        </div>
    </div>
</x-filament::page>