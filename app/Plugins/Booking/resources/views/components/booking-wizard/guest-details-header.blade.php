@props([
    'selectedHotel' => null,
    'selectedRooms' => [],
    'checkIn' => null,
    'checkOut' => null,
    'nights' => 0
])

@if($selectedHotel && count($selectedRooms) > 0)
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <img class="h-20 w-20 rounded-lg object-cover" 
                     src="{{ $selectedHotel->featured_image ?? '/images/placeholder/hotel.jpg' }}" 
                     alt="{{ $selectedHotel->name }}">
            </div>
            <div class="flex-grow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedHotel->name }}</h3>
                <div class="flex items-center mt-1">
                    <div class="flex items-center">
                        @for($i = 0; $i < $selectedHotel->stars; $i++)
                            <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-sm text-gray-500">{{ $selectedHotel->region->name ?? 'Unknown Location' }}</span>
                </div>
                
                <div class="mt-3 flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }} - {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}
                    <span class="mx-2">•</span>
                    {{ $nights }} {{ $nights === 1 ? 'Night' : 'Nights' }}
                </div>
                
                <div class="mt-2">
                    @foreach($selectedRooms as $room)
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            • {{ $room['roomName'] }} - {{ $room['boardTypeName'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif