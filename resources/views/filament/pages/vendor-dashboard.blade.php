<x-filament::page>
    <div class="vendor-dashboard">
        <!-- Elegant Header with Turkish-inspired gradient -->
        <div class="bg-gradient-to-r from-red-50 via-white to-blue-50 dark:from-red-950/30 dark:via-gray-900 dark:to-blue-950/30 
             rounded-xl p-6 mb-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        <i class="fas fa-hotel mr-2 text-red-500 dark:text-red-400"></i>Hoş Geldiniz, {{ $vendor->name }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ now()->format('d F Y') }} • {{ $vendor->hotels->count() }} Otel • {{ $vendor->status }}
                    </p>
                </div>
                
                <div class="flex space-x-3">
                    <x-filament::button
                        color="primary"
                        tag="a"
                        icon="heroicon-o-currency-dollar"
                        href="{{ route('filament.admin.pages.financial-summary', ['vendor_id' => $vendor->id]) }}"
                    >
                        Finansal Özet
                    </x-filament::button>
                    
                    <x-filament::button
                        tag="a"
                        icon="heroicon-o-document-text"
                        href="{{ route('filament.admin.pages.vendor-documents', ['vendor_id' => $vendor->id]) }}"
                    >
                        Belgeler
                    </x-filament::button>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            @foreach ($this->getStats() as $stat)
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat->getLabel() }}</h3>
                                <p class="text-2xl font-semibold mt-1 text-gray-900 dark:text-white">{!! $stat->getValue() !!}</p>
                                @if ($stat->getDescription())
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        @if ($stat->getDescriptionIcon())
                                            <x-filament::icon 
                                                :name="$stat->getDescriptionIcon()" 
                                                class="h-4 w-4 mr-1 text-gray-400 dark:text-gray-500" 
                                            />
                                        @endif
                                        {{ $stat->getDescription() }}
                                    </p>
                                @endif
                            </div>
                            
                            @if ($stat->getColor())
                                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-{{ $stat->getColor() }}-100 dark:bg-{{ $stat->getColor() }}-900/50">
                                    <x-filament::icon 
                                        :name="$stat->getDescriptionIcon() ?? 'heroicon-o-plus'" 
                                        class="h-6 w-6 text-{{ $stat->getColor() }}-500 dark:text-{{ $stat->getColor() }}-400" 
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Turkish-inspired decorative bottom border -->
                    <div class="h-1 bg-gradient-to-r from-transparent via-{{ $stat->getColor() }}-500 to-transparent"></div>
                </div>
            @endforeach
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <span class="bg-blue-100 dark:bg-blue-900/50 p-2 rounded-lg mr-3">
                                <i class="fas fa-exchange-alt text-lg text-blue-500 dark:text-blue-400"></i>
                            </span>
                            Son İşlemler
                        </h2>
                        <a href="#" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                            Tümünü Gör
                        </a>
                    </div>
                </div>
                <div class="p-3">
                    {{ $this->table }}
                </div>
            </div>

            <!-- Payment Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center">
                        <span class="bg-amber-100 dark:bg-amber-900/50 p-2 rounded-lg mr-3">
                            <i class="fas fa-money-bill-wave text-lg text-amber-500 dark:text-amber-400"></i>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Ödeme Talepleri</h2>
                    </div>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-4 py-3 rounded-l-lg">Tarih</th>
                                    <th scope="col" class="px-4 py-3">Tutar</th>
                                    <th scope="col" class="px-4 py-3 rounded-r-lg">Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->getPaymentRequests() as $request)
                                    <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3">{{ $request->requested_date->format('d.m.Y') }}</td>
                                        <td class="px-4 py-3 font-medium">{{ number_format($request->amount, 2) }} {{ config('vendor.default_currency') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                                @elseif($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                @elseif($request->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                                @elseif($request->status === 'paid') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                @endif">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                                    @if($request->status === 'pending') bg-yellow-500
                                                    @elseif($request->status === 'approved') bg-green-500
                                                    @elseif($request->status === 'rejected') bg-red-500
                                                    @elseif($request->status === 'paid') bg-blue-500
                                                    @else bg-gray-500
                                                    @endif"></span>
                                                {{ __('vendor.payment_status.' . $request->status) }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if(count($this->getPaymentRequests()) === 0)
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-inbox text-2xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                                Henüz ödeme talebi bulunmuyor
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <x-filament::button
                            tag="a"
                            icon="heroicon-m-plus"
                            href="#"
                            x-data="{}"
                            x-on:click="$dispatch('open-modal', { id: 'create-payment-request' })"
                        >
                            Ödeme Talebi Oluştur
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Hotels Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="bg-red-100 dark:bg-red-900/50 p-2 rounded-lg mr-3">
                            <i class="fas fa-hotel text-lg text-red-500 dark:text-red-400"></i>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Otellerim</h2>
                    </div>
                    <a href="{{ route('filament.admin.pages.vendor-hotels', ['vendor_id' => $vendor->id]) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                        Tüm Otelleri Yönet
                    </a>
                </div>
            </div>
            
            <!-- Hotels Grid Layout (for larger screens) -->
            <div class="hidden md:block p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($this->getVendorHotels()->take(6) as $hotel)
                        <div class="bg-gray-50 dark:bg-gray-800/70 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all">
                            <div class="h-40 bg-gray-200 dark:bg-gray-700 relative overflow-hidden">
                                @if($hotel->cover_image)
                                    <img src="{{ Storage::url($hotel->cover_image) }}" 
                                         alt="{{ $hotel->name }}" 
                                         class="w-full h-full object-cover"
                                    />
                                @else
                                    <div class="flex items-center justify-center h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700">
                                        <i class="fas fa-hotel text-4xl text-gray-400 dark:text-gray-600"></i>
                                    </div>
                                @endif
                                
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-800 dark:bg-gray-800/90 dark:text-gray-200 backdrop-blur-sm">
                                        {{ str_repeat('★', (int) $hotel->star_rating) }}
                                    </span>
                                </div>
                                
                                <div class="absolute bottom-0 left-0 right-0 px-4 py-2 bg-gradient-to-t from-black/80 to-transparent">
                                    <h3 class="font-bold text-white truncate">{{ $hotel->name }}</h3>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex items-center gap-2 mb-3 text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    <span>{{ $hotel->city }}</span>
                                    
                                    <span class="mx-1">•</span>
                                    
                                    <i class="fas fa-door-open text-gray-400"></i>
                                    <span>{{ $hotel->rooms->count() }} Oda</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        @if($hotel->is_active) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                        @endif">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                            @if($hotel->is_active) bg-green-500
                                            @else bg-red-500
                                            @endif"></span>
                                        {{ $hotel->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                    
                                    <div class="flex space-x-1">
                                        <x-filament::button
                                            size="xs"
                                            color="gray"
                                            tag="a"
                                            href="{{ route('filament.admin.resources.hotels.edit', $hotel) }}"
                                        >
                                            <i class="fas fa-edit text-xs"></i>
                                        </x-filament::button>
                                        
                                        <x-filament::button
                                            size="xs"
                                            tag="a"
                                            href="{{ route('filament.admin.pages.hotel-pricing-management', ['hotel_id' => $hotel->id]) }}"
                                        >
                                            <i class="fas fa-tags text-xs"></i>
                                        </x-filament::button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(count($this->getVendorHotels()) === 0)
                        <div class="col-span-3 py-8">
                            <div class="text-center p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-hotel text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Henüz otel bulunmuyor</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">İlk otelinizi ekleyerek başlayın</p>
                                    <x-filament::button
                                        tag="a"
                                        :href="route('filament.admin.resources.hotels.create', ['vendor_id' => $vendor->id])"
                                    >
                                        Yeni Otel Ekle
                                    </x-filament::button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                @if(count($this->getVendorHotels()) > 0)
                    <div class="mt-4 flex justify-end">
                        <x-filament::button
                            tag="a"
                            :href="route('filament.admin.resources.hotels.create', ['vendor_id' => $vendor->id])"
                            icon="heroicon-m-plus"
                        >
                            Yeni Otel Ekle
                        </x-filament::button>
                    </div>
                @endif
            </div>
            
            <!-- Hotels Table Layout (for mobile screens) -->
            <div class="md:hidden p-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-4 py-3">Otel Adı</th>
                                <th scope="col" class="px-4 py-3">Şehir</th>
                                <th scope="col" class="px-4 py-3">Durum</th>
                                <th scope="col" class="px-4 py-3 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getVendorHotels() as $hotel)
                                <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $hotel->name }}
                                    </td>
                                    <td class="px-4 py-3">{{ $hotel->city }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($hotel->is_active) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                            @endif">
                                            {{ $hotel->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end space-x-1">
                                            <x-filament::button
                                                size="xs"
                                                color="gray"
                                                tag="a"
                                                href="{{ route('filament.admin.resources.hotels.edit', $hotel) }}"
                                            >
                                                <i class="fas fa-edit text-xs"></i>
                                            </x-filament::button>
                                            
                                            <x-filament::button
                                                size="xs"
                                                tag="a"
                                                href="{{ route('filament.admin.pages.hotel-pricing-management', ['hotel_id' => $hotel->id]) }}"
                                            >
                                                <i class="fas fa-tags text-xs"></i>
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if(count($this->getVendorHotels()) === 0)
                        <div class="text-center p-6">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-hotel text-3xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 mb-3">Henüz otel bulunmuyor</p>
                                <x-filament::button
                                    size="sm"
                                    tag="a"
                                    :href="route('filament.admin.resources.hotels.create', ['vendor_id' => $vendor->id])"
                                >
                                    Yeni Otel Ekle
                                </x-filament::button>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 flex justify-end">
                            <x-filament::button
                                size="sm"
                                tag="a"
                                :href="route('filament.admin.resources.hotels.create', ['vendor_id' => $vendor->id])"
                                icon="heroicon-m-plus"
                            >
                                Yeni Otel Ekle
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for payment request -->
    <x-filament::modal id="create-payment-request" width="md">
        <x-slot name="heading">
            Yeni Ödeme Talebi Oluştur
        </x-slot>
        
        <x-slot name="description">
            Lütfen talep etmek istediğiniz ödeme tutarını belirtin.
        </x-slot>
        
        <div class="space-y-4">
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.label for="payment_amount">Ödeme Tutarı</x-filament::input.label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-200 dark:border-gray-600">
                            {{ config('vendor.default_currency') }}
                        </span>
                        <x-filament::input 
                            type="number" 
                            id="payment_amount" 
                            name="payment_amount"
                            step="0.01"
                            min="0"
                            class="rounded-none rounded-r-lg"
                            placeholder="0.00"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Talep edilebilir maksimum tutar: {{ number_format($vendor->balance, 2) }} {{ config('vendor.default_currency') }}</p>
                </x-filament::input.wrapper>
            </div>
            
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.label for="payment_note">Not (Opsiyonel)</x-filament::input.label>
                    <x-filament::input.textarea id="payment_note" name="payment_note" rows="3" placeholder="Ödeme talebi için ek bilgiler..."></x-filament::input.textarea>
                </x-filament::input.wrapper>
            </div>
        </div>
        
        <x-slot name="footerActions">
            <x-filament::button
                color="gray"
                x-on:click="$dispatch('close-modal', { id: 'create-payment-request' })"
            >
                İptal
            </x-filament::button>
            
            <x-filament::button
                x-on:click="$dispatch('close-modal', { id: 'create-payment-request' }); $dispatch('notify', { message: 'Ödeme talebi başarıyla oluşturuldu.', icon: 'heroicon-o-check-circle' })"
            >
                Talep Oluştur
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
    
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</x-filament::page>