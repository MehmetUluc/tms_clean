<div class="selected-rooms-container">
    @if(empty($selectedRooms))
        <div class="p-6 bg-yellow-50 rounded-lg text-center border border-yellow-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-yellow-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-yellow-700 font-medium">Henüz bir oda seçilmedi.</p>
            <p class="text-yellow-600 text-sm mt-1">Devam etmek için lütfen yukarıdan en az bir oda seçiniz.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($selectedRooms as $selectionId => $room)
                <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-200 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <!-- Oda resmi -->
                        <div class="w-16 h-16 rounded-md overflow-hidden">
                            @if($room->cover_image)
                                <img src="{{ $room->cover_image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">Görsel yok</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Oda bilgileri -->
                        <div>
                            <h4 class="font-medium">{{ $room->name }}</h4>
                            <div class="text-sm text-gray-500">
                                {{ $room->roomType->name ?? 'Standart Oda' }} • 
                                {{ $room->capacity_adults }} Yetişkin 
                                @if($room->capacity_children > 0)
                                    • {{ $room->capacity_children }} Çocuk
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 font-medium">
                                Pansiyon: {{ $selectedBoardTypes[$selectionId]->name ?? 'Belirtilmemiş' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Fiyat bilgisi -->
                        <div class="text-right">
                            <div class="font-bold text-primary-600">
                                {{ number_format($room->base_price + ($selectedBoardTypes[$selectionId]->pivot->price_modifier ?? 0), 2) }} {{ $room->currency ?? 'TRY' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Gecelik
                            </div>
                        </div>
                        
                        <!-- Kaldır butonu -->
                        <button 
                            type="button"
                            class="text-red-600 hover:text-red-800 transition"
                            wire:click="removeRoom('{{ $selectionId }}')"
                        >
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            @endforeach
            
            <!-- Toplam fiyat özeti -->
            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                <div class="flex justify-between font-bold">
                    <span>Toplam</span>
                    <span>
                        {{ 
                            number_format(
                                collect($selectedRooms)->map(function($room, $selectionId) use ($selectedBoardTypes) {
                                    return $room->base_price + ($selectedBoardTypes[$selectionId]->pivot->price_modifier ?? 0); 
                                })->sum(), 
                                2
                            ) 
                        }}
                        {{ collect($selectedRooms)->first()?->currency ?? 'TRY' }}
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>