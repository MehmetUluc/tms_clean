<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Otel ve Oda Bilgileri -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Hotel & Room Details</h3>
            
            @if(!empty($hotel))
                <div class="mb-3 pb-3 border-b border-gray-100">
                    <div class="flex justify-between">
                        <p class="text-sm font-medium text-gray-700">Hotel:</p>
                        <p class="text-sm text-gray-900">{{ $hotel['name'] }}</p>
                    </div>
                    
                    @if(!empty($hotel['star_rating']))
                        <div class="flex justify-between mt-1">
                            <p class="text-sm font-medium text-gray-700">Rating:</p>
                            <p class="text-sm text-yellow-500">
                                @for($i = 0; $i < $hotel['star_rating']; $i++)
                                    ★
                                @endfor
                            </p>
                        </div>
                    @endif
                    
                    @if(!empty($hotel['address']))
                        <div class="flex justify-between mt-1">
                            <p class="text-sm font-medium text-gray-700">Address:</p>
                            <p class="text-sm text-gray-900 text-right">{{ $hotel['address'] }}</p>
                        </div>
                    @endif
                </div>
            @endif
            
            @if(!empty($room))
                <div class="mb-3">
                    <div class="flex justify-between">
                        <p class="text-sm font-medium text-gray-700">Room:</p>
                        <p class="text-sm text-gray-900">{{ $room['name'] }}</p>
                    </div>
                    
                    @if(!empty($room['type']))
                        <div class="flex justify-between mt-1">
                            <p class="text-sm font-medium text-gray-700">Type:</p>
                            <p class="text-sm text-gray-900">{{ $room['type'] }}</p>
                        </div>
                    @endif
                    
                    @if(!empty($room['max_occupancy']))
                        <div class="flex justify-between mt-1">
                            <p class="text-sm font-medium text-gray-700">Max Guests:</p>
                            <p class="text-sm text-gray-900">{{ $room['max_occupancy'] }}</p>
                        </div>
                    @endif
                    
                    @if(!empty($room['selected_board_type'] ?? $room['board_type']))
                        <div class="flex justify-between mt-1">
                            <p class="text-sm font-medium text-gray-700">Board:</p>
                            @php 
                                $boardType = $room['selected_board_type'] ?? $room['board_type']; 
                            @endphp
                            <p class="text-sm text-gray-900">{{ $boardType['name'] }} ({{ $boardType['code'] }})</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Tarih ve Misafir Bilgileri -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Stay Details</h3>
            
            <div class="mb-3 pb-3 border-b border-gray-100">
                <div class="flex justify-between">
                    <p class="text-sm font-medium text-gray-700">Check-in:</p>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($check_in)->format('d.m.Y') }}</p>
                </div>
                
                <div class="flex justify-between mt-1">
                    <p class="text-sm font-medium text-gray-700">Check-out:</p>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($check_out)->format('d.m.Y') }}</p>
                </div>
                
                <div class="flex justify-between mt-1">
                    <p class="text-sm font-medium text-gray-700">Nights:</p>
                    <p class="text-sm text-gray-900">{{ $nights }}</p>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="flex justify-between">
                    <p class="text-sm font-medium text-gray-700">Adults:</p>
                    <p class="text-sm text-gray-900">{{ $adults }}</p>
                </div>
                
                @if(!empty($children) && $children > 0)
                    <div class="flex justify-between mt-1">
                        <p class="text-sm font-medium text-gray-700">Children:</p>
                        <p class="text-sm text-gray-900">{{ $children }}</p>
                    </div>
                @endif
                
                @if(!empty($primary_guest))
                    <div class="flex justify-between mt-3">
                        <p class="text-sm font-medium text-gray-700">Primary Guest:</p>
                        <p class="text-sm text-gray-900">{{ $primary_guest['first_name'] }} {{ $primary_guest['last_name'] }}</p>
                    </div>
                    
                    <div class="flex justify-between mt-1">
                        <p class="text-sm font-medium text-gray-700">Contact:</p>
                        <p class="text-sm text-gray-900">{{ $primary_guest['email'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Fiyat Özeti -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Price Summary</h3>
        
        <div class="space-y-2">
            <div class="flex justify-between">
                <p class="text-sm font-medium text-gray-700">Room Price (per night):</p>
                <p class="text-sm text-gray-900">{{ number_format($room['price_per_night'] ?? 0, 0) }} TL</p>
            </div>
            
            {{-- Şimdilik board type fiyat modifikasyonları devre dışı
            @if(!empty($room['selected_board_type']['price_modifier'] ?? $room['board_type']['price_modifier'] ?? 0) && 
                ($room['selected_board_type']['price_modifier'] ?? $room['board_type']['price_modifier'] ?? 0) > 0) --}}
                {{-- <div class="flex justify-between">
                    <p class="text-sm font-medium text-gray-700">Board Price (per night):</p>
                    @php 
                        $boardModifier = 0; // Şimdilik price_modifier yok
                    @endphp
                    <p class="text-sm text-gray-900">{{ number_format($boardModifier, 0) }} TL</p>
                </div>
            @endif --}}
            
            <div class="flex justify-between">
                <p class="text-sm font-medium text-gray-700">Number of Nights:</p>
                <p class="text-sm text-gray-900">{{ $nights }}</p>
            </div>
            
            <div class="pt-2 mt-2 border-t border-gray-100 flex justify-between">
                <p class="text-base font-bold text-gray-900">Total Price:</p>
                <p class="text-base font-bold text-primary-600">{{ number_format($total_amount, 0) }} TL</p>
            </div>
        </div>
    </div>
</div>