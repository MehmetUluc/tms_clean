# OTA Entegrasyon Modülü Geliştirme Planı

Bu doküman, OTA (Online Travel Agency) entegrasyon modülünün geliştirilmesi ve iyileştirilmesi için detaylı plan ve to-do listesini içerir. Mevcut XML odaklı yapıyı daha genel, çok formatlı ve çift yönlü çalışan bir entegrasyon modülüne dönüştürmek amaçlanmaktadır.

## 1. Temel Altyapı Değişiklikleri

- [ ] **Modül İsimlerinin Güncellenmesi**
  - [ ] XML'e özgü isimlendirmeyi daha genel entegrasyon isimlendirmesine dönüştür
    - XmlMapping → DataMapping
    - XmlMappingWizard → DataMappingWizard
    - XmlParserService → DataParserService
  - [ ] Veritabanı tablolarını güncelle (yeni migrationlar oluştur)
    - xml_mappings → data_mappings
    - Yeni alanlar ekle: format_type (xml, json), operation_type (import, export)

- [ ] **Çift Yönlü Veri İşleme Desteği**
  - [ ] Import işlemi için altyapı (dış sistemden içeri veri alma)
    - Webhook endpoint'leri oluştur
    - Gelen veriyi ayrıştırma ve işleme mekanizması
  - [ ] Export işlemi için altyapı (iç sistemden dışarı veri gönderme)
    - Şablon tabanlı veri dönüştürme sistemi
    - İç veri modellerini dış formatlara dönüştürme mekanizması
  - [ ] Ortak bir dönüşüm mantığı oluştur
    - Her iki yön için de aynı eşleştirme kurallarını kullanabilme

- [ ] **Çoklu Format Desteği**
  - [ ] XML Parser'ın yanına JSON Parser ekleme
    - JSON verileri için analiz ve eşleştirme altyapısı
    - JSON formatında veri döndürebilme yeteneği
  - [ ] Ortak bir arayüz üzerinden çalışacak generik parser yapısı
    - IDataParser interface'i oluştur
    - XmlDataParser ve JsonDataParser sınıfları geliştir
  - [ ] Format otomatik tanıma mekanizması
    - Gelen veri formatını içeriğe bakarak otomatik tanıma
    - İstemciye uygun format dönme yeteneği

## 2. Şablonlama Sistemi Geliştirme

- [ ] **Şablon Motoru Belirleme/Geliştirme**
  - [ ] Mustache veya Handlebars benzeri bir şablon motoru entegrasyonu
    - Mevcut PHP şablon motorlarını değerlendir
    - Laravel'in blade sistemini kullanmanın olabilirliğini araştır
  - [ ] Veya basit bir şablon motoru geliştir
    - Temel değişken yakalama {{variable}}
    - İç içe değişkenler için dot notation {{user.name}}
    - Koşullu ifadeler ve döngüler için sözdizimi

- [ ] **Şablon Depolama Yapısı**
  - [ ] DataMapping modelinde şablon depolama alanı oluştur
    - template_content alanı ekle (TEXT veya LONGTEXT tipinde)
    - template_format alanı ekle (output formatını belirtmek için)
  - [ ] Şablonlar için versiyon kontrolü ekle
    - version alanı ekle
    - Birden fazla şablon versiyonunu yönetebilme

