<x-filament::page>
    <form wire:submit.prevent="create">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit">
                Create Preset Discount
            </x-filament::button>
        </div>
    </form>
</x-filament::page>