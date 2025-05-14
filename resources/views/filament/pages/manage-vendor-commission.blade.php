<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ $vendor->company_name }} - Commission Management</h2>
            <p class="text-gray-500 mt-1">Manage commission rates for this vendor and its associated hotels</p>
        </div>
        <div class="bg-primary-50 dark:bg-primary-950 p-4 rounded-lg">
            <p class="text-sm font-medium text-primary-600 dark:text-primary-400">Default Commission Rate</p>
            <div class="text-2xl font-bold">{{ number_format($vendor->default_commission_rate, 2) }}%</div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Commission Rate Form -->
        <div>
            {{ $this->form }}
        </div>

        <!-- Commission Rates Table -->
        <div class="mt-8">
            <h3 class="text-lg font-medium mb-4">Existing Commission Rates</h3>
            {{ $this->table }}
        </div>
    </div>
</x-filament::page>