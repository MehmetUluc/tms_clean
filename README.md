# TMS - Otel ve Seyahat Yönetim Sistemi

TMS (Travel Management System), otel ve seyahat sektörü için geliştirilmiş modüler bir yönetim sistemidir. Laravel ve FilamentPHP kullanılarak geliştirilmiş, çoklu kiracı (multi-tenant) mimarisi üzerine kurulmuş, kapsamlı bir çözümdür.

## Özellikler

- **Modüler Yapı**: Tüm özellikler ayrı plugin'ler halinde, kolay entegre edilebilir ve yönetilebilir şekilde tasarlanmıştır.
- **Çoklu Kiracı Mimarisi**: Tek kurulum ile birden fazla acente ve otel zinciri yönetimi sağlar.
- **Modern Arayüz**: FilamentPHP tabanlı kullanıcı dostu, responsive admin paneli.
- **Servis Tabanlı Mimari**: Tüm iş mantığı sözleşmeler (contracts) ve servisler üzerinden yürütülür.
- **Entegrasyon Esnekliği**: API üzerinden diğer sistemlerle kolay entegrasyon sağlar.

## Plugin'ler

Sistem aşağıdaki plugin'lerden oluşmaktadır:

1. **Core Plugin**
   - Temel altyapı ve çoklu kiracı mimarisi
   - Ortak servis katmanı ve sözleşmeler
   - Tüm plugin'lerin bağımlı olduğu merkezi bileşen

2. **Hotel Plugin**
   - Otel yönetimi
   - Oda tipleri ve pansiyon tipleri
   - Otel özellikleri ve konumlar

3. **Room Plugin**
   - Fiziksel oda yönetimi
   - Oda özellikleri ve durumları
   - Bakım takibi

4. **Booking Plugin**
   - Rezervasyon yönetimi
   - Misafir bilgileri
   - Ödeme takibi

5. **Agency Plugin**
   - Acente yönetimi
   - Sözleşme ve komisyon yönetimi
   - Acente kullanıcıları

6. **Transfer Plugin**
   - Transfer yönetimi
   - Araç ve sürücü yönetimi
   - Rota ve fiyatlandırma

## Kurulum

```bash
# Repoyu klonla
git clone [repo-url] tms
cd tms

# Bağımlılıkları yükle
composer install
npm install

# Ortam dosyasını hazırla
cp .env.example .env
php artisan key:generate

# Veritabanını hazırla
php artisan migrate
php artisan db:seed

# Admin kullanıcısını oluştur
php artisan tms:create-admin

# Dosya sistemini hazırla
php artisan storage:link

# Geliştirme sunucusunu başlat
php artisan serve
npm run dev
```

## Kullanım

Sisteme giriş yaptıktan sonra, ilk olarak bir acente seçmeniz gerekiyor. Sistem çoklu kiracı mimarisi üzerine kurulduğu için, tüm işlemler seçili acenteye özel gerçekleştirilir.

### Acente Seçimi

- Üst menüdeki "Acente Seçimi" düğmesine tıklayın
- Listeden bir acente seçin veya "Acente Seçimini Yönet" sayfasına gidin
- Acente seçildikten sonra, tüm işlemler bu acenteye özel gerçekleştirilecektir

### Temel İşlemler

- **Otel Yönetimi**: Otelleri, oda tiplerini ve pansiyon tiplerini yönetin
- **Rezervasyon**: Misafir rezervasyonlarını oluşturun ve takip edin
- **Transfer Yönetimi**: Misafir transferlerini ve araç planlamasını yapın
- **Acente Yönetimi**: Acente sözleşmelerini ve komisyonlarını yönetin

## Mimari

Sistem, Laravel'in servis tabanlı mimarisi üzerine kurulmuştur ve şu prensipleri takip eder:

- **Repository Pattern**: Veri erişim katmanı için
- **Service Layer**: İş mantığı için
- **Contract-based Programming**: Bağımlılıkları azaltmak için
- **Event-driven Architecture**: Ölçeklenebilirlik için

Çoklu kiracı mimarisi, veritabanı seviyesinde uygulanmış olup, tüm modeller otomatik olarak kiracı filtreleme özelliğine sahiptir.

## Lisans

Bu proje [MIT lisansı](LICENSE.md) altında lisanslanmıştır.