<div class="search-box-container w-full max-w-5xl p-4 md:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-xl">
    <form wire:submit.prevent="search" class="space-y-4">
        <!-- Ana Arama Formu -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Bölge Seçimi -->
            <div class="col-span-1 md:col-span-2 lg:col-span-1 relative">
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Location') }}
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.debounce.300ms="searchTerm" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" 
                        placeholder="{{ __('Search for a region, city or hotel') }}"
                    >
                    <input type="hidden" wire:model="regionId" name="region_id">
                    
                    <!-- Arama Önerileri -->
                    @if(!empty($suggestions))
                        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-900 rounded-md shadow-lg border border-gray-200 dark:border-gray-700">
                            <ul class="py-1 max-h-60 overflow-auto">
                                @foreach($suggestions as $suggestion)
                                    <li>
                                        <button
                                            type="button"
                                            wire:click="selectRegion({{ $suggestion['id'] }}, '{{ $suggestion['name'] }}')"
                                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                                        >
                                            <span class="font-medium">{{ $suggestion['name'] }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $suggestion['path'] }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Check-In Tarihi -->
            <div>
                <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Check-in Date') }}
                </label>
                <input 
                    type="date" 
                    wire:model="checkInDate" 
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                >
            </div>
            
            <!-- Check-Out Tarihi -->
            <div>
                <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Check-out Date') }}
                </label>
                <input 
                    type="date" 
                    wire:model="checkOutDate" 
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                >
            </div>
            
            <!-- Misafir Sayısı / Oda Sayısı Özeti -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Guests & Rooms') }}
                </label>
                <button 
                    type="button"
                    wire:click="toggleAdvancedFilters"
                    class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <span>
                        {{ $adults }} {{ __('Adult') }}{{ $adults > 1 ? 's' : '' }}, 
                        {{ $children }} {{ __('Child') }}{{ $children > 1 ? 'ren' : '' }}, 
                        {{ $roomCount }} {{ __('Room') }}{{ $roomCount > 1 ? 's' : '' }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Gelişmiş Filtreler -->
        @if($showAdvancedFilters)
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Yetişkin Sayısı -->
                    <div>
                        <label for="adults" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Adults') }}
                        </label>
                        <select 
                            wire:model="adults"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        >
                            @for($i = 1; $i <= config('b2c-theme.search_box.max_adults', 10); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <!-- Çocuk Sayısı -->
                    <div>
                        <label for="children" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Children') }}
                        </label>
                        <select 
                            wire:model="children"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        >
                            @for($i = 0; $i <= config('b2c-theme.search_box.max_children', 6); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <!-- Oda Sayısı -->
                    <div>
                        <label for="roomCount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Rooms') }}
                        </label>
                        <select 
                            wire:model="roomCount"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        >
                            @for($i = 1; $i <= config('b2c-theme.search_box.max_rooms', 5); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <!-- Çocuk Yaşları (eğer çocuk varsa) -->
                @if($children > 0)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Children Ages') }}
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                            @for($i = 0; $i < $children; $i++)
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 mb-1 block">
                                        {{ __('Child') }} {{ $i + 1 }}
                                    </label>
                                    <select 
                                        wire:model="childrenAges.{{ $i }}"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs"
                                    >
                                        @for($age = 0; $age <= 17; $age++)
                                            <option value="{{ $age }}">{{ $age }} {{ __('years') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        @endif
        
        <!-- Arama Butonu -->
        <div class="flex justify-center">
            <button 
                type="submit"
                class="w-full sm:w-auto px-6 py-3 bg-primary-600 text-white font-medium text-base rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out"
            >
                <span class="flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Search Hotels') }}
                </span>
            </button>
        </div>
    </form>
</div>