<x-filament::page>
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                {{ $this->createReportForm }}
            </div>
        </div>
        
        <div class="col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4">
                    <h2 class="text-lg font-medium">Ministry Reports</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your reports for the Ministry of Tourism</p>
                </div>
                
                <div>
                    {{ $this->reportsTable }}
                </div>
            </div>
        </div>
    </div>
</x-filament::page>