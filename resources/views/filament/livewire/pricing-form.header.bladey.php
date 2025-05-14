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
        
        /* Başlık için özel stil */
        .rate-plan-header {
            background-image: linear-gradient(to right, #f9fafb, #f3f4f6);
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.25rem;
        }
        
        .rate-plan-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .rate-plan-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.125rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        .refundable-badge {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .non-refundable-badge {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .pricing-method-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 0.375rem;
            padding: 0.125rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
    
    @if(empty($ratePlans) || empty($dateRange))
        <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
            <div class="flex flex-col items-center justify-center space-y-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-lg font-medium text-gray-900">Fiyat verileri yüklenemedi</p>
                <p class="text-sm text-gray-500">Lütfen formu yeniden oluşturun ya da sayfayı yenileyin.</p>
                <button 
                    type="button" 
                    class="mt-2 px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600"
                    onclick="window.location.reload();"
                >
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
                    
                    // Oda ve pansiyon türü 
                    $roomName = '';
                    $boardTypeName = '';
                    
                    if (isset($tableHeaders[$ratePlanId])) {
                        $roomName = $tableHeaders[$ratePlanId]['room_name'];
                        $boardTypeName = $tableHeaders[$ratePlanId]['board_type_name'];
                    } elseif (isset($ratePlan['room_name']) && isset($ratePlan['board_type_name'])) {
                        $roomName = $ratePlan['room_name'];
                        $boardTypeName = $ratePlan['board_type_name'];
                    } elseif (isset($room['name']) && isset($boardType['name'])) {
                        $roomName = $room['name'];
                        $boardTypeName = $boardType['name'];
                    } else {
                        $roomName = 'Oda';
                        $boardTypeName = 'Pansiyon Tipi';
                    }
                @endphp
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="rate-plan-header">
                        <div class="rate-plan-title">
                            {{ $roomName }} - {{ $boardTypeName }}
                            
                            @if(!empty($refundTypeLabel))
                                <span class="rate-plan-badge {{ $refundBadgeClass }}">
                                    {{ $refundTypeLabel }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-2 flex items-center flex-wrap gap-2">
                            <span class="pricing-method-badge">
                                {{ $pricingMethodLabel }}
                            </span>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                                </svg>
                                Fiyat Kontrolü
                            </div>
                        </div>
                    </div>