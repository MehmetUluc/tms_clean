@php
    $subTotal = 0;
    foreach ($selectedRooms as $room) {
        $subTotal += $room['total_price'] ?? 0;
    }
    
    $extrasTotal = 0;
    if ($airportTransfer) $extrasTotal += 50;
    if ($travelInsurance) $extrasTotal += 25;
    
    $grandTotal = $subTotal + $extrasTotal;
    
    $checkInDate = \Carbon\Carbon::parse($checkIn);
    $checkOutDate = \Carbon\Carbon::parse($checkOut);
@endphp

<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Booking Summary</h3>
    
    <!-- Stay Details -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 dark:text-gray-400">Check-in</span>
            <span class="font-medium">{{ $checkInDate->format('D, M d, Y') }}</span>
        </div>
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 dark:text-gray-400">Check-out</span>
            <span class="font-medium">{{ $checkOutDate->format('D, M d, Y') }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 dark:text-gray-400">Duration</span>
            <span class="font-medium">{{ $nights }} {{ Str::plural('Night', $nights) }}</span>
        </div>
    </div>
    
    <!-- Room Details -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
        <h4 class="font-medium mb-3">Room Details</h4>
        @foreach($selectedRooms as $room)
            <div class="mb-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $room['room_name'] }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $room['board_type_name'] ?? $room['board_type'] }}
                            @if(isset($room['is_per_person']) && $room['is_per_person'])
                                <span class="text-xs"> (Per Person)</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $room['nights'] }} nights × ₺{{ number_format($room['price_per_night']) }}
                            @if(isset($room['is_per_person']) && $room['is_per_person'])
                                /person
                            @endif
                        </p>
                    </div>
                    <span class="font-medium">₺{{ number_format($room['total_price']) }}</span>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Extras -->
    @if($airportTransfer || $travelInsurance)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <h4 class="font-medium mb-3">Extras</h4>
            @if($airportTransfer)
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Airport Transfer</span>
                    <span>₺50</span>
                </div>
            @endif
            @if($travelInsurance)
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Travel Insurance</span>
                    <span>₺25</span>
                </div>
            @endif
        </div>
    @endif
    
    <!-- Special Requests -->
    @if($specialRequests)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <h4 class="font-medium mb-2">Special Requests</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $specialRequests }}</p>
        </div>
    @endif
    
    <!-- Price Breakdown -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
            <span>₺{{ number_format($subTotal) }}</span>
        </div>
        @if($extrasTotal > 0)
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 dark:text-gray-400">Extras</span>
                <span>₺{{ number_format($extrasTotal) }}</span>
            </div>
        @endif
        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
            <span class="text-lg font-semibold">Total</span>
            <span class="text-lg font-semibold text-primary-600">₺{{ number_format($grandTotal) }}</span>
        </div>
    </div>
    
    <!-- Price Guarantee -->
    <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Best Price Guarantee</p>
                <p class="text-xs text-green-600 dark:text-green-400">We guarantee the lowest price for your stay</p>
            </div>
        </div>
    </div>
</div>