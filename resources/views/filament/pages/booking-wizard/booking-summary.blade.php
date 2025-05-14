<div class="space-y-4">
    @php
        $checkIn = \Carbon\Carbon::parse($this->data['check_in_date'] ?? now());
        $checkOut = \Carbon\Carbon::parse($this->data['check_out_date'] ?? now()->addDay());
        $nights = $checkIn->diffInDays($checkOut);
        
        $hotel = \App\Plugins\Accommodation\Models\Hotel::find($this->data['hotel_id'] ?? null);
        
        // Handle room type and rate plan (both real and dummy)
        $isDummyRoom = false;
        $roomTypeName = 'Unknown Room';
        $ratePlanName = 'Unknown Rate';
        $pricePerNight = 0;
        
        // Get hotel name (fallback to dummy if not found)
        $hotelName = $hotel->name ?? 'Test Hotel';
        
        // Determine room type
        if (!empty($this->data['room_type_id'])) {
            if ($this->data['room_type_id'] >= 997) {
                $isDummyRoom = true;
                // Get dummy room name from available rooms
                if (isset($this->availableRooms[$this->data['room_type_id']])) {
                    $roomTypeName = $this->availableRooms[$this->data['room_type_id']]['name'] ?? 'Dummy Room';
                    $pricePerNight = $this->availableRooms[$this->data['room_type_id']]['price_per_night'] ?? 1000;
                }
            } else {
                // Get real room type
                $roomType = \App\Plugins\Accommodation\Models\RoomType::find($this->data['room_type_id']);
                if ($roomType) {
                    $roomTypeName = $roomType->name;
                }
            }
        }
        
        // Determine rate plan
        if (!empty($this->data['rate_plan_id'])) {
            if ($this->data['rate_plan_id'] >= 997) {
                $isDummyRoom = true;
                // Dummy rate plans
                $dummyRatePlans = [
                    '999' => 'Standard Rate',
                    '998' => 'Flexible Rate',
                    '997' => 'Premium Rate'
                ];
                $ratePlanName = $dummyRatePlans[$this->data['rate_plan_id']] ?? 'Dummy Rate';
                
                // Set multiplier
                $multiplier = 1.0;
                if ($this->data['rate_plan_id'] == '998') $multiplier = 1.2;
                if ($this->data['rate_plan_id'] == '997') $multiplier = 1.5;
                
                // Apply multiplier to price
                if ($pricePerNight > 0) {
                    $pricePerNight = $pricePerNight * $multiplier;
                }
            } else {
                // Real rate plan
                $ratePlan = \App\Plugins\Pricing\Models\RatePlan::find($this->data['rate_plan_id']);
                if ($ratePlan) {
                    $ratePlanName = $ratePlan->name;
                    $pricePerNight = $ratePlan->base_price;
                }
            }
        }
        
        // Total for nights
        $totalForNights = $pricePerNight * $nights;
        
        // Force update total amount
        if ($totalForNights > 0 && $this->totalAmount == 0) {
            $this->totalAmount = $totalForNights;
        }
    @endphp
    
    <!-- Booking Details -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h4 class="text-lg font-semibold mb-3">Booking Details</h4>
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-sm text-gray-500">Hotel</p>
                <p class="font-medium">{{ $hotelName }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Room Type</p>
                <p class="font-medium">{{ $roomTypeName }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Check-in</p>
                <p class="font-medium">{{ $checkIn->format('M d, Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Check-out</p>
                <p class="font-medium">{{ $checkOut->format('M d, Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Guests</p>
                <p class="font-medium">{{ $this->data['adults'] ?? 1 }} Adults, {{ $this->data['children'] ?? 0 }} Children</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Length of Stay</p>
                <p class="font-medium">{{ $nights }} {{ Str::plural('night', $nights) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Guest Information -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h4 class="text-lg font-semibold mb-3">Guest Information</h4>
        
        @if(!empty($this->data['guest_details']))
            <div class="space-y-3">
                @foreach($this->data['guest_details'] as $index => $guest)
                    <div class="bg-gray-50 p-3 rounded-lg {{ $guest['is_primary'] ? 'border-l-4 border-primary-500' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">{{ $guest['first_name'] }} {{ $guest['last_name'] }}</p>
                                @if($guest['is_primary'])
                                    <p class="text-xs text-primary-600 font-medium">Primary Guest</p>
                                @endif
                            </div>
                            
                            @if($guest['is_primary'])
                                <div class="text-sm">
                                    <p>{{ $guest['email'] }}</p>
                                    <p>{{ $guest['phone'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No guest information provided.</p>
        @endif
    </div>
    
    <!-- Price Summary -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h4 class="text-lg font-semibold mb-3">Price Summary</h4>
        
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>Rate Plan:</span>
                <span class="font-medium">{{ $ratePlanName }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>Rate per Night:</span>
                <span class="font-medium">{{ number_format($pricePerNight, 2) }} USD</span>
            </div>
            
            <div class="flex justify-between">
                <span>{{ $nights }} {{ Str::plural('night', $nights) }}:</span>
                <span class="font-medium">{{ number_format($pricePerNight * $nights, 2) }} USD</span>
            </div>
            
            @if($this->totalAmount > ($pricePerNight * $nights))
                <div class="flex justify-between">
                    <span>Additional charges:</span>
                    <span class="font-medium">{{ number_format($this->totalAmount - ($pricePerNight * $nights), 2) }} USD</span>
                </div>
            @endif
            
            <hr class="my-2">
            
            <div class="flex justify-between font-bold">
                <span>Total:</span>
                <span class="text-primary-600">{{ number_format($this->totalAmount, 2) }} USD</span>
            </div>
        </div>
    </div>
    
    <!-- Special Requests -->
    @if(!empty($this->data['special_requests']))
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h4 class="text-lg font-semibold mb-3">Special Requests</h4>
            <p class="text-gray-700">{{ $this->data['special_requests'] }}</p>
        </div>
    @endif
    
    <!-- Debug Info -->
    @if(auth()->user() && auth()->user()->hasRole('super_admin'))
        <div class="bg-gray-100 rounded-lg border border-gray-200 p-4 mt-4">
            <h4 class="text-lg font-semibold mb-3">Debug Info (Admin Only)</h4>
            <pre class="text-xs overflow-auto p-2 bg-gray-50 max-h-40 rounded border">{{ json_encode($this->data, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif
</div>