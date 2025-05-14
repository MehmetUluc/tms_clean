<x-filament::page>
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                {{ $this->uploadForm }}
            </div>
        </div>
        
        <div class="col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4">
                    <h2 class="text-lg font-medium">Your Documents</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your vendor documents and certificates</p>
                </div>
                
                <div>
                    {{ $this->documentsTable }}
                </div>
            </div>
        </div>
    </div>
</x-filament::page>