- [ ] **Şablon İçi Yardımcı Fonksiyonlar**
  - [ ] Döngüler için {{#each}} helper
    - Belirli bir koleksiyonu döngüye alarak içeriği tekrarlama
    - İç içe döngüleri destekleme
  - [ ] Koşullu ifadeler için {{#if}} helper
    - Basit koşulları değerlendirme ({{#if variable}})
    - Karşılaştırmalı koşulları destekleme ({{#if variable == 'value'}})
  - [ ] Sayısal döngüler için {{#for}} helper
    - Belirli bir sayıdan başlayıp belirli bir sayıya kadar döngü
    - Adım değerini belirtme opsiyonu
  - [ ] Metin birleştirme için {{concat}} helper
    - Birden fazla değişkeni birleştirme
    - Sabit metinlerle birleştirme
  - [ ] Formatting için {{format}} helper
    - Tarih formatını düzenleme
    - Para birimi formatını düzenleme
    - Sayı formatını düzenleme

## 3. Veri Eşleştirme Mekanizması

- [ ] **Gelişmiş Path Sistemi**
  - [ ] Dot notation desteği geliştir
    - İç içe nesnelere erişim (hotel.rooms[0].title)
    - Dizi elemanlarına erişim (rooms[0], rooms[1] vb.)
  - [ ] XPath benzeri seçiciler ekle
    - Belirli bir kritere göre eleman seçme (hotel.rooms[rate_type='standart'].price)
    - Özel filtreleme fonksiyonları (hotel.rooms.filter(r => r.is_active).name)
  - [ ] Dinamik path oluşturma destekle
    - Değişkenleri path içinde kullanabilme (hotel.rooms[{{index}}].title)
    - Koşullu path oluşturma

- [ ] **Değişken Dönüşüm Kuralları**
  - [ ] Basit eşleştirmeleri geliştir (sourceField → targetField)
    - Direkt kopyalama
    - Sabit değer atama
  - [ ] Format değişimleri ekle
    - Tarih formatı değiştirme (Y-m-d → d/m/Y)
    - Sayı formatı değiştirme (1000 → 1,000.00)
    - Metin formatı değiştirme (lowercase, uppercase, trim vb.)
  - [ ] Değer dönüşümleri destekle
    - Boolean dönüşümleri (YES/NO → true/false)
    - Enum mapping (1,2,3 → Low,Medium,High)
    - Null handling stratejileri
  - [ ] Özel dönüşüm fonksiyonları ekle
    - Matematiksel işlemler (price * 1.18 for tax calculation)
    - Metin işleme (substring, replace vb.)
    - Özel PHP fonksiyonlarını çağırabilme

- [ ] **Test Araçları**
  - [ ] Eşleştirmeleri test etmek için araçlar geliştir
    - Örnek veri giriş alanı
    - Dönüşüm sonucunu gösterme
  - [ ] Örnek verilerle ön izleme özelliği ekle
    - Gerçek veri olmadan test edebilme
    - Hızlı iterasyonlar için

## 4. Arayüz Geliştirmeleri

- [ ] **DataMappingWizard İyileştirmeleri**
  - [ ] İki yönlü eşleştirme desteği ekle
    - Import/export seçim adımı
    - Her iki yön için farklı form alanları
  - [ ] Format seçimi ekle (XML/JSON)
    - Format özel ayarlar
    - Otomatik dönüşüm seçenekleri
  - [ ] Şablon editörü entegre et
    - Kod düzenleme alanı
    - Syntax highlighting
    - Şablon yardımcıları için otomatik tamamlama

- [ ] **Şablon ve Eşleştirme Editörü**
  - [ ] Görsel eşleştirme arayüzü geliştir
    - Sürükle-bırak arayüzü
    - Kaynak ve hedef alanlar arasında oklar
  - [ ] Gelişmiş şablon editörü ekle
    - Syntax highlighting
    - Hata işaretleme
    - Otomatik tamamlama
  - [ ] Eşleştirme bağlantılarını görselleştir
    - Kaynak ve hedef alanlar arasındaki ilişkiyi gösterme
    - Karmaşık eşleştirmeleri görselleştirme

- [ ] **Test ve Debug Araçları**
  - [ ] Gerçek verilerle test etme özelliği ekle
    - Test veri yükleme
    - Sonuçları görüntüleme
  - [ ] Hata ayıklama görünümü geliştir
    - Adım adım dönüşüm izleme
    - Hata mesajlarını gösterme
  - [ ] Adım adım işlem izleme özelliği ekle
    - Her bir dönüşüm adımını gösterme
    - Ara değerleri izleme

## 5. API Katmanı

- [ ] **Webhook API Endpoints**
  - [ ] Dış sistemler için webhook endpoints oluştur
    - /api/ota/{channel}/webhook gibi yapılar
    - Farklı veri formatlarını kabul edebilme
  - [ ] API güvenliği sağla
    - Authentication (API key, JWT, OAuth vb.)
    - Rate limiting
    - Input validation

- [ ] **Channel API Endpoints**
  - [ ] Dış sistemlerden veri çekmek için API endpoints geliştir
    - /api/ota/{channel}/fetch gibi yapılar
    - Farklı veri formatlarında yanıt dönebilme
  - [ ] Otomatik dönüşüm entegrasyonu yap
    - Çekilen veriyi otomatik olarak iç formata dönüştürme
    - İç verilerden dış format oluşturma

- [ ] **API Response Formatları**
  - [ ] Başarı/hata durumları için tutarlı yanıt formatları tanımla
    - Standart yapı (status, message, data, errors vb.)
    - HTTP durum kodlarını doğru kullanma
  - [ ] Hata izleme ve loglama sistemi kur
    - Detaylı hata mesajları
    - Hata kategorileri
    - Log rotasyonu ve saklama stratejisi

## 6. Dokümentasyon

- [ ] **Developer Docs**
  - [ ] Şablon sözdizimi rehberi oluştur
    - Tüm helper'ların detaylı açıklamaları
    - Örneklerle kullanım
  - [ ] Eşleştirme kuralları rehberi hazırla
    - Temel kurallar
    - Karmaşık dönüşüm senaryoları
  - [ ] API dokümentasyonu yaz
    - Tüm endpoint'lerin açıklamaları
    - Request/response örnekleri

- [ ] **Kullanıcı Kılavuzu**
  - [ ] Yeni kanal ekleme adımları dokümanı yaz
    - Adım adım kılavuz
    - Ekran görüntüleri
  - [ ] Eşleştirme oluşturma rehberi hazırla
    - Basit senaryolar
    - Karmaşık senaryolar
  - [ ] Sorun giderme kılavuzu oluştur
    - Yaygın hatalar ve çözümleri
    - Debug teknikleri

## 7. Test ve Optimizasyon

- [ ] **Birim Testleri**
  - [ ] Parser fonksiyonları için testler yaz
    - XML parsing test cases
    - JSON parsing test cases
  - [ ] Dönüşüm kuralları için testler yaz
    - Basit dönüşümler
    - Karmaşık senaryolar

- [ ] **Entegrasyon Testleri**
  - [ ] Gerçek OTA entegrasyonları ile testler yap
    - HotelRunner test cases
    - ElektraWeb test cases
    - Diğer OTA'lar için test cases
  - [ ] Uçtan uca test senaryoları oluştur
    - Veri alımından işlemeye ve yanıt dönmeye kadar tüm süreç
    - Hata senaryoları

- [ ] **Performans Optimizasyonları**
  - [ ] Büyük veri setleri için optimizasyon yap
    - Memory kullanımını azalt
    - Chunk'lar halinde işleme
  - [ ] Önbellek stratejileri geliştir
    - Sık kullanılan şablonları cache'leme
    - Aynı verilerin tekrar tekrar parse edilmesini önleme

## Aşamalı Yaklaşım Öncelik Sırası

### Aşama 1 - Temel Yapı ve İsim Değişiklikleri
- Mevcut XML odaklı yapıyı daha genel bir yapıya dönüştür
- Çift yönlü işlem desteği için altyapı oluştur
- Model ve tablo yapısını güncelle

### Aşama 2 - Şablonlama Sistemi
- Basit bir şablon motoru entegrasyonu
- Temel şablon desteği ekle
- Şablon oluşturma ve saklama altyapısı

### Aşama 3 - JSON Desteği
- XML'in yanına JSON parser ekle
- Format bağımsız eşleştirme mantığı
- Otomatik format belirleme

### Aşama 4 - Gelişmiş Eşleştirme Özellikleri
- Koşullu dönüşümler
- Döngüler
- Dinamik path'ler
- Özel dönüşüm fonksiyonları

### Aşama 5 - Arayüz İyileştirmeleri
- Gelişmiş şablon editörü
- Görsel eşleştirme arayüzü
- Test ve debug araçları