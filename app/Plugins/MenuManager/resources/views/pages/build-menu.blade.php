<x-filament::page>
    <div>
        <x-menu-manager::menu-builder :menu="$menu" :menuItems="$menuItems" />
    </div>
    
    <x-filament::modal id="add-menu-item-modal" width="md" display-classes="block">
        <x-slot name="heading">Add Menu Item</x-slot>
        
        <x-slot name="description">Create a new menu item for "{{ $menu->name }}"</x-slot>
        
        {{ $this->addItemForm }}
        
        <x-slot name="footerActions">
            <x-filament::button wire:click="createMenuItem" type="submit" color="success">
                <span class="flex items-center gap-1">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Create Item</span>
                </span>
            </x-filament::button>
            
            <x-filament::button x-on:click="close" color="gray">
                Cancel
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    @endpush
</x-filament::page>