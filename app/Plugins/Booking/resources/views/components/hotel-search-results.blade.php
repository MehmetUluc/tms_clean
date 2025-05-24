@php
    use App\Plugins\Accommodation\Models\Hotel;
    use App\Plugins\Amenities\Models\HotelAmenity;
    use App\Plugins\Booking\Models\BoardType;
@endphp

<div class="hotel-search-results">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="filters-sidebar bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>
                
                <!-- Price Range -->
                <div class="mb-6">
                    <h4 class="font-medium mb-2">Price per night</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span>₺{{ number_format($filters['priceRange'][0]) }}</span>
                            <span>₺{{ number_format($filters['priceRange'][1]) }}</span>
                        </div>
                        <div class="relative">
                            <input type="range" 
                                   wire:model.live="priceRange.0" 
                                   min="0" 
                                   max="5000" 
                                   class="w-full">
                            <input type="range" 
                                   wire:model.live="priceRange.1" 
                                   min="0" 
                                   max="10000" 
                                   class="w-full">
                        </div>
                    </div>
                </div>
                
                <!-- Star Rating -->
                <div class="mb-6">
                    <h4 class="font-medium mb-2">Star Rating</h4>
                    <div class="space-y-2">
                        @foreach([5, 4, 3, 2, 1] as $stars)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="starRatings" 
                                       value="{{ $stars }}"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 flex items-center">
                                    @for($i = 0; $i < $stars; $i++)
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Board Types -->
                <div class="mb-6">
                    <h4 class="font-medium mb-2">Board Type</h4>
                    <div class="space-y-2">
                        @foreach(BoardType::all() as $boardType)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="boardTypes" 
                                       value="{{ $boardType->id }}"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">{{ $boardType->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Hotel Amenities -->
                <div class="mb-6">
                    <h4 class="font-medium mb-2">Amenities</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach(HotelAmenity::take(10)->get() as $amenity)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="amenities" 
                                       value="{{ $amenity->id }}"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Sort By -->
                <div>
                    <h4 class="font-medium mb-2">Sort By</h4>
                    <select wire:model.live="sortBy" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="recommended">Recommended</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="rating">Star Rating</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Hotel Results -->
        <div class="lg:col-span-3">
            @if($hotels->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hotels found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or search criteria.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($hotels as $hotel)
                        <div class="hotel-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden {{ $selectedHotelId == $hotel->id ? 'selected' : '' }}"
                             wire:click="selectHotel({{ $hotel->id }})">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Hotel Image -->
                                <div class="md:col-span-1">
                                    @if($hotel->cover_image_url)
                                        <img src="{{ $hotel->cover_image_url }}" 
                                             alt="{{ $hotel->name }}"
                                             class="w-full h-48 md:h-full object-cover">
                                    @else
                                        <div class="w-full h-48 md:h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Hotel Details -->
                                <div class="md:col-span-2 p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $hotel->name }}
                                            </h3>
                                            <div class="flex items-center mt-1">
                                                @for($i = 0; $i < $hotel->star_rating; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $hotel->region->name ?? 'Unknown Location' }}
                                            </p>
                                            
                                            <!-- Amenities -->
                                            @if($hotel->amenities && count($hotel->amenities) > 0)
                                                <div class="flex flex-wrap gap-2 mt-3">
                                                    @php
                                                        $hotelAmenities = is_array($hotel->amenities) ? collect($hotel->amenities) : $hotel->amenities;
                                                    @endphp
                                                    @foreach($hotelAmenities->take(5) as $amenity)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            @if(is_object($amenity) && property_exists($amenity, 'icon') && $amenity->icon)
                                                                <x-dynamic-component :component="$amenity->icon" class="w-3 h-3 mr-1" />
                                                            @endif
                                                            {{ is_object($amenity) ? $amenity->name : $amenity }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="text-right ml-4">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Starting from</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                                ₺{{ number_format($hotel->min_price ?? 500) }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">per night</p>
                                            <button type="button"
                                                    class="mt-2 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                                View Rooms
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Room Selection (shown when hotel is selected) -->
                                    @if($selectedHotelId == $hotel->id)
                                        <div id="hotel-{{ $hotel->id }}-rooms" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <h4 class="font-semibold mb-3">Available Rooms</h4>
                                            <div class="space-y-3">
                                                @foreach($hotel->rooms as $room)
                                                    @foreach($room->ratePlans as $ratePlan)
                                                        <div class="room-card border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                            <div class="flex justify-between items-center">
                                                                <div>
                                                                    <h5 class="font-medium">{{ $room->name }}</h5>
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                        {{ $ratePlan->boardType->name }} • 
                                                                        Max {{ $room->capacity_adults ?? 2 }} guests
                                                                    </p>
                                                                    @if($room->amenities && count($room->amenities) > 0)
                                                                        <div class="flex gap-2 mt-2">
                                                                            @php
                                                                                $amenities = is_array($room->amenities) ? collect($room->amenities) : $room->amenities;
                                                                            @endphp
                                                                            @foreach($amenities->take(3) as $amenity)
                                                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                                                    {{ is_object($amenity) ? $amenity->name : $amenity }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-lg font-semibold">₺{{ number_format($room->base_price ?? 500) }}</p>
                                                                    <p class="text-sm text-gray-500">per night</p>
                                                                    <button type="button"
                                                                            wire:click="addRoom({
                                                                                'room_id': {{ $room->id }},
                                                                                'rate_plan_id': {{ $ratePlan->id }},
                                                                                'room_name': '{{ $room->name }}',
                                                                                'board_type': '{{ $ratePlan->boardType->code }}',
                                                                                'price_per_night': {{ $room->base_price ?? 500 }}
                                                                            })"
                                                                            class="mt-2 px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                                        Add Room
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $hotels->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Selected Rooms Summary (Floating Cart) -->
    @if(count($selectedRooms) > 0)
        <div class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 max-w-sm">
            <h4 class="font-semibold mb-2">Selected Rooms ({{ count($selectedRooms) }})</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach($selectedRooms as $index => $room)
                    <div class="flex justify-between items-center text-sm">
                        <span>{{ $room['room_name'] }} - {{ $room['board_type'] }}</span>
                        <div class="flex items-center gap-2">
                            <span>₺{{ number_format($room['total_price']) }}</span>
                            <button wire:click="removeRoom({{ $index }})" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
                <div class="flex justify-between font-semibold">
                    <span>Total:</span>
                    <span>₺{{ number_format(array_sum(array_column($selectedRooms, 'total_price'))) }}</span>
                </div>
            </div>
        </div>
    @endif
</div>