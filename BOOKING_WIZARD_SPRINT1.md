# Booking Wizard - Sprint 1 Planı

## Genel Bakış
Bu belge, otel rezervasyon sisteminin Booking Wizard akışının ilk sprint planını içerir. Sprint 1, temel rezervasyon akışının iyileştirilmesini ve kullanıcı deneyiminin geliştirilmesini kapsamaktadır.

## Hedefler
- Bölge seçimi yaparak otelleri filtrelemek
- Tarih ve kişi bilgilerini doğru sırayla almak
- Müsaitlik durumuna göre otelleri listelemek
- Oda tiplerini ve pansiyon seçeneklerini göstermek

## İş Maddeleri

### 1. Veri Yapısı İncelemeleri
- [x] Region modeli ve ilişkilerini incelemek
- [x] Hotel-Region ilişkilerini anlamak
- [x] Müsaitlik kontrolü için gerekli modelleri incelemek
- [x] Oda tipleri ve pansiyon seçenekleri ilişkilerini analiz etmek

### 2. Wizard Akışı Değişiklikleri
- [x] İlk adımda bölge seçimi sağlamak
- [x] Tarih ve kişi bilgilerini uygun adımda almak
- [x] Yetişkin/çocuk sayısı girişini düzenlemek
- [x] Çocuk yaşı girişi eklemek

### 3. Otel Listeleme Ekranı
- [x] Bölgeye ve tarih aralığına göre otelleri filtreleme (müsaitlik kontrolü ayrıca eklenecek)
- [x] Otel kartları tasarımı
  - [x] Otel fotoğrafı gösterimi
  - [x] Otel adı, yıldız sayısı, bölge bilgisi
  - [x] Kısa açıklama
  - [x] Temel fiyat bilgisi
- [x] "Otel Seç" düğmesi ile bir sonraki adıma geçiş

### 4. Oda Listeleme Ekranı
- [x] Seçilen otelin müsait oda tiplerini listeleme
- [x] Oda kartları tasarımı
  - [x] Oda fotoğrafı gösterimi
  - [x] Oda adı ve özellikleri
  - [x] Detaylar gösterimi (sigara içilebilir/içilemez, manzara vb.)
- [x] Her oda için pansiyon tiplerini listeleme (O, OB, YP, TP, HD)
- [x] Fiyat gösterimi ve hesaplaması

### 5. Müsaitlik Kontrolü (Sprint 2'ye Aktarıldı)
- [ ] Tarih ve kişi sayısına göre otel ve oda müsaitlik sorgusu
- [ ] Müsait değilse oteli/odayı listede göstermeme
- [ ] Hiç müsait oda/otel yoksa kullanıcıya bilgi mesajı gösterme

### 6. Adımlar Arası Veri Aktarımı
- [x] Seçilen bölge, tarih ve kişi bilgilerini sonraki adımlara aktarma
- [x] Seçilen oteli ve oda tipini sonraki adıma aktarma
- [x] Seçilen pansiyon tipini ve hesaplanan fiyatı aktarma

## Başlangıç Noktası
Mevcut wizard sadece tek bir otel için rezervasyon yapma imkanı sunuyor. Geliştirmemizle bölge bazlı filtreleme, müsaitlik kontrolü ve gerçek dünya senaryolarına uygun akış sağlayacağız.

## Teknik Gereksinimler
- Region ve Hotel modelleri arasındaki ilişkiler
- Müsaitlik kontrolü için Inventory modeli kullanımı
- Filament Forms API'si ile dinamik formlar oluşturma
- Kart görünümü için uygun bileşenler
- Oda ve pansiyon tiplerinin ilişkisel yapısı

## Sprint 1 Tamamlanan İş Maddeleri Özeti
- [x] Bölge seçimi ve filtreleme (16/16 tamamlanan görev)
- [x] Tarih ve kişi bilgileri girişi
- [x] Otel listeleme ve seçim akışı
- [x] Oda ve pansiyon tipi seçimleri
- [x] Fiyat hesaplama ve görüntüleme
- [x] Rezervasyon oluşturma adımları
- [ ] Müsaitlik kontrolü (Sprint 2'ye aktarıldı)

## Sonraki Adımlar (Sprint 2 için)
- Müsaitlik kontrolü ve envanter yönetimi
- Özel istekler ve notlar ekleme
- İptal politikası gösterimi
- Ödeme seçenekleri ve bilgileri
- Erken giriş/geç çıkış seçenekleri
- Transfer hizmetleri
- Rezervasyon özeti ve onayı
- Promosyon kodu uygulaması

---

**Not:** Bu belge, geliştirme sürecinde güncellenecektir.