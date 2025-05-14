<?php

return [
    // Çekirdek sistem sağlayıcıları
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\MigrationServiceProvider::class, // Migration management service provider
    
    // Plugin sağlayıcıları - Öncelikli sıralama
    App\Plugins\Core\CoreServiceProvider::class,
    App\Plugins\Hotel\HotelServiceProvider::class,
    App\Plugins\Accommodation\AccommodationServiceProvider::class,
    App\Plugins\Booking\BookingServiceProvider::class,
    App\Plugins\Amenities\AmenitiesServiceProvider::class,
    App\Plugins\Integration\IntegrationServiceProvider::class,
    App\Plugins\API\ApiServiceProvider::class,
    App\Plugins\UserManagement\UserManagementServiceProvider::class,
    App\Plugins\Pricing\PricingServiceProvider::class,
    App\Plugins\ThemeManager\ThemeManagerServiceProvider::class,
    App\Plugins\OTA\OTAServiceProvider::class, // OTA ServiceProvider ekledik
    
    // B2C Tema sağlayıcısı kaldırıldı
    
    // Filament panel sağlayıcısı - Plugin kaynaklarının yüklenmesinden sonra
    App\Providers\Filament\AdminPanelProvider::class,
];