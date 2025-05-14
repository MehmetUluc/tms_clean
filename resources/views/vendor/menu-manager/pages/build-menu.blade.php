<x-filament::page>
    <div>
        @include('vendor.menu-manager.components.menu-builder', ['menu' => $menu, 'menuItems' => $menuItems])
    </div>
    
    <x-filament::modal id="add-menu-item-modal" width="md" display-classes="block">
        <x-slot name="heading">Add Menu Item</x-slot>
        
        <x-slot name="description">Create a new menu item for "{{ $menu->name }}"</x-slot>
        
        {{ $this->form }}
        
        <x-slot name="footerActions">
            <button wire:click="createMenuItem" type="submit" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Item</span>
                </span>
            </button>
            
            <button x-on:click="close" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600">
                Cancel
            </button>
        </x-slot>
    </x-filament::modal>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    @endpush
</x-filament::page>