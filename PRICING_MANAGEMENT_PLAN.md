# Pricing Management V2 Plugin Plan

## Genel Bakış

PricingV2 plugini, mevcut fiyatlandırma yönetiminin geliştirilmiş, daha kapsamlı ve kullanıcı dostu bir versiyonudur. Otel ve oda fiyatlandırması, dönemler, istisnalar ve envanter yönetimini içerir. Filament admin panel entegrasyonu ile kolay kullanım ve yönetim sağlar.

## Hedefler

- Kullanıcı dostu fiyatlandırma yönetimi arayüzü sunmak
- Karmaşık fiyatlandırma senaryolarını desteklemek
- Mevcut sistem mimarisi ve tasarım diliyle uyumlu olmak
- Performanslı ve ölçeklenebilir bir çözüm sunmak
- Kolay bakım ve genişletilebilirlik sağlamak

## Teknik Tasarım

### 1. Veritabanı Şeması

#### Tablolar:

1. **hotel_board_types**
   - `id`: bigint (PK)
   - `hotel_id`: bigint (FK to hotels.id)
   - `board_type_id`: bigint (FK to board_types.id)
   - `tenant_id`: bigint (nullable, for multi-tenancy)
   - `timestamps`: created_at, updated_at
   - `deleted_at`: timestamp (nullable, for soft deletes)

2. **rate_plans_v2**
   - `id`: bigint (PK)
   - `hotel_id`: bigint (FK to hotels.id)
   - `name`: string
   - `description`: text (nullable)
   - `is_refundable`: boolean (default: true)
   - `priority`: integer (default: 10, for ordering)
   - `status`: boolean (default: true)
   - `tenant_id`: bigint (nullable, for multi-tenancy)
   - `timestamps`: created_at, updated_at
   - `deleted_at`: timestamp (nullable, for soft deletes)

3. **room_rate_plans**
   - `id`: bigint (PK)
   - `rate_plan_id`: bigint (FK to rate_plans_v2.id)
   - `room_id`: bigint (FK to rooms.id)
   - `board_type_id`: bigint (FK to board_types.id)
   - `is_per_person`: boolean (default: false)
   - `tenant_id`: bigint (nullable, for multi-tenancy)
   - `timestamps`: created_at, updated_at
   - `deleted_at`: timestamp (nullable, for soft deletes)

4. **rate_periods_v2**
   - `id`: bigint (PK)
   - `rate_plan_id`: bigint (FK to rate_plans_v2.id)
   - `name`: string
   - `start_date`: date
   - `end_date`: date
   - `base_price`: decimal(10,2) (nullable, for per-unit pricing)
   - `prices`: json (for per-person pricing storage)
   - `min_stay`: integer (default: 1, minimum nights)
   - `max_stay`: integer (nullable, maximum nights)
   - `close_to_arrival`: boolean (default: false)
   - `close_to_departure`: boolean (default: false)
   - `quantity`: integer (default: 0, inventory)
   - `priority`: integer (default: 10, for ordering)
   - `status`: boolean (default: true)
   - `tenant_id`: bigint (nullable, for multi-tenancy)
   - `timestamps`: created_at, updated_at
   - `deleted_at`: timestamp (nullable, for soft deletes)

5. **rate_exceptions_v2**
   - `id`: bigint (PK)
   - `rate_period_id`: bigint (FK to rate_periods_v2.id)
   - `name`: string
   - `date`: date
   - `base_price`: decimal(10,2) (nullable, for per-unit pricing)
   - `prices`: json (for per-person pricing storage)
   - `quantity`: integer (default: 0, inventory)
   - `status`: boolean (default: true)
   - `tenant_id`: bigint (nullable, for multi-tenancy)
   - `timestamps`: created_at, updated_at
   - `deleted_at`: timestamp (nullable, for soft deletes)

### 2. Model İlişkileri

```php
// Hotel - HotelBoardType ilişkisi
Hotel::hasMany(HotelBoardType::class);
HotelBoardType::belongsTo(Hotel::class);
HotelBoardType::belongsTo(BoardType::class);

// Hotel - RatePlanV2 ilişkisi
Hotel::hasMany(RatePlanV2::class);
RatePlanV2::belongsTo(Hotel::class);
RatePlanV2::hasMany(RoomRatePlan::class);
RatePlanV2::hasMany(RatePeriodV2::class);

// RoomRatePlan ilişkileri
RoomRatePlan::belongsTo(RatePlanV2::class);
RoomRatePlan::belongsTo(Room::class);
RoomRatePlan::belongsTo(BoardType::class);

// RatePeriodV2 ilişkileri
RatePeriodV2::belongsTo(RatePlanV2::class);
RatePeriodV2::hasMany(RateExceptionV2::class);

// RateExceptionV2 ilişkileri
RateExceptionV2::belongsTo(RatePeriodV2::class);
```

### 3. Servis Katmanı

PricingV2 modülünde servis katmanı, iş mantığını içerecek ve repository'lerle doğrudan iletişim kuracaktır:

1. **PricingV2Service**
   - Rate plan yönetimi
   - Dönem yönetimi
   - İstisna yönetimi
   - Fiyat hesaplamaları
   - Envanter yönetimi

