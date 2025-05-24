@props([
    'selectedRooms' => [],
    'subtotal' => 0,
    'children' => 0,
    'childrenAges' => [],
    'airportTransferPrice' => 0,
    'travelInsurancePrice' => 0,
    'discount' => 0,
    'taxes' => 0,
    'totalPrice' => 0
])

<div class="price-breakdown-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        Price Breakdown
    </h3>
    
    <div class="space-y-2">
        <!-- Room subtotal -->
        <div class="flex justify-between py-2">
            <span class="text-gray-600 dark:text-gray-400">
                Room Total ({{ count($selectedRooms) }} {{ count($selectedRooms) === 1 ? 'room' : 'rooms' }})
            </span>
            <span class="font-medium">₺{{ number_format($subtotal, 2) }}</span>
        </div>
        
        <!-- Child pricing details if applicable -->
        @if($children > 0 && !empty($childrenAges))
            <div class="text-sm text-gray-500 dark:text-gray-500 italic pl-4">
                Children ages: {{ implode(', ', array_map(fn($age) => $age . ' years', $childrenAges)) }}
            </div>
        @endif
        
        <!-- Add-ons -->
        @if($airportTransferPrice > 0)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Airport Transfer</span>
                <span class="font-medium">₺{{ number_format($airportTransferPrice, 2) }}</span>
            </div>
        @endif
        
        @if($travelInsurancePrice > 0)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Travel Insurance</span>
                <span class="font-medium">₺{{ number_format($travelInsurancePrice, 2) }}</span>
            </div>
        @endif
        
        <!-- Discount -->
        @if($discount > 0)
            <div class="flex justify-between py-2 text-green-600 dark:text-green-400">
                <span>Discount</span>
                <span class="font-medium">-₺{{ number_format($discount, 2) }}</span>
            </div>
        @endif
        
        <!-- Taxes -->
        <div class="flex justify-between py-2">
            <span class="text-gray-600 dark:text-gray-400">Taxes & Fees</span>
            <span class="font-medium">₺{{ number_format($taxes, 2) }}</span>
        </div>
        
        <!-- Total -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold">Total Amount</span>
                <span class="text-2xl font-bold text-primary-600">₺{{ number_format($totalPrice, 2) }}</span>
            </div>
        </div>
    </div>
</div>