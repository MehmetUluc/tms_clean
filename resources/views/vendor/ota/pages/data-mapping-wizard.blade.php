<x-filament::page>
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold tracking-tight mb-4">Veri Eşleştirme Sihirbazı</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Bu sihirbaz, OTA entegrasyonları için veri eşleştirmeleri oluşturmanıza yardımcı olacak. Birkaç basit adımı izleyerek yeni bir eşleştirme oluşturabilirsiniz.</p>

        {{ $this->form }}
    </div>

    <!-- Form dışında butonlar tek bir yerde -->
    <div class="max-w-5xl mx-auto mt-8 flex justify-end space-x-3">
        @if($this->currentStep > 1)
            <x-filament::button
                wire:click="$set('currentStep', {{ $this->currentStep - 1 }})"
                color="secondary"
            >
                <span>Geri</span>
            </x-filament::button>
        @endif

        @if($this->currentStep < 3)
            <x-filament::button
                wire:click="$set('currentStep', {{ $this->currentStep + 1 }})"
                color="primary"
            >
                <span>İleri</span>
            </x-filament::button>
        @else
            <x-filament::button
                wire:click="create"
                color="success"
            >
                <span>Oluştur</span>
            </x-filament::button>
        @endif
    </div>
</x-filament::page>