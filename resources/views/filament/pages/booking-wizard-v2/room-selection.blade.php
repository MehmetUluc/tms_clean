<div class="space-y-6">
    @if (empty($this->availableRooms))
        <div class="flex items-center justify-center p-6 bg-gray-100 rounded-lg">
            <div class="text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-medium text-gray-900">No Rooms Available</h3>
                <p class="text-gray-500">Please select a hotel first or try different dates for availability.</p>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach($this->availableRooms as $room)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition duration-300">
                    <div class="flex flex-col md:flex-row">
                        <!-- Room Image -->
                        <div class="md:w-1/3 bg-gray-200">
                            @if(!empty($room['image']))
                                <img src="{{ $room['image'] }}" alt="{{ $room['name'] }}" class="object-cover w-full h-full min-h-[200px]">
                            @else
                                <div class="flex items-center justify-center h-full min-h-[200px] bg-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Room Details -->
                        <div class="md:w-2/3 p-4">
                            <div class="flex flex-col h-full">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $room['name'] }}</h3>
                                            <p class="text-sm text-gray-500">{{ $room['type'] }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $room['is_smoking'] ? 'Smoking' : 'Non-Smoking' }}
                                            </span>
                                            
                                            @if(!empty($room['size']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $room['size'] }} m²
                                                </span>
                                            @endif
                                            
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Max {{ $room['max_occupancy'] }} guests
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($room['description'] ?? 'No description available', 150) }}</p>
                                    
                                    @if(!empty($room['features']))
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Room Features</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($room['features'] as $feature)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $feature }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="border-t pt-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Board Options</h4>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                                            @foreach($room['board_types'] as $boardType)
                                                <div class="flex items-center justify-between p-2 border rounded-lg hover:bg-gray-50 {{ $loop->first ? 'bg-blue-50 border-blue-200' : '' }}">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input 
                                                            type="radio" 
                                                            name="board_type_{{ $room['id'] }}" 
                                                            value="{{ $boardType['id'] }}" 
                                                            class="form-radio h-4 w-4 text-primary-600" 
                                                            {{ $loop->first ? 'checked' : '' }}
                                                        >
                                                        <span class="ml-2 text-sm font-medium text-gray-900">{{ $boardType['name'] }} ({{ $boardType['code'] }})</span>
                                                    </label>
                                                    <span class="text-sm font-medium {{ $boardType['price_modifier'] > 0 ? 'text-primary-600' : 'text-gray-600' }}">
                                                        @if($boardType['price_modifier'] > 0)
                                                            +{{ number_format($boardType['price_modifier'], 0) }} TL
                                                        @else
                                                            Included
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <span class="text-xl font-bold text-primary-600">{{ number_format($room['price_per_night'], 0) }} TL</span>
                                                <span class="text-xs text-gray-500">/night</span>
                                            </div>
                                            
                                            <button
                                                type="button"
                                                wire:click="selectRoom({{ $room['id'] }})"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                            >
                                                Select Room
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>