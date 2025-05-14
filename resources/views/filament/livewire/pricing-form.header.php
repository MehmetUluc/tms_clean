                    <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                            <div class="flex flex-col space-y-2">
                                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
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
                                    
                                    @php
                                        $refundTypeLabel = '';
                                        $refundBadgeColor = '';
                                        
                                        if (isset($tableHeaders[$ratePlanId]['refund_type_label'])) {
                                            $refundTypeLabel = $tableHeaders[$ratePlanId]['refund_type_label'];
                                            $refundBadgeColor = strpos($refundTypeLabel, 'İade Edilebilir') !== false ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';
                                        }
                                    @endphp
                                    
                                    @if(!empty($refundTypeLabel))
                                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $refundBadgeColor }}">
                                            {{ str_replace(['(', ')'], '', $refundTypeLabel) }}
                                        </span>
                                    @endif
                                </h3>
                                
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-800">
                                        @isset($tableHeaders[$ratePlanId]['pricing_method_label'])
                                            {{ str_replace(['(', ')'], '', $tableHeaders[$ratePlanId]['pricing_method_label']) }}
                                        @else
                                            {{ $isPerPerson ? 'Kişi Başı' : 'Ünite Bazlı' }} Fiyatlandırma
                                        @endif
                                    </span>
                                    
                                    <div class="ml-4 flex items-center text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        {{ \Carbon\Carbon::now()->format('d.m.Y') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2 md:mt-0">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Hızlı Düzenle
                                </button>
                            </div>
                        </div>
                    </div>