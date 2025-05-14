<x-filament::page>
    <form wire:submit="generatePricingForm">
        {{ $this->form }}

        @if ($errors->has('form'))
            <div class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-md border border-red-200">
                {{ $errors->first('form') }}
            </div>
        @endif

        <div class="mt-4 flex justify-end">
            <x-filament::button
                type="submit"
                :disabled="$this->isLoading"
            >
                @if ($this->isLoading)
                    <div class="flex items-center gap-2">
                        <x-filament::loading-indicator class="h-5 w-5" />
                        <span>Yükleniyor...</span>
                    </div>
                @else
                    Fiyat Tablosunu Oluştur
                @endif
            </x-filament::button>
        </div>
    </form>

    @if ($this->isLoading && !$this->showPricingForm)
        <div class="mt-8 w-full flex justify-center items-center p-8 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-col items-center gap-4">
                <x-filament::loading-indicator class="h-10 w-10" />
                <p>Fiyat tablosu hazırlanıyor, lütfen bekleyin...</p>
            </div>
        </div>
    @endif

    @if($this->showPricingForm)
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">
                    {{ is_object($this->hotel) && property_exists($this->hotel, 'name') ? $this->hotel->name : 'Otel' }} Fiyat Tablosu
                    <span class="text-sm text-gray-500">
                        ({{ $roomsCount }} oda, {{ $boardTypesCount }} pansiyon tipi için {{ $ratePlansCount }} plan)
                    </span>
                </h2>

                <div>
                    <x-filament::button
                        wire:click="$dispatch('refreshPricingData')"
                        color="secondary"
                        icon="heroicon-o-arrow-path"
                    >
                        Yenile
                    </x-filament::button>
                </div>
            </div>

            <!-- The component container with auto-loading state management -->
            <div
                x-data="{
                    loading: true,
                    attemptCount: 0,
                    maxAttempts: 3,
                    async loadPricingForm() {
                        this.loading = true;

                        // Wait 1 second and then automatically dispatch event to generate pricing data
                        if (this.attemptCount < this.maxAttempts) {
                            await new Promise(resolve => setTimeout(resolve, 1000));
                            $dispatch('refreshPricingData');
                            this.attemptCount++;
                        } else {
                            // If we've tried too many times, just hide loading
                            this.loading = false;
                        }
                    }
                }"
                x-init="$nextTick(() => { loadPricingForm() })"
                x-on:pricing-form-loading.window="loading = true"
                x-on:pricing-form-loaded.window="loading = false; attemptCount = 0;"
                x-on:pricing-form-error.window="loading = false"
                class="relative"
            >
                <!-- Loading overlay -->
                <div
                    x-show="loading"
                    class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 rounded-lg"
                >
                    <div class="flex flex-col items-center">
                        <x-filament::loading-indicator class="h-10 w-10" />
                        <p class="mt-2 text-gray-600">Fiyat verisi yükleniyor...</p>
                        <p class="text-sm text-gray-500 mt-1" x-show="attemptCount > 1" x-text="'(Deneme ' + attemptCount + '/' + maxAttempts + ')'">
                            (Deneme sayısı...)
                        </p>
                    </div>
                </div>

                <!-- Pricing form component -->
                <div x-show="!loading">
                    <livewire:pricing::pricing-form
                        wire:key="pricing-form-{{ $this->startDate }}-{{ $this->endDate }}-{{ implode(',', $this->selectedRooms) }}-{{ implode(',', $this->selectedBoardTypes) }}-{{ implode(',', $this->selectedDays) }}"
                    />
                </div>
            </div>
        </div>
    @endif
</x-filament::page>