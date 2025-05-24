@props([
    'hotel' => null,
    'selectedRooms' => [],
    'checkIn' => null,
    'checkOut' => null,
    'nights' => 0,
    'adults' => 0,
    'children' => 0
])

@if($hotel)
<div class="booking-detail-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Accommodation Details
    </h3>
    
    <div class="space-y-3">
        <div class="flex items-start">
            <div class="flex-1">
                <h4 class="font-semibold text-base">
                    {{ $hotel->name }} 
                    @for($i = 0; $i < $hotel->star_rating; $i++)⭐@endfor
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $hotel->region->name ?? 'Unknown location' }}</p>
            </div>
        </div>
        
        <!-- Date and guest information -->
        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Check-in</p>
                <p class="font-medium">{{ \Carbon\Carbon::parse($checkIn)->format('D, d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Check-out</p>
                <p class="font-medium">{{ \Carbon\Carbon::parse($checkOut)->format('D, d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                <p class="font-medium">{{ $nights }} {{ $nights === 1 ? 'Night' : 'Nights' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Guests</p>
                <p class="font-medium">
                    {{ $adults }} {{ $adults === 1 ? 'Adult' : 'Adults' }}
                    @if($children > 0)
                        , {{ $children }} {{ $children === 1 ? 'Child' : 'Children' }}
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Room details -->
        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
            <h5 class="font-medium mb-2">Selected Rooms</h5>
            @foreach($selectedRooms as $room)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">{{ $room['room_name'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $room['board_type_name'] }}</p>
                            @if($room['is_per_person'] ?? false)
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Price per person</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">₺{{ number_format($room['total_price'], 2) }}</p>
                            <p class="text-xs text-gray-500">₺{{ number_format($room['price_per_night'], 2) }}/night</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif