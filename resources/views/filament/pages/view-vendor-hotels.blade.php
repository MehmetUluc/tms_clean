<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ $vendor->company_name }} - Hotels</h2>
            <p class="text-gray-500 mt-1">Manage hotels associated with this vendor</p>
        </div>
        <div class="bg-primary-50 dark:bg-primary-950 p-4 rounded-lg">
            <p class="text-sm font-medium text-primary-600 dark:text-primary-400">Total Hotels</p>
            <div class="text-2xl font-bold">{{ $vendor->hotels()->count() }}</div>
        </div>
    </div>

    <div class="mt-4">
        {{ $this->table }}
    </div>
</x-filament::page>