@php
    use Filament\Forms\Components\DatePicker;
    use Filament\Forms\Components\Grid;
    use Filament\Forms\Components\Section;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\Toggle;
    use Filament\Forms\Components\Card;
@endphp

<x-filament::page class="filament-rate-plan-manage-inventory">
    @livewireStyles
    <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold tracking-tight">
                {{ $record->name }} - Kontenjan Yönetimi
            </h2>
            <p class="text-gray-500">
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
                wire:click="saveInventory"
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
                <p class="text-sm text-gray-500">Kontenjan yönetimi yapılacak tarih aralığını seçin</p>
                
                <form wire:submit.prevent="loadInventoryData" method="POST">
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
                <h3 class="text-base font-medium text-gray-900">Toplu Kontenjan Güncelleme</h3>
                <p class="text-sm text-gray-500">Birden fazla gün için aynı kontenjanı uygulayın</p>
                
                <form wire:submit.prevent="applyBulkInventory" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Başlangıç Tarihi</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model="bulkInventoryForm.bulk_start_date" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Bitiş Tarihi</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model="bulkInventoryForm.bulk_end_date" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Müsait</span>
                                </label>
                                <input 
                                    type="number" 
                                    wire:model="bulkInventoryForm.bulk_available" 
                                    min="0"
                                    placeholder="Müsait oda sayısı"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">Toplam</span>
                                </label>
                                <input 
                                    type="number" 
                                    wire:model="bulkInventoryForm.bulk_total" 
                                    min="0"
                                    placeholder="Toplam kapasite"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox" 
                                        wire:model="bulkInventoryForm.bulk_is_closed" 
                                        class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700">Satışa Kapalı</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox" 
                                        wire:model="bulkInventoryForm.bulk_stop_sell" 
                                        class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700">Geçici Durdur</span>
                                </label>
                            </div>
                        </div>
                        
                        <x-filament::button
                            type="submit"
                            color="warning"
                            icon="heroicon-o-bolt"
                            class="w-full"
                        >
                            Toplu Kontenjan Uygula
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="lg:col-span-8 xl:col-span-9">
            <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Günlük Kontenjan
                    </h3>
                    <p class="text-sm text-gray-500">
                        Her gün için kontenjan ve durum bilgilerini girin
                    </p>
                </div>
                
                <form wire:submit.prevent="saveInventory" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Tarih
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Müsait
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Toplam
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Kapalı
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Durdur
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Not
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inventoryData as $index => $inventory)
                                    <tr class="{{ $inventory['day_name'] === 'Cumartesi' || $inventory['day_name'] === 'Pazar' ? 'bg-gray-50' : '' }}">
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($inventory['date'])->format('d.m.Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $inventory['day_name'] }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <input 
                                                type="number" 
                                                wire:model="inventoryData.{{ $index }}.available" 
                                                min="0"
                                                placeholder="0"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <input 
                                                type="number" 
                                                wire:model="inventoryData.{{ $index }}.total" 
                                                min="0"
                                                placeholder="0"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="inventoryData.{{ $index }}.is_closed"
                                                    class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                                >
                                            </label>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="inventoryData.{{ $index }}.stop_sell"
                                                    class="rounded border-gray-300 text-primary-500 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500"
                                                >
                                            </label>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <input 
                                                type="text" 
                                                wire:model="inventoryData.{{ $index }}.notes" 
                                                placeholder="Not..."
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if(empty($inventoryData))
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                            Tarih aralığı seçip "Uygula" düğmesine tıklayın
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    @if(!empty($inventoryData))
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