2. **Repository Sınıfları**
   - RatePlanRepository
   - RatePeriodRepository
   - RateExceptionRepository

Örnek repository metotları:

```php
// RatePlanRepository
public function getForHotel(int $hotelId): Collection;
public function getForRoom(int $roomId): Collection;
public function getActive(int $hotelId): Collection;

// RatePeriodRepository
public function getForRatePlan(int $ratePlanId): Collection;
public function getForDateRange(int $ratePlanId, Carbon $startDate, Carbon $endDate): Collection;
public function checkOverlapping(int $ratePlanId, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): bool;

// RateExceptionRepository
public function getForPeriod(int $periodId): Collection;
public function getForDate(int $ratePlanId, Carbon $date): ?RateExceptionV2;
```

### 4. Filament Entegrasyonu

Filament ile admin paneli entegrasyonu için aşağıdaki bileşenler oluşturulacaktır:

1. **Resources**
   - RatePlanResource
   - HotelResourceExtension (otel listesine "Fiyatlandırma" butonu eklemek için)

2. **Pages**
   - HotelPricingManagement (ana fiyatlandırma yönetimi sayfası)

3. **Widgets**
   - PricingCalendarWidget (görsel takvim arayüzü)
   - RatePlanSummaryWidget (özet bilgileri)

4. **Livewire Components**
   - RatePlanManager
   - PeriodEditor
   - ExceptionEditor
   - PricingCalendar

## Kullanıcı Arayüzü Tasarımı

### 1. Hotel Pricing Management Sayfası

- Otel bilgisi ve konfigürasyon seçenekleri
- Rate plan listesi ve yönetimi
- Takvim görünümü 
- Öne çıkan metriklerin gösterimi

### 2. Rate Plan Yönetimi

- Rate plan oluşturma/düzenleme formu
- Oda ve pansiyon tipi seçimi
- Fiyatlandırma tipi seçimi (kişi başı/birim fiyat)
- Dönem ve istisna yönetimi

### 3. Dönem Yönetimi

- Dönem oluşturma/düzenleme formu
- Tarih aralığı seçimi
- Fiyat ayarları
- Minimum/maksimum konaklama
- Envanter yönetimi

### 4. İstisna Yönetimi

- Belirli tarihlere özel fiyat ve envanter ayarları
- Hızlı ekle/düzenle/sil işlemleri

## Geliştirme Planı

### Aşama 1: Temel Altyapı

1. Plugin yapısı oluşturma
2. Veritabanı migrasyonları
3. Model sınıfları ve ilişkiler

### Aşama 2: Servis Katmanı

1. Repository yapısı
2. Servis sınıfları
3. İş mantığı

### Aşama 3: Admin Arayüzü

1. Filament resources
2. Yönetim sayfaları
3. Formlar ve UI

### Aşama 4: Entegrasyon ve Bağlantılar

1. Mevcut Hotel ve Room modellerine entegrasyon
2. Fiyatlandırma hesaplama fonksiyonları
3. Sistemin diğer bileşenleriyle bağlantılar

### Aşama 5: Test ve Dağıtım

1. Birim testler
2. Entegrasyon testleri
3. Dağıtım ve dokümantasyon

## Kullanılan Teknolojiler

- Laravel
- Filament Admin Panel
- Alpine.js
- Laravel Livewire
- MySQL/MariaDB

## Kısıtlamalar ve Notlar

- Mevcut fiyatlandırma verileri bu yeni sisteme aktarılmayacak
- Tüm veri erişimi multi-tenant yapıya uygun olacak
- Pansiyon tipleri mevcut BoardType modelinden alınacak
- Filament'in mevcut stil ve dil kurallarına uyulacak

## Farklılıklar ve İyileştirmeler (Mevcut Pricing Modülüne Göre)

### 1. Veri Yapısı İyileştirmeleri

- **İlişki Mimarisi**: Daha esnek ve normalize edilmiş bir veri yapısı
- **Performans Optimizasyonu**: İndeksleme ve ilişki stratejileri
- **Tarih Aralığı Yönetimi**: Çakışmalar için daha güçlü kontrol mekanizmaları

### 2. Kullanıcı Deneyimi İyileştirmeleri

- **Daha Sezgisel UI**: Filament bileşenlerinin doğru kullanımı
- **Hızlı Veri Girişi**: Toplu güncelleme ve hızlı düzenleme seçenekleri
- **Görsel Takvim**: Tarih bazlı fiyat ve envanteri görsel olarak yönetme

### 3. Teknik İyileştirmeler

- **Repository Paterni**: Daha modüler ve test edilebilir kod
- **Servis Katmanı**: İş mantığı izolasyonu
- **Denetimli Erişim**: Daha iyi yetkilendirme ve doğrulama mekanizmaları

### 4. Planlama ve Pazarlama İyileştirmeleri

- **Minimum Konaklama**: Min/max konaklama süresi desteği
- **İleri Düzey Envanter**: Arrival/Departure kısıtlamaları
- **Refundable/Non-refundable**: İade politikalarını destekleme

## Sonraki Aşamalar

- Rezervasyon sistemi entegrasyonu
- Gelişmiş raporlama özellikleri
- Fiyat optimizasyonu algoritmaları
- Online Seyahat Acentesi (OTA) entegrasyonları