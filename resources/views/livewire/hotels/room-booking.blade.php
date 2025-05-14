<div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Rezervasyon Detayları') }}</h3>
    
    <form wire:submit.prevent="bookNow" class="space-y-4">
        <!-- Pansiyon Tipi Seçimi -->
        @if(count($boardTypes) > 0)
            <div>
                <label for="board_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Pansiyon Tipi') }}
                </label>
                <select id="board_type" wire:model="selectedBoardType" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @foreach($boardTypes as $boardType)
                        <option value="{{ $boardType->id }}">
                            {{ $boardType->name }} 
                            @if($boardType->pivot->price_modifier != 0)
                                ({{ $boardType->pivot->price_modifier > 0 ? '+' : '' }}{{ $boardType->pivot->price_modifier }}%)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        
        <!-- Tarih Seçimi -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Giriş Tarihi') }}
                </label>
                <input type="date" id="check_in_date" wire:model="checkInDate" 
                       min="{{ date('Y-m-d') }}"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('checkInDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Çıkış Tarihi') }}
                </label>
                <input type="date" id="check_out_date" wire:model="checkOutDate" 
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('checkOutDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <!-- Misafir Sayısı -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="adults" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Yetişkin') }}
                </label>
                <select id="adults" wire:model="adults" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @for($i = 1; $i <= $room->capacity_adults; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                @error('adults') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="children" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Çocuk') }}
                </label>
                <select id="children" wire:model="children" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @for($i = 0; $i <= $room->capacity_children; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                @error('children') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <!-- Fiyat Özeti -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">{{ __('Gecelik') }}:</span>
                <span class="font-semibold">{{ number_format($room->base_price, 0, ',', '.') }} {{ $room->currency ?? '₺' }}</span>
            </div>
            
            <div class="flex justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">{{ __('Gece Sayısı') }}:</span>
                <span class="font-semibold">{{ $nights }}</span>
            </div>
            
            <div class="flex justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">{{ __('Kişi') }}:</span>
                <span class="font-semibold">{{ $adults }} {{ __('Yetişkin') }}, {{ $children }} {{ __('Çocuk') }}</span>
            </div>
            
            @if($selectedBoardType)
                @php
                    $boardType = $boardTypes->firstWhere('id', $selectedBoardType);
                @endphp
                
                @if($boardType && $boardType->pivot->price_modifier != 0)
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">{{ $boardType->name }}:</span>
                        <span class="font-semibold">
                            {{ $boardType->pivot->price_modifier > 0 ? '+' : '' }}{{ $boardType->pivot->price_modifier }}%
                        </span>
                    </div>
                @endif
            @endif
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                <div class="flex justify-between">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Toplam') }}:</span>
                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                        {{ number_format($totalPrice, 0, ',', '.') }} {{ $room->currency ?? '₺' }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                    {{ __('* Vergiler dahil') }}
                </div>
            </div>
        </div>
        
        <!-- Rezervasyon Butonu -->
        <button type="submit" 
                class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded shadow-sm transition">
            {{ __('Rezervasyon Yap') }}
        </button>
    </form>
</div>