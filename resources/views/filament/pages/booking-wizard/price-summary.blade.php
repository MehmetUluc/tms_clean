<div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <h3 class="text-lg font-medium mb-2">Price Summary</h3>
    
    @php
        $checkIn = \Carbon\Carbon::parse($this->data['check_in_date'] ?? now());
        $checkOut = \Carbon\Carbon::parse($this->data['check_out_date'] ?? now()->addDay());
        $nights = $checkIn->diffInDays($checkOut);
        
        // Dummy RoomType/RatePlan IDs (>= 997) kullanılıyor mu?
        $isDummyRoom = false;
        $roomTypeName = 'Unknown Room';
        $ratePlanName = 'Unknown Rate';
        $pricePerNight = 0;
        
        // Oda tipini bul
        if (!empty($this->data['room_type_id'])) {
            if ($this->data['room_type_id'] >= 997) {
                $isDummyRoom = true;
                // Get from available rooms array
                if (isset($this->availableRooms[$this->data['room_type_id']])) {
                    $roomTypeName = $this->availableRooms[$this->data['room_type_id']]['name'] ?? 'Dummy Room';
                    $pricePerNight = $this->availableRooms[$this->data['room_type_id']]['price_per_night'] ?? 1000;
                }
            } else {
                // Real room type
                $roomType = \App\Plugins\Accommodation\Models\RoomType::find($this->data['room_type_id']);
                if ($roomType) {
                    $roomTypeName = $roomType->name;
                }
            }
        }
        
        // Rate planı bul
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
                
                // Set price multiplier
                $multiplier = 1.0;
                if ($this->data['rate_plan_id'] == '998') $multiplier = 1.2;
                if ($this->data['rate_plan_id'] == '997') $multiplier = 1.5;
                
                // If we already have price per night, apply multiplier
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
        
        // Calculate total
        $totalAmount = $pricePerNight * $nights;
        
        // Force-update totalAmount
        if ($totalAmount > 0) {
            $this->totalAmount = $totalAmount;
        }
    @endphp
    
    @if(!empty($this->data['room_type_id']) && !empty($this->data['rate_plan_id']))
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>Room Type:</span>
                <span class="font-medium">{{ $roomTypeName }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>Rate Plan:</span>
                <span class="font-medium">{{ $ratePlanName }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>Dates:</span>
                <span class="font-medium">{{ $checkIn->format('M d, Y') }} - {{ $checkOut->format('M d, Y') }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>Nights:</span>
                <span class="font-medium">{{ $nights }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>Guests:</span>
                <span class="font-medium">{{ $this->data['adults'] ?? 1 }} Adults, {{ $this->data['children'] ?? 0 }} Children</span>
            </div>
            
            <div class="flex justify-between">
                <span>Rate per Night:</span>
                <span class="font-medium">{{ number_format($pricePerNight, 2) }} USD</span>
            </div>
            
            <hr class="my-2">
            
            <div class="flex justify-between font-bold">
                <span>Total:</span>
                <span class="text-primary-600">{{ number_format($totalAmount, 2) }} USD</span>
            </div>
            
            <div class="bg-green-50 p-2 rounded mt-2 text-sm text-green-700">
                Seçimleriniz kaydedildi, "Next" butonuyla bir sonraki adıma geçebilirsiniz.
            </div>
        </div>
    @else
        <p class="text-gray-500">Price information not available.</p>
        
        <div class="mt-4">
            <p class="text-sm text-red-600 font-medium">Test için:</p>
            <div class="mt-1 flex space-x-2">
                <button 
                    type="button" 
                    class="px-2 py-1 bg-blue-500 text-white text-xs rounded" 
                    wire:click="$set('data.room_type_id', '999')"
                >
                    Set Room Type to Deluxe Room
                </button>
                
                <button 
                    type="button" 
                    class="px-2 py-1 bg-blue-500 text-white text-xs rounded" 
                    wire:click="$set('data.rate_plan_id', '999')"
                >
                    Set Rate Plan to Standard Rate
                </button>
            </div>
        </div>
    @endif
</div>