@props([
    'hotel' => null,
    'selectedRooms' => [],
    'checkIn' => null,
    'checkOut' => null,
    'nights' => 0,
    'adults' => 0,
    'children' => 0
])

<div class="guest-form-section">
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Your Selection Summary
        </h3>
        
        @if($hotel)
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <p class="font-medium text-base">
                    {{ $hotel->name }} 
                    @for($i = 0; $i < $hotel->star_rating; $i++)⭐@endfor
                </p>
                <p class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $hotel->region->name ?? 'Unknown location' }}
                </p>
                <p class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }} - {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }} 
                    ({{ $nights }} {{ $nights === 1 ? 'night' : 'nights' }})
                </p>
                <p class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ $adults }} {{ $adults === 1 ? 'Adult' : 'Adults' }}
                    @if($children > 0)
                        , {{ $children }} {{ $children === 1 ? 'Child' : 'Children' }}
                    @endif
                </p>
            </div>
            
            <!-- Selected rooms -->
            <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-800">
                <p class="font-medium text-sm mb-2">Selected Rooms:</p>
                <ul class="space-y-1">
                    @foreach($selectedRooms as $room)
                        <li class="text-sm flex items-center justify-between">
                            <span>
                                <span class="font-medium">{{ $room['room_name'] }}</span> - {{ $room['board_type_name'] }}
                            </span>
                            <span class="font-semibold">₺{{ number_format($room['total_price'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>