@php
    $nights = $nights ?? 0;
    $checkInDate = $checkIn ? \Carbon\Carbon::parse($checkIn) : null;
    $checkOutDate = $checkOut ? \Carbon\Carbon::parse($checkOut) : null;
@endphp

<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="flex items-center text-blue-700 dark:text-blue-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">{{ $nights }} {{ Str::plural('Night', $nights) }}</span>
            </div>
            
            @if($checkInDate && $checkOutDate)
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $checkInDate->format('M d, Y') }} → {{ $checkOutDate->format('M d, Y') }}
                </div>
            @endif
        </div>
        
        <div class="flex items-center space-x-3 text-sm">
            <div class="flex items-center text-gray-700 dark:text-gray-300">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>{{ $adults }} {{ Str::plural('Adult', $adults) }}</span>
            </div>
            
            @if($children > 0)
                <div class="flex items-center text-gray-700 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ $children }} {{ Str::plural('Child', $children) }}</span>
                </div>
            @endif
        </div>
    </div>
</div>