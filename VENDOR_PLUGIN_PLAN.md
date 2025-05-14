# TMS Vendor Plugin Implementasyon Planı

Bu doküman, TMS (Travel Management System) için tasarlanacak Vendor (Partner) modülünün implementasyon planını ve yapılacaklar listesini içermektedir.

## Genel Bakış

Vendor modülü, otel sahiplerinin/işletmecilerinin (partner) kendi tesislerini yönetmelerine olanak sağlayacak ve platformun komisyon bazlı çalışma modelini destekleyecek bir alt sistemdir. Bu modül, TMS'in bel kemiğini oluşturacak ve multi-tenant mimarisiyle bütünleşik çalışacaktır.

## Temel Bileşenler

1. **Vendor Yönetimi**: Vendor kayıt, onay ve profil yönetim süreçleri
2. **Otel ve Oda Yönetimi**: Vendor'ların kendi tesislerini yönetmesi
3. **Rezervasyon Yönetimi**: Vendor'a ait rezervasyonların takibi ve yönetimi
4. **Finansal Yönetim**: Komisyon, gelir paylaşımı, ödeme ve fatura süreçleri
5. **Bakanlık Entegrasyonu**: Kültür ve Turizm Bakanlığı bilgi toplama süreçleri
6. **Raporlama ve Analitik**: Vendor bazlı performans ve finansal raporlar

## Veritabanı Şeması

```
vendors
  - id
  - name
  - company_name
  - tax_number
  - tax_office
  - email
  - phone
  - address
  - city
  - country
  - status (active, inactive, pending, suspended)
  - commission_rate
  - contract_start_date
  - contract_end_date
  - created_at
  - updated_at
  - deleted_at

vendor_users
  - id
  - vendor_id
  - user_id
  - role (admin, manager, staff)
  - created_at
  - updated_at
  - deleted_at

vendor_bank_accounts
  - id
  - vendor_id
  - bank_name
  - account_name
  - iban
  - account_number
  - branch_code
  - currency
  - is_default
  - created_at
  - updated_at
  - deleted_at

vendor_documents
  - id
  - vendor_id
  - document_type (contract, tax_certificate, license, etc.)
  - file_path
  - status (pending, approved, rejected)
  - comments
  - expiry_date
  - created_at
  - updated_at
  - deleted_at

vendor_commissions
  - id
  - vendor_id
  - hotel_id (nullable)
  - room_type_id (nullable)
  - commission_rate
  - start_date
  - end_date
  - created_at
  - updated_at
  - deleted_at

vendor_payments
  - id
  - vendor_id
  - amount
  - currency
  - payment_date
  - due_date
  - status (pending, completed, cancelled)
  - payment_method
  - payment_reference
  - notes
  - created_at
  - updated_at
  - deleted_at

vendor_payment_requests
  - id
  - vendor_id
  - amount
  - currency
  - requested_date
  - status (pending, approved, rejected, paid)
  - notes
  - rejection_reason
  - created_at
  - updated_at
  - deleted_at

vendor_transactions
  - id
  - vendor_id
  - reservation_id
  - amount
  - commission_amount
  - net_amount
  - currency
  - transaction_date
  - transaction_type (booking, cancellation, modification, payment)
  - status (pending, processed, cancelled)
  - notes
  - created_at
  - updated_at
  - deleted_at

vendor_ministry_reports
  - id
  - vendor_id
  - hotel_id
  - report_date
  - report_type
  - file_path
  - status (pending, submitted, approved, rejected)
  - notes
  - created_at
  - updated_at
  - deleted_at
```

## Yetkilendirme Yapısı

1. **Super Admin**: Tüm sistemi yönetebilir
2. **Admin**: Tüm vendor'ları yönetebilir
3. **Vendor Admin**: Kendi hesabını ve otellerini yönetebilir
4. **Vendor Staff**: Vendor tarafından belirlenen sınırlı yetkilere sahip

## Yapılacaklar Listesi

### 1. Temel Altyapı

- [ ] Vendor modeli ve ilişkili modellerin oluşturulması
- [ ] Veritabanı migration dosyalarının oluşturulması
- [ ] Model factory ve seeder'ların hazırlanması
- [ ] VendorServiceProvider sınıfının oluşturulması
- [ ] VendorPlugin sınıfının oluşturulması
- [ ] Filament panel entegrasyonu

### 2. Vendor Yönetimi

- [ ] Vendor CRUD işlemleri için Filament resource oluşturulması
- [ ] Vendor onay sürecinin implementasyonu
- [ ] Vendor statü yönetim sisteminin kurulması
- [ ] Vendor profil yönetim sayfasının oluşturulması
- [ ] Vendor'a bağlı kullanıcıların yönetimi
- [ ] Vendor döküman yükleme ve onay sistemi
- [ ] Vendor sözleşme yönetimi

### 3. Otel ve Oda Yönetimi

- [ ] Hotel modeli için Vendor ilişkisinin eklenmesi
- [ ] Vendor bazlı otel filtreleme sisteminin kurulması
- [ ] Vendor'ların kendi otellerini yönetebilmesi için arayüz
- [ ] Vendor'ların oda envanterini yönetmesi için arayüz
- [ ] Otel ve oda aktivasyon/deaktivasyon yönetimi
- [ ] Hızlı fiyat güncelleme araçları

### 4. Rezervasyon Yönetimi

- [ ] Vendor'a ait rezervasyonların listelenmesi
- [ ] Rezervasyon detay görüntüleme
- [ ] Rezervasyon durum değişiklikleri ve bildirimler
- [ ] Misafir iletişim yönetimi
- [ ] Rezervasyon takvimi görünümü
- [ ] Vendor bazlı doluluk göstergeleri

### 5. Finansal Yönetim

- [ ] Komisyon oranı tanımlama arayüzü
- [ ] Özel anlaşma ve farklı komisyon oranları tanımlama
- [ ] Finansal işlem kaydı ve takibi
- [ ] Ödeme talebi oluşturma sistemi
- [ ] Ödeme onay ve ret süreçleri
- [ ] Fatura oluşturma ve gönderme sistemi
- [ ] Finansal özet dashboard'u

### 6. Bakanlık Entegrasyonu

- [ ] Bakanlık veri toplama gereksinimlerinin analizi
- [ ] Veri toplama formlarının oluşturulması
- [ ] Misafir bilgilerinin bakanlık formatında dışa aktarımı
- [ ] Otomatik rapor oluşturma sistemi
- [ ] XML/API entegrasyonu (gerekirse)

### 7. Raporlama ve Analitik

- [ ] Vendor bazlı performans raporu
- [ ] Gelir ve komisyon raporları
- [ ] Doluluk ve rezervasyon raporları
- [ ] Bakanlık raporlama
- [ ] İstatistiksel analizler ve grafikler
- [ ] Excel/PDF dışa aktarma özellikleri

### 8. Kullanıcı Arayüzü

- [ ] Vendor dashboard'u
- [ ] Vendor profil sayfası
- [ ] Otel yönetim arayüzü
- [ ] Finansal özet ve işlem detayları
- [ ] Rapor görüntüleme araçları
- [ ] Bildirim sistemi

### 9. API ve Entegrasyonlar

- [ ] Vendor API endpoints oluşturulması
- [ ] Muhasebe yazılımı entegrasyonu
- [ ] Banka entegrasyonu (ödeme için)
- [ ] Bakanlık API entegrasyonu

### 10. Güvenlik ve Doğrulama

- [ ] Vendor yetkilendirme ve izin sistemi
- [ ] Veri giriş doğrulama kuralları
- [ ] İşlem logları ve audit trail
- [ ] Multi-factor authentication (MFA)

### 11. Testler

- [ ] Unit testlerin yazılması
- [ ] Feature testlerin yazılması
- [ ] Integration testlerin yazılması
- [ ] End-to-end testlerin yapılması

### 12. Dokümantasyon

- [ ] Teknik dokümantasyon
- [ ] Kullanıcı kılavuzları
- [ ] API dokümantasyonu
- [ ] Kurulum ve yapılandırma talimatları

## İmplementasyon Fazları

### Faz 1: Temel Altyapı ve Vendor Yönetimi
- Vendor modelleri ve migration'lar
- Temel CRUD işlemleri
- Yetkilendirme sistemi

### Faz 2: Otel ve Rezervasyon Yönetimi
- Vendor'a bağlı otellerin yönetimi
- Oda envanteri
- Rezervasyon görüntüleme ve yönetimi

### Faz 3: Finansal Yönetim
- Komisyon sistemi
- Ödeme talep ve onay süreci
- Finansal raporlama

### Faz 4: Bakanlık Entegrasyonu ve Gelişmiş Özellikler
- Bakanlık raporlaması
- Gelişmiş analitik
- Entegrasyonlar

## Teknik Gereksinimler

- Laravel 10+
- Filament Admin Panel
- MySQL/PostgreSQL
- PHP 8.1+
- Redis (önbellek için)
- Laravel Sanctum (API authentication)
- Laravel Spatie Permission (yetkilendirme)

## Notlar ve Öneriler

1. Vendor modülünün mevcut multi-tenant mimarisiyle tam entegrasyonu kritik önemdedir
2. Finansal işlemler için sağlam bir audit log sistemi geliştirilmelidir
3. Özellikle finansal verilerin güvenliği için ekstra önlemler alınmalıdır
4. Ölçeklenebilirlik için asenkron işlemler (queue) kullanılmalıdır
5. Vendor dashboard'u için performans optimize edilmelidir
6. GDPR ve KVKK uyumluluğu için veri işleme politikaları oluşturulmalıdır

Bu plan, TMS Vendor modülünün geliştirilmesi için temel bir yol haritası sunmaktadır. Geliştirme sürecinde gereksinimlere göre değişiklikler yapılabilir.