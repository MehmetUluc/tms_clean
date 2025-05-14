@php
    use Filament\Forms\Components\DatePicker;
    use Filament\Forms\Components\Grid;
    use Filament\Forms\Components\Section;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\Toggle;
    use Filament\Forms\Components\Card;
@endphp

<x-filament::page class="filament-rate-plan-manage-prices">
    @livewireStyles
    <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold tracking-tight">
                {{ $record->name }} - Fiyat Yönetimi
            </h2>
            <p class="text-gray-500">
                @if($record->occupancy_pricing)
                    <span class="px-2 py-1 text-xs rounded-full bg-primary-500 text-white">Kişi Başı Fiyatlandırma</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-warning-500 text-white">Birim (Oda) Fiyatlandırma</span>
                @endif
                <span class="ml-2">{{ $record->room ? $record->room->name : ($record->roomType ? $record->roomType->name : 'Tüm odalar') }}</span>
            </p>
        </div>
        
        <div class="flex gap-2">
            <x-filament::button
                wire:click="$refresh"
                color="gray"
                icon="heroicon-o-arrow-path"
            >
                Yenile
            </x-filament::button>
            
            <x-filament::button
                wire:click="savePrices"
                color="success"
                icon="heroicon-o-check"
            >
                Kaydet
            </x-filament::button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <h3 class="text-base font-medium text-gray-900">Tarih Aralığı</h3>
                <p class="text-sm text-gray-500">Fiyatlandırma yapılacak tarih aralığını seçin</p>
                
                <form wire:submit.prevent="loadPriceData" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="inline-flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-700">Başlangıç Tarihi</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="dateRange.start" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            >
                        </div>
                        
                        <div>
                            <label class="inline-flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-700">Bitiş Tarihi</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="dateRange.end" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            >
                        </div>
                        
                        <x-filament::button
                            type="submit"
                            color="primary"
                        >
                            Uygula
                        </x-filament::button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200 mt-4">
                <h3 class="text-base font-medium text-gray-900">Toplu Fiyat Güncelleme</h3>
                <p class="text-sm text-gray-500">Birden fazla gün için aynı fiyatı uygulayın</p>
                
                <form wire:submit.prevent="applyBulkPrices" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Başlangıç Tarihi</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model="bulkPriceForm.bulk_start_date" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Bitiş Tarihi</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model="bulkPriceForm.bulk_end_date" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Fiyat</span>
                                </label>
                                <input 
                                    type="number" 
                                    wire:model="bulkPriceForm.bulk_price" 
                                    step="0.01"
                                    min="0"
                                    placeholder="100.00"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Para Birimi</span>
                                </label>
                                <select 
                                    wire:model="bulkPriceForm.bulk_currency" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                                    <option value="TRY">₺ TL</option>
                                    <option value="USD">$ USD</option>
                                    <option value="EUR">€ EUR</option>
                                    <option value="GBP">£ GBP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Minimum Kalış</span>
                                </label>
                                <input 
                                    type="number" 
                                    wire:model="bulkPriceForm.bulk_min_stay" 
                                    min="1"
                                    placeholder="1"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Durum</span>
                                </label>
                                <select 
                                    wire:model="bulkPriceForm.bulk_status" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                                    <option value="available">Müsait</option>
                                    <option value="limited">Sınırlı</option>
                                    <option value="sold_out">Dolu</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="flex items-center space-x-3">
                                <input 
                                    type="checkbox" 
                                    wire:model="bulkPriceForm.bulk_is_closed" 
                                    class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                >
                                <span class="text-sm font-medium text-gray-700">Satışa Kapalı</span>
                            </label>
                        </div>
                        
                        <x-filament::button
                            type="submit"
                            color="warning"
                            icon="heroicon-o-bolt"
                            class="w-full"
                        >
                            Toplu Fiyat Uygula
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="lg:col-span-8 xl:col-span-9">
            <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Günlük Fiyatlar
                    </h3>
                    <p class="text-sm text-gray-500">
                        Her gün için fiyat ve durum bilgilerini girin
                    </p>
                </div>
                
                <form wire:submit.prevent="savePrices" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Tarih
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Fiyat
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Min. Kalış
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Durum
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Kapalı
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Not
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($priceData as $index => $price)
                                    <tr class="{{ $price['day_name'] === 'Cumartesi' || $price['day_name'] === 'Pazar' ? 'bg-gray-50' : '' }}">
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($price['date'])->format('d.m.Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $price['day_name'] }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="flex space-x-1">
                                                <div class="flex-1">
                                                    <input 
                                                        type="number" 
                                                        wire:model="priceData.{{ $index }}.base_price" 
                                                        step="0.01"
                                                        min="0"
                                                        placeholder="0.00"
                                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-right"
                                                    >
                                                </div>
                                                <div class="w-16">
                                                    <select 
                                                        wire:model="priceData.{{ $index }}.currency"
                                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    >
                                                        <option value="TRY">₺</option>
                                                        <option value="USD">$</option>
                                                        <option value="EUR">€</option>
                                                        <option value="GBP">£</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <input 
                                                type="number" 
                                                wire:model="priceData.{{ $index }}.min_stay_arrival" 
                                                min="1"
                                                placeholder="1"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <select 
                                                wire:model="priceData.{{ $index }}.status"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                                <option value="available">Müsait</option>
                                                <option value="limited">Sınırlı</option>
                                                <option value="sold_out">Dolu</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="priceData.{{ $index }}.is_closed"
                                                    class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                                >
                                            </label>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <input 
                                                type="text" 
                                                wire:model="priceData.{{ $index }}.notes" 
                                                placeholder="Not..."
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if(empty($priceData))
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                            Tarih aralığı seçip "Uygula" düğmesine tıklayın
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    @if(!empty($priceData))
                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <x-filament::button
                                type="submit"
                                color="success"
                                icon="heroicon-o-check"
                            >
                                Değişiklikleri Kaydet
                            </x-filament::button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    @livewireScripts
</x-filament::page>