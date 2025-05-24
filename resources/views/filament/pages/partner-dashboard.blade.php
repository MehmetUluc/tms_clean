<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Toplam Otel
                    </h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $stats['hotels']['total'] }}
                    </p>
                    <p class="mt-1 flex items-center gap-1 text-sm text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-check-circle class="h-4 w-4" />
                        {{ $stats['hotels']['active'] }} aktif
                    </p>
                </div>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Bu Ay Rezervasyon
                    </h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $stats['reservations']['monthly'] }}
                    </p>
                    <p class="mt-1 flex items-center gap-1 text-sm text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-calendar class="h-4 w-4" />
                        Toplam: {{ $stats['reservations']['total'] }}
                    </p>
                </div>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Bakiye
                    </h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        ₺{{ number_format($stats['financial']['balance'], 2, ',', '.') }}
                    </p>
                    <p class="mt-1 flex items-center gap-1 text-sm text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-currency-dollar class="h-4 w-4" />
                        Bekleyen: ₺{{ number_format($stats['financial']['pending_payments'], 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Komisyon Oranı
                    </h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        %{{ $stats['financial']['commission_rate'] }}
                    </p>
                    <p class="mt-1 flex items-center gap-1 text-sm text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-calculator class="h-4 w-4" />
                        Varsayılan oran
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
    
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">
                Hızlı İşlemler
            </x-slot>
            
            <div class="space-y-2">
                <x-filament::link 
                    href="#"
                    color="primary"
                    icon="heroicon-m-building-office"
                    class="block"
                >
                    Otellerimi Yönet
                </x-filament::link>
                
                <x-filament::link 
                    href="#"
                    color="primary"
                    icon="heroicon-m-calendar-days"
                    class="block"
                >
                    Rezervasyonları Görüntüle
                </x-filament::link>
                
                <x-filament::link 
                    href="#"
                    color="primary"
                    icon="heroicon-m-currency-dollar"
                    class="block"
                >
                    Finansal Özet
                </x-filament::link>
                
                <x-filament::link 
                    href="#"
                    color="success"
                    icon="heroicon-m-plus-circle"
                    class="block"
                >
                    Ödeme Talebi Oluştur
                </x-filament::link>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <x-slot name="heading">
                Partner Bilgileri
            </x-slot>
            
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Şirket Adı</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $partner->company_name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vergi No</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $partner->tax_number }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Yetkili Kişi</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $partner->contact_person }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">İletişim</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $partner->contact_email }}<br>
                        {{ $partner->contact_phone }}
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Durum</dt>
                    <dd>
                        <x-filament::badge :color="$partner->status === 'active' ? 'success' : 'warning'">
                            {{ ucfirst($partner->status) }}
                        </x-filament::badge>
                    </dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>