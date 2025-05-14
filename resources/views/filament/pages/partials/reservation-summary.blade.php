<div class="reservation-summary">
    <div class="space-y-6">
        <!-- Tarih ve Kişi Bilgileri -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-sm text-gray-500">Giriş Tarihi</h3>
                <p class="font-bold">{{ isset($this->data['check_in']) ? date('d.m.Y', strtotime($this->data['check_in'])) : '-' }}</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-sm text-gray-500">Çıkış Tarihi</h3>
                <p class="font-bold">{{ isset($this->data['check_out']) ? date('d.m.Y', strtotime($this->data['check_out'])) : '-' }}</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-sm text-gray-500">Konaklama Süresi</h3>
                <p class="font-bold">
                    @if(isset($this->data['check_in']) && isset($this->data['check_out']))
                        {{ \Carbon\Carbon::parse($this->data['check_in'])->diffInDays(\Carbon\Carbon::parse($this->data['check_out'])) }} gece
                    @else
                        -
                    @endif
                </p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-sm text-gray-500">Misafir Sayısı</h3>
                <p class="font-bold">
                    {{ $this->data['adults'] ?? 0 }} Yetişkin
                    @if(($this->data['children'] ?? 0) > 0)
                        • {{ $this->data['children'] }} Çocuk
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Otel Bilgisi -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="font-medium text-sm text-gray-500">Otel</h3>
            <p class="font-bold">
                @php
                    $hotel = \App\Plugins\Accommodation\Models\Hotel::find($this->data['hotel_id'] ?? null);
                @endphp
                {{ $hotel->name ?? 'Belirtilmemiş' }}
            </p>
            @if($hotel)
                <p class="text-sm text-gray-500 mt-1">
                    {{ $hotel->region?->full_path ?? '-' }} • 
                    @for ($i = 0; $i < ($hotel->stars ?? 0); $i++)
                        ⭐
                    @endfor
                </p>
            @endif
        </div>
        
        <!-- Seçilen Odalar -->
        <div>
            <h3 class="font-medium text-sm text-gray-500 mb-2">Seçilen Odalar</h3>
            
            @if(empty($selectedRooms))
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-gray-500">Henüz bir oda seçilmedi.</p>
                </div>
            @else
                <div class="space-y-2">
                    @php
                        $totalPrice = 0;
                        $nights = isset($this->data['check_in']) && isset($this->data['check_out']) 
                            ? \Carbon\Carbon::parse($this->data['check_in'])->diffInDays(\Carbon\Carbon::parse($this->data['check_out'])) 
                            : 1;
                    @endphp
                    
                    @foreach($selectedRooms as $selectionId => $room)
                        @php
                            $roomPrice = $room->base_price + ($selectedBoardTypes[$selectionId]->pivot->price_modifier ?? 0);
                            $roomTotalPrice = $roomPrice * $nights;
                            $totalPrice += $roomTotalPrice;
                        @endphp
                        
                        <div class="bg-white shadow-sm rounded-lg p-3 border border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium">{{ $room->name }}</h4>
                                    <div class="text-sm text-gray-600">
                                        {{ $selectedBoardTypes[$selectionId]->name ?? 'Belirtilmemiş' }}
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="font-medium">
                                        {{ number_format($roomPrice, 2) }} {{ $room->currency ?? 'TRY' }} × {{ $nights }} gece
                                    </div>
                                    <div class="font-bold text-primary-600">
                                        {{ number_format($roomTotalPrice, 2) }} {{ $room->currency ?? 'TRY' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Toplam Fiyat -->
                    <div class="bg-primary-50 p-4 rounded-lg mt-4 border-t-2 border-primary-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Toplam Tutar</h4>
                                <p class="text-sm text-gray-500">Tüm vergiler dahil</p>
                            </div>
                            
                            <div class="text-right">
                                <div class="font-bold text-lg text-primary-700">
                                    {{ number_format($totalPrice, 2) }} {{ collect($selectedRooms)->first()?->currency ?? 'TRY' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Ödeme Bilgileri -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="font-medium text-sm text-gray-500 mb-2">Ödeme Yöntemi</h3>
            
            <p class="font-medium">
                @switch($this->data['payment_method'] ?? '')
                    @case('credit_card')
                        Kredi Kartı
                        @break
                    @case('bank_transfer')
                        Banka Havalesi
                        @break
                    @case('pay_at_hotel')
                        Otelde Ödeme
                        @break
                    @default
                        Belirtilmemiş
                @endswitch
            </p>
            
            @if(isset($this->data['payment_method']) && $this->data['payment_method'] === 'credit_card')
                <p class="text-sm text-gray-500 mt-1">
                    {{ $this->data['card_number'] ?? '•••• •••• •••• ••••' }}
                </p>
            @endif
        </div>
    </div>
</div>