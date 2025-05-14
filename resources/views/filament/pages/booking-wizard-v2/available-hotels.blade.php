<div class="space-y-6">
    @if (empty($this->availableHotels))
        <div class="flex items-center justify-center p-6 bg-gray-100 rounded-lg">
            <div class="text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-medium text-gray-900">No Hotels Available</h3>
                <p class="text-gray-500">Please select a region to see available hotels or try different dates.</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($this->availableHotels as $hotel)
                <div class="overflow-hidden bg-white rounded-xl border border-gray-200 hover:shadow-md transition duration-300">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                        @if(!empty($hotel['cover_image']))
                            <img src="{{ $hotel['cover_image'] }}" alt="{{ $hotel['name'] }}" class="object-cover w-full h-full">
                        @else
                            <div class="flex items-center justify-center h-full bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $hotel['name'] }}</h3>
                            <div class="flex items-center">
                                @for($i = 0; $i < $hotel['star_rating']; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        
                        <p class="mt-2 text-sm text-gray-500">{{ Str::limit($hotel['description'] ?? 'No description available', 100) }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-xl font-bold text-primary-600">{{ number_format($hotel['base_price'], 0) }} TL</span>
                                    <span class="text-xs text-gray-500">/night</span>
                                </div>
                                
                                <button
                                    type="button"
                                    wire:click="selectHotel({{ $hotel['id'] }})"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Select Hotel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>