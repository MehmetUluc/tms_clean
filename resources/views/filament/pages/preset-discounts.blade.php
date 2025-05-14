<x-filament::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight">Preset Discounts</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Create a new discount using our predefined templates.</p>
                </div>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <form wire:submit.prevent="create">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit">
                        Create Preset Discount
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament::page>