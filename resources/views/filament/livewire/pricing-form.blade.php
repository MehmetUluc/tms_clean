<div>
    <style>
        /* Input genişlikleri için iyileştirmeler */
        .pricing-input {
            width: 80px !important;
            min-width: 80px !important;
            max-width: 80px !important;
        }

        .pricing-input-sm {
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
        }

        /* Tarih sütunlarını sabit genişlikte tutar */
        .date-column {
            width: 90px !important;
            min-width: 90px !important;
            max-width: 90px !important;
        }

        /* Kontrol alanlarını sabit genişlikte tutar */
        .control-column {
            width: 300px !important;
            min-width: 300px !important;
            max-width: 300px !important;
        }

        /* Kontrol alanı içindeki inputları sabit genişlikte tutar */
        .control-input {
            width: 200px !important;
            min-width: 200px !important;
        }

        /* Tablo scroll desteği */
        .pricing-table-container {
            overflow-x: auto;
            max-width: 100%;
        }

        /* Mobil ekranlarda form bileşeni genişliğini optimize eder */
        @media (max-width: 768px) {
            .pricing-table-container table {
                min-width: 600px;
            }
        }

        /* Select için sabit width */
        .status-select, .sales-select {
            width: 90px !important;
        }
        
        /* Dark theme select option styling */
        @media (prefers-color-scheme: dark) {
            select option {
                background-color: rgb(31 41 55);
                color: rgb(243 244 246);
            }
        }
        
        .dark select option {
            background-color: rgb(31 41 55);
            color: rgb(243 244 246);
        }
    </style>
    
    @if(empty($ratePlans) || empty($dateRange))
        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
            <div class="flex flex-col items-center justify-center space-y-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">Fiyat verileri yüklenemedi</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lütfen formu yeniden oluşturun ya da sayfayı yenileyin.</p>
                <button
                    type="button"
                    class="mt-2 px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600"
                    onclick="window.location.reload();">
                    Sayfayı Yenile
                </button>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach($ratePlans as $ratePlanId => $ratePlan)
                @php
                    $room = $roomsData[$ratePlan['room_id']] ?? null;
                    $boardType = $boardTypesData[$ratePlan['board_type_id']] ?? null;
                    $isPerPerson = $ratePlan['is_per_person'] ?? false;
                    $maxOccupancy = $room['capacity'] ?? 3;

                    // Fiyatlandırma metodu
                    $pricingMethodLabel = '';
                    if (isset($tableHeaders[$ratePlanId]['pricing_method_label'])) {
                        $pricingMethodLabel = $tableHeaders[$ratePlanId]['pricing_method_label'];
                    } else {
                        $pricingMethodLabel = $isPerPerson ? 'Kişi Başı Fiyatlandırma' : 'Ünite Bazlı Fiyatlandırma';
                    }
                    $pricingMethodLabel = str_replace(['(', ')'], '', $pricingMethodLabel);

                    // İade bilgisi
                    $refundTypeLabel = '';
                    $refundBadgeClass = '';
                    if (isset($tableHeaders[$ratePlanId]['refund_type_label'])) {
                        $refundTypeLabel = $tableHeaders[$ratePlanId]['refund_type_label'];
                        $refundTypeLabel = str_replace(['(', ')'], '', $refundTypeLabel);
                        $refundBadgeClass = strpos($refundTypeLabel, 'İade Edilebilir') !== false ? 'refundable-badge' : 'non-refundable-badge';
                    }
                @endphp

                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100 flex items-center flex-wrap">
                            @isset($tableHeaders[$ratePlanId])
                                {{ $tableHeaders[$ratePlanId]['room_name'] }} - {{ $tableHeaders[$ratePlanId]['board_type_name'] }}
                            @else
                                @if(isset($ratePlan['room_name']) && isset($ratePlan['board_type_name']))
                                    {{ $ratePlan['room_name'] }} - {{ $ratePlan['board_type_name'] }}
                                @elseif(isset($room['name']) && isset($boardType['name']))
                                    {{ $room['name'] }} - {{ $boardType['name'] }}
                                @else
                                    Oda - Pansiyon Tipi
                                @endif
                            @endif

                            @if(!empty($refundTypeLabel))
                                @if(strpos($refundTypeLabel, 'İade Edilebilir') !== false)
                                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400 ml-3">
                                        {{ $refundTypeLabel }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-800/20 dark:text-amber-400 ml-3">
                                        {{ $refundTypeLabel }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        <div class="mt-2 flex items-center flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-md px-3 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-400">
                                {{ $pricingMethodLabel }}
                            </span>

                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                                </svg>
                                Fiyat Kontrolü
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto pricing-table-container">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider control-column sticky left-0 bg-gray-50 dark:bg-gray-800 shadow-sm z-10">
                                        Kontrol
                                    </th>
                                    
                                    @foreach($dateRange as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $dayName = $carbonDate->locale('tr')->isoFormat('ddd');
                                            $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                        @endphp
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider date-column {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                            <div>{{ $carbonDate->format('d.m.Y') }}</div>
                                            <div>{{ $dayName }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-600">
                                @if($isPerPerson)
                                    @for($i = 1; $i <= $maxOccupancy; $i++)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                                <div class="grid grid-cols-1 gap-2">
                                                    <div class="font-medium">{{ $i }} Kişi Fiyatı</div>
                                                    <div class="flex items-center gap-2">
                                                        <x-filament::input 
                                                            type="number"
                                                            wire:model="controls.{{ $ratePlanId }}.price_{{ $i }}"
                                                            step="0.01"
                                                            class="control-input bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring focus:ring-primary-500 dark:focus:ring-primary-400 focus:ring-opacity-50 rounded-md"
                                                            placeholder="Tümü"
                                                        />
                                                        <button 
                                                            type="button"
                                                            wire:click="applyControl({{ $ratePlanId }}, 'price_{{ $i }}')"
                                                            class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                              <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            @foreach($dateRange as $date)
                                                @php
                                                    $carbonDate = \Carbon\Carbon::parse($date);
                                                    $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                                    $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                                    $prices = $formData[$ratePlanId][$date]['prices'] ?? [];
                                                    $price = $prices[$i] ?? null;
                                                @endphp
                                                <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                                    <x-filament::input.wrapper>
                                                        <x-filament::input 
                                                            type="number"
                                                            wire:model="formData.{{ $ratePlanId }}.{{ $date }}.prices.{{ $i }}"
                                                            step="0.01"
                                                            class="w-16 pricing-input"
                                                            placeholder="-"
                                                        />
                                                    </x-filament::input.wrapper>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                @else
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                            <div class="grid grid-cols-1 gap-2">
                                                <div class="font-medium">Ünite Fiyatı</div>
                                                <div class="flex items-center gap-2">
                                                    <x-filament::input 
                                                        type="number"
                                                        wire:model="controls.{{ $ratePlanId }}.base_price"
                                                        step="0.01"
                                                        class="control-input bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring focus:ring-primary-500 dark:focus:ring-primary-400 focus:ring-opacity-50 rounded-md"
                                                        placeholder="Tümü"
                                                    />
                                                    <button 
                                                        type="button"
                                                        wire:click="applyControl({{ $ratePlanId }}, 'base_price')"
                                                        class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                          <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        @foreach($dateRange as $date)
                                            @php
                                                $carbonDate = \Carbon\Carbon::parse($date);
                                                $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                                $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                            @endphp
                                            <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                                <x-filament::input.wrapper>
                                                    <x-filament::input 
                                                        type="number"
                                                        wire:model="formData.{{ $ratePlanId }}.{{ $date }}.base_price"
                                                        step="0.01"
                                                        class="w-20 pricing-input"
                                                        placeholder="-"
                                                    />
                                                </x-filament::input.wrapper>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                                
                                <!-- Min Stay Row -->
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                        <div class="grid grid-cols-1 gap-2">
                                            <div class="font-medium">Min. Konaklama</div>
                                            <div class="flex items-center gap-2">
                                                <x-filament::input 
                                                    type="number"
                                                    wire:model="controls.{{ $ratePlanId }}.min_stay"
                                                    min="1"
                                                    class="control-input bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring focus:ring-primary-500 dark:focus:ring-primary-400 focus:ring-opacity-50 rounded-md"
                                                    placeholder="Tümü"
                                                />
                                                <button 
                                                    type="button"
                                                    wire:click="applyControl({{ $ratePlanId }}, 'min_stay')"
                                                    class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                      <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($dateRange as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                            $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                        @endphp
                                        <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                            <x-filament::input.wrapper>
                                                <x-filament::input 
                                                    type="number"
                                                    wire:model="formData.{{ $ratePlanId }}.{{ $date }}.min_stay"
                                                    min="1"
                                                    class="w-16 pricing-input-sm"
                                                    placeholder="-"
                                                />
                                            </x-filament::input.wrapper>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Quantity Row -->
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                        <div class="grid grid-cols-1 gap-2">
                                            <div class="font-medium">Günlük Stok</div>
                                            <div class="flex items-center gap-2">
                                                <x-filament::input 
                                                    type="number"
                                                    wire:model="controls.{{ $ratePlanId }}.quantity"
                                                    min="0"
                                                    class="control-input bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring focus:ring-primary-500 dark:focus:ring-primary-400 focus:ring-opacity-50 rounded-md"
                                                    placeholder="Tümü"
                                                />
                                                <button 
                                                    type="button"
                                                    wire:click="applyControl({{ $ratePlanId }}, 'quantity')"
                                                    class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                      <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($dateRange as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                            $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                        @endphp
                                        <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                            <x-filament::input.wrapper>
                                                <x-filament::input 
                                                    type="number"
                                                    wire:model="formData.{{ $ratePlanId }}.{{ $date }}.quantity"
                                                    min="0"
                                                    class="w-16 pricing-input-sm"
                                                    placeholder="-"
                                                />
                                            </x-filament::input.wrapper>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Sales Type Row (Sor-Sat) -->
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                        <div class="grid grid-cols-1 gap-2">
                                            <div class="font-medium">Sor-Sat</div>
                                            <div class="flex items-center gap-2">
                                                <select
                                                    wire:model="controls.{{ $ratePlanId }}.ask_sell"
                                                    class="control-input text-sm block bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring-1 focus:ring-primary-500 dark:focus:ring-primary-400"
                                                >
                                                    <option value="0" selected>Kapalı</option>
                                                    <option value="1">Açık</option>
                                                </select>
                                                <button 
                                                    type="button"
                                                    wire:click="applyControl({{ $ratePlanId }}, 'ask_sell')"
                                                    class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                      <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    @foreach($dateRange as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                            $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                            $salesType = $formData[$ratePlanId][$date]['sales_type'] ?? 'direct';
                                            $isAskSell = ($salesType === 'ask_sell');
                                        @endphp
                                        <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                            <select
                                                wire:model="formData.{{ $ratePlanId }}.{{ $date }}.sales_type"
                                                class="text-sm block bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring-1 focus:ring-primary-500 dark:focus:ring-primary-400 sales-select"
                                            >
                                                <option value="direct">Kapalı</option>
                                                <option value="ask_sell">Açık</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Status Row -->
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-900 shadow-sm z-10 control-column">
                                        <div class="grid grid-cols-1 gap-2">
                                            <div class="font-medium">Durum</div>
                                            <div class="flex items-center gap-2">
                                                <select
                                                    wire:model="controls.{{ $ratePlanId }}.status"
                                                    class="control-input text-sm block bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring-1 focus:ring-primary-500 dark:focus:ring-primary-400"
                                                >
                                                    <option value="1" selected>Açık</option>
                                                    <option value="0">Kapalı</option>
                                                </select>
                                                <button 
                                                    type="button"
                                                    wire:click="applyControl({{ $ratePlanId }}, 'status')"
                                                    class="flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 p-1.5 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                      <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($dateRange as $date)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $isWeekend = in_array($carbonDate->dayOfWeek, [0, 6]);
                                            $isException = $formData[$ratePlanId][$date]['is_exception'] ?? false;
                                            // Status defaults to true (active)
                                            $isActive = $formData[$ratePlanId][$date]['status'] ?? true;
                                        @endphp
                                        <td class="px-2 py-1 whitespace-nowrap text-sm {{ $isWeekend ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} {{ $isException ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                            <select
                                                wire:model="formData.{{ $ratePlanId }}.{{ $date }}.status"
                                                class="text-sm block bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:border-primary-500 dark:focus:border-primary-400 focus:ring-1 focus:ring-primary-500 dark:focus:ring-primary-400 status-select"
                                            >
                                                <option value="1">Açık</option>
                                                <option value="0">Kapalı</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end mt-6">
                <x-filament::button
                    wire:click="savePricing"
                    color="success"
                >
                    Fiyat Değişikliklerini Kaydet
                </x-filament::button>
            </div>
        </div>
    @endif
    
    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-50 dark:bg-blue-900/20 border border-gray-200 dark:border-gray-700 rounded"></div>
                <span class="ml-2">Hafta sonu</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-50 dark:bg-yellow-900/20 border border-gray-200 dark:border-gray-700 rounded"></div>
                <span class="ml-2">İstisna</span>
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', function () {
                // Bulk update with arrow keys
                window.addEventListener('keydown', function(e) {
                    if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
                        const currentValue = parseFloat(e.target.value || 0);
                        
                        if (e.key === 'ArrowUp') {
                            e.target.value = (currentValue + 10).toFixed(2);
                            e.preventDefault();
                            e.target.dispatchEvent(new Event('input', { bubbles: true }));
                        } else if (e.key === 'ArrowDown') {
                            e.target.value = Math.max(0, (currentValue - 10)).toFixed(2);
                            e.preventDefault();
                            e.target.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                });
            });
        </script>
    @endpush
</div>