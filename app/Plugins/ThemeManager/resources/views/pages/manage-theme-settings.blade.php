
<x-filament::page>
    <div 
        x-data 
        x-init="
            document.addEventListener('theme-color-updated', event => {
                $dispatch('syncColor', event.detail);
            })
        " 
        class="grid grid-cols-1 xl:grid-cols-2 gap-6"
    >
        <div>
            {{ $this->form }}
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <livewire:color-preview />
        </div>
    </div>
</x-filament::page>
