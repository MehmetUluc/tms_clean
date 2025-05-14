<div class="available-rooms-container">
    @if(empty($availableRooms) || $availableRooms->isEmpty())
        <div class="p-8 bg-gray-50 rounded-lg text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-700 font-medium text-lg">Bu kriterlere uygun müsait oda bulunmamaktadır.</p>
            <p class="text-gray-500 mt-2">Lütfen tarih aralığı, kişi sayısı veya konaklama yerini değiştirin.</p>
            <div class="mt-6">
                <button 
                    type="button"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-md font-medium transition"
                    onclick="document.querySelector('[data-wizard-previous-step-button]').click()"
                >
                    Kriterleri Değiştir
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableRooms as $room)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                    <!-- Oda Görseli -->
                    <div class="h-48 overflow-hidden relative">
                        @if($room->cover_image)
                            <img src="{{ $room->cover_image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Görsel yok</span>
                            </div>
                        @endif
                        
                        <!-- Oda tipi badge -->
                        <div class="absolute top-2 right-2 bg-primary-600 text-white px-2 py-1 text-sm rounded">
                            {{ $room->roomType->name ?? 'Standart' }}
                        </div>
                    </div>
                    
                    <!-- Oda bilgileri -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg">{{ $room->name }}</h3>
                        
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                {{ $room->capacity_adults }} Yetişkin
                            </span>
                            
                            @if($room->capacity_children > 0)
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                    {{ $room->capacity_children }} Çocuk
                                </span>
                            @endif
                            
                            @if($room->size)
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                    {{ $room->size }} m²
                                </span>
                            @endif
                        </div>
                        
                        <!-- Oda özellikleri -->
                        <div class="mt-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($room->amenities->take(4) as $amenity)
                                    <span class="text-xs bg-gray-50 px-2 py-1 rounded flex items-center gap-1">
                                        @if($amenity->icon)
                                            <x-icon icon="{{ $amenity->icon }}" class="w-3 h-3" />
                                        @endif
                                        {{ $amenity->name }}
                                    </span>
                                @endforeach
                                
                                @if($room->amenities->count() > 4)
                                    <span class="text-xs bg-gray-50 px-2 py-1 rounded">
                                        +{{ $room->amenities->count() - 4 }} daha
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Oda açıklaması -->
                        <div class="mt-3 text-sm text-gray-500">
                            {{ \Illuminate\Support\Str::limit($room->description, 100) }}
                        </div>
                        
                        <!-- Pansiyon tipleri -->
                        <div class="mt-4">
                            <h4 class="text-sm font-medium mb-2">Pansiyon Tipleri</h4>
                            
                            <div class="space-y-2">
                                @foreach($room->boardTypes as $boardType)
                                    <div class="flex items-center justify-between border p-2 rounded-md hover:bg-gray-50">
                                        <div>
                                            <span class="font-medium">{{ $boardType->name }}</span>
                                            <span class="text-xs text-gray-500 block">{{ $boardType->code }}</span>
                                        </div>
                                        
                                        <!-- Fiyat kısmı -->
                                        <div class="text-right">
                                            <div class="font-bold text-primary-600">
                                                {{ number_format($room->base_price + ($boardType->pivot->price_modifier ?? 0), 2) }} {{ $room->currency ?? 'TRY' }}
                                            </div>
                                            <button 
                                                type="button"
                                                class="text-sm bg-primary-500 hover:bg-primary-600 text-white px-3 py-1.5 rounded-md font-medium transition flex items-center gap-1"
                                                wire:click="selectRoom({{ $room->id }}, {{ $boardType->id }})"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Sepete Ekle
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($room->boardTypes->isEmpty())
                                    <div class="text-sm text-gray-500 italic">
                                        Pansiyon tipi bulunmamaktadır
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>