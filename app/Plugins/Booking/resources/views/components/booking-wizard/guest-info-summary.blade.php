@props([
    'guest' => [],
    'specialRequests' => null
])

@if(!empty($guest))
<div class="booking-detail-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Guest Information
    </h3>
    
    <div class="space-y-2">
        <p>
            <span class="text-gray-500 dark:text-gray-400">Primary Guest:</span> 
            <span class="font-medium">{{ $guest['first_name'] ?? '' }} {{ $guest['last_name'] ?? '' }}</span>
        </p>
        <p>
            <span class="text-gray-500 dark:text-gray-400">Email:</span> 
            <span class="font-medium">{{ $guest['email'] ?? '' }}</span>
        </p>
        <p>
            <span class="text-gray-500 dark:text-gray-400">Phone:</span> 
            <span class="font-medium">{{ $guest['phone'] ?? '' }}</span>
        </p>
        
        @if($specialRequests)
            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400 mb-1">Special Requests:</p>
                <p class="text-sm italic">{{ $specialRequests }}</p>
            </div>
        @endif
    </div>
</div>
@endif