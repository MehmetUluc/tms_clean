@php
    $agencies = class_exists('\Tms\Core\Models\Agency') ? \Tms\Core\Models\Agency::all() : collect([]);
@endphp

<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-2">{{ __('Acente Seçimi') }}</h1>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('Lütfen yönetmek istediğiniz acenteyi seçin.') }}
            </p>
        </div>
        
        @if($agencies->count() > 0)
            <div class="w-full max-w-md p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form wire:submit="submit">
                    {{ $this->form }}
                    
                    <div class="flex justify-center mt-6">
                        <x-filament::button type="submit">
                            {{ __('Seçimi Onayla') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
        @else
            <div class="w-full max-w-md p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="text-center">
                    <div class="text-amber-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-2">{{ __('Acente Bulunamadı') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        {{ __('Henüz sistemde kayıtlı bir acente bulunmamaktadır. Lütfen önce bir acente ekleyin.') }}
                    </p>
                    
                    <x-filament::button 
                        color="success"
                        tag="a"
                        href="{{ route('filament.admin.resources.agencies.create') }}"
                        class="mt-4"
                    >
                        {{ __('Yeni Acente Ekle') }}
                    </x-filament::button>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>