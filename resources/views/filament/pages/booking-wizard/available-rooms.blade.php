<div>
    @if(empty($this->availableRooms))
        <div class="p-8 bg-gray-50 rounded-lg text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-700 font-medium text-lg">No available rooms found</p>
            <p class="text-gray-500 mt-2">Please adjust your search criteria or select different dates.</p>
            <div class="mt-6">
                <button 
                    type="button"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-md font-medium transition"
                    wire:click="previousStep"
                >
                    Change Search Criteria
                </button>
            </div>
        </div>
    @else
        <div class="space-y-4">
            <h3 class="text-lg font-medium">Available Room Types</h3>
            
            <div class="grid grid-cols-1 gap-4">
                @foreach($this->availableRooms as $roomType)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="p-4">
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold">{{ $roomType['name'] }}</h4>
                                    <p class="text-sm text-gray-500">Max Occupancy: {{ $roomType['max_occupancy'] }} persons</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-primary-600">
                                        {{ number_format($roomType['price_per_night'], 2) }} USD
                                    </p>
                                    <p class="text-xs text-gray-500">per night</p>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p class="text-sm text-gray-700">{{ $roomType['description'] }}</p>
                            </div>
                            
                            <div class="mt-3 flex justify-between items-center">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ $roomType['available_count'] }}</span> rooms available
                                </p>
                                
                                <p class="text-sm font-semibold">
                                    Total for {{ $roomType['nights'] }} nights: 
                                    <span class="text-primary-600">{{ number_format($roomType['total_base_price'], 2) }} USD</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>