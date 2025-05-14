<div class="final-summary p-6 bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-primary-700">Rezervasyon Özeti</h2>
        <p class="text-gray-500">Lütfen rezervasyon bilgilerinizi kontrol edin</p>
    </div>
    
    <div class="space-y-6">
        <!-- Otel ve Tarih Bilgileri -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b">Konaklama Bilgileri</h3>
                
                @php
                    $hotel = \App\Plugins\Accommodation\Models\Hotel::find($this->data['hotel_id'] ?? null);
                @endphp
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Otel:</span>
                        <span class="font-medium">{{ $hotel->name ?? 'Belirtilmemiş' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Giriş Tarihi:</span>
                        <span class="font-medium">{{ isset($this->data['check_in']) ? date('d.m.Y', strtotime($this->data['check_in'])) : '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Çıkış Tarihi:</span>
                        <span class="font-medium">{{ isset($this->data['check_out']) ? date('d.m.Y', strtotime($this->data['check_out'])) : '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Konaklama Süresi:</span>
                        <span class="font-medium">
                            @if(isset($this->data['check_in']) && isset($this->data['check_out']))
                                {{ \Carbon\Carbon::parse($this->data['check_in'])->diffInDays(\Carbon\Carbon::parse($this->data['check_out'])) }} gece
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Misafir Sayısı:</span>
                        <span class="font-medium">
                            {{ $this->data['adults'] ?? 0 }} Yetişkin
                            @if(($this->data['children'] ?? 0) > 0)
                                • {{ $this->data['children'] }} Çocuk
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b">İletişim Bilgileri</h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">İrtibat Kişisi:</span>
                        <span class="font-medium">{{ $this->data['contact_name'] ?? '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">E-posta:</span>
                        <span class="font-medium">{{ $this->data['contact_email'] ?? '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Telefon:</span>
                        <span class="font-medium">{{ $this->data['contact_phone'] ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Misafir Bilgileri -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b">Misafir Bilgileri</h3>
            
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <span class="text-gray-500 text-sm">Ad:</span>
                        <div class="font-medium">{{ $this->data['primary_guest_first_name'] ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <span class="text-gray-500 text-sm">Soyad:</span>
                        <div class="font-medium">{{ $this->data['primary_guest_last_name'] ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <span class="text-gray-500 text-sm">Kimlik No:</span>
                        <div class="font-medium">{{ $this->data['primary_guest_id_number'] ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <span class="text-gray-500 text-sm">Uyruk:</span>
                        <div class="font-medium">
                            @php
                                $countries = [
                                    'TR' => 'Türkiye',
                                    'US' => 'Amerika Birleşik Devletleri',
                                    'GB' => 'Birleşik Krallık',
                                    'DE' => 'Almanya',
                                    'FR' => 'Fransa',
                                    'RU' => 'Rusya',
                                ];
                            @endphp
                            {{ $countries[$this->data['primary_guest_nationality'] ?? ''] ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Seçilen Odalar -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b">Seçilen Odalar</h3>
            
            @if(empty($selectedRooms))
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-gray-500">Henüz bir oda seçilmedi.</p>
                </div>
            @else
                @php
                    $totalPrice = 0;
                    $nights = isset($this->data['check_in']) && isset($this->data['check_out']) 
                        ? \Carbon\Carbon::parse($this->data['check_in'])->diffInDays(\Carbon\Carbon::parse($this->data['check_out'])) 
                        : 1;
                    $currency = collect($selectedRooms)->first()?->currency ?? 'TRY';
                @endphp
                
                <div class="space-y-3">
                    @foreach($selectedRooms as $selectionId => $room)
                        @php
                            $roomPrice = $room->base_price + ($selectedBoardTypes[$selectionId]->pivot->price_modifier ?? 0);
                            $roomTotalPrice = $roomPrice * $nights;
                            $totalPrice += $roomTotalPrice;
                        @endphp
                        
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">{{ $room->name }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ $selectedBoardTypes[$selectionId]->name ?? 'Belirtilmemiş' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $nights }} gece × {{ number_format($roomPrice, 2) }} {{ $currency }}
                                </div>
                            </div>
                            
                            <div class="font-bold text-primary-600">
                                {{ number_format($roomTotalPrice, 2) }} {{ $currency }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Toplam Tutar -->
        <div class="bg-primary-50 p-4 rounded-lg border border-primary-100">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-primary-800">Toplam Tutar</h3>
                <div class="font-bold text-xl text-primary-800">
                    {{ number_format($totalPrice ?? 0, 2) }} {{ $currency ?? 'TRY' }}
                </div>
            </div>
            <div class="text-sm text-primary-600 mt-1">Tüm vergiler dahil</div>
        </div>
        
        <!-- Ödeme Bilgileri -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b">Ödeme Bilgileri</h3>
            
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="flex justify-between">
                    <span class="text-gray-500">Ödeme Yöntemi:</span>
                    <span class="font-medium">
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
                    </span>
                </div>
                
                @if(isset($this->data['payment_method']) && $this->data['payment_method'] === 'credit_card')
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-500">Kart Sahibi:</span>
                        <span class="font-medium">{{ $this->data['card_holder'] ?? '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-500">Kart Numarası:</span>
                        <span class="font-medium">
                            @if(isset($this->data['card_number']))
                                •••• •••• •••• {{ substr($this->data['card_number'], -4) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Onay ve Iptal Politikaları -->
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <h3 class="font-semibold text-yellow-700 mb-2">Önemli Bilgiler</h3>
        
        <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
            <li>Giriş saati: 14:00, Çıkış saati: 12:00</li>
            <li>Rezervasyon iptal süresi: Giriş tarihinden 72 saat öncesine kadar ücretsiz iptal</li>
            <li>Bu süre sonrasında 1 gecelik konaklama bedeli tahsil edilir</li>
            <li>No-show (gelmeme) durumunda tam tutar tahsil edilir</li>
        </ul>
    </div>
</div>