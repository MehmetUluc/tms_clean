<div>
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Filtreler') }}</h2>
    
    <div class="space-y-6">
        <!-- Bölge Filtresi -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Bölge') }}</h3>
            <select wire:model="regionId" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">{{ __('Tüm Bölgeler') }}</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Otel Tipi Filtresi -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Otel Tipi') }}</h3>
            <select wire:model="typeId" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">{{ __('Tüm Tipler') }}</option>
                @foreach($hotelTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Yıldız Filtresi -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Yıldız Sayısı') }}</h3>
            <div class="space-y-2">
                @foreach($starsOptions as $star)
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="stars" value="{{ $star }}" class="rounded border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 flex items-center">
                            <span class="text-yellow-500">{{ str_repeat('★', $star) }}</span>
                            <span class="ml-1">{{ $star }} {{ __('Yıldız') }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        
        <!-- Fiyat Aralığı Filtresi -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Fiyat Aralığı') }}: {{ $priceRange[0] }} - {{ $priceRange[1] }} ₺
            </h3>
            <div class="px-2">
                <div class="relative">
                    <input type="range" min="{{ $priceRange[0] }}" max="{{ $priceRange[1] }}" wire:model="priceRange.0" 
                           class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer">
                    <input type="range" min="{{ $priceRange[0] }}" max="{{ $priceRange[1] }}" wire:model="priceRange.1" 
                           class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer">
                </div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mt-2">
                    <span>{{ $priceRange[0] }} ₺</span>
                    <span>{{ $priceRange[1] }} ₺</span>
                </div>
            </div>
        </div>
        
        <!-- Özellikler Filtresi -->
        @if(count($amenitiesList) > 0)
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Özellikler') }}</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($amenitiesList as $amenity)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="amenities" value="{{ $amenity->id }}" class="rounded border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $amenity->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Filtre Butonları -->
        <div class="flex space-x-2">
            <button wire:click="applyFilters" class="w-3/4 py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded shadow-sm transition">
                {{ __('Filtrele') }}
            </button>
            <button wire:click="clearFilters" class="w-1/4 py-2 px-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded shadow-sm transition">
                {{ __('Temizle') }}
            </button>
        </div>
    </div>
</div>