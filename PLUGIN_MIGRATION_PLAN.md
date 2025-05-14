# Plugin Mimarisine Geçiş Planı

Bu belge, mevcut monolitik yapıdan plugin mimarisine geçiş sürecinde izlenecek adımları ve stratejileri içerir.

## 1. Hazırlık Aşaması

- [x] Core Plugin oluşturma
- [x] Temel plugin mimarisinin oluşturulması
- [x] Modellerin plugin yapısına taşınması
- [ ] Resource'ların plugin yapısına taşınması
- [ ] Widget'ların plugin yapısına taşınması
- [ ] Sayfa ve görünümlerin plugin yapısına taşınması

## 2. Oluşturulan Plugin'ler

- [x] Core Plugin - Temel altyapı ve ortak bileşenler
- [x] Accommodation Plugin - Otel ve oda yönetimi
- [x] Booking Plugin - Rezervasyon ve misafir yönetimi
- [x] Amenities Plugin - Otel ve oda özellikleri/etiketleri
- [x] Integration Plugin - API entegrasyonları
- [x] UserManagement Plugin - Kullanıcı ve rol yönetimi
- [x] Pricing Plugin (zaten mevcuttu) - Fiyatlandırma ve envanter

## 3. Kademeli Geçiş Stratejisi

### Aşama 1: Resource'ların Taşınması (2 Hafta)

Tüm Resource sınıfları plugin yapısına taşınacak, bu aşamada sistem hala mevcut modelleri kullanmaya devam edecek.

1. Her plugin için Resource yapılarını oluşturun
2. Mevcut Resource'ları kopyalayıp yeni yapıya uyarlayın
3. Menü yapılandırmasını güncelleyin
4. Test ve doğrulama yapın

### Aşama 2: Modeller ve İlişkilerin Taşınması (2 Hafta)

1. Her plugin için Model sınıfları oluşturuldu
2. İlişkiler ve namespace'ler güncellendi
3. Veritabanı işlemleri için geçiş mekanizmaları eklendi

### Aşama 3: Widget ve Sayfaların Taşınması (1 Hafta)

1. Dashboard widget'larını uygun plugin'lere taşıyın
2. Özel sayfaları taşıyın (Config, Help, Settings vb.)
3. Görünümleri (view) ilgili plugin'lere taşıyın

### Aşama 4: Mevcut Kodun Kaldırılması (1 Hafta)

1. Mevcut kodun plugin sürümüyle karşılaştırılması
2. İki sürüm arasında çalışan geçiş katmanının sağlanması
3. Eski kodun kaldırılması

## 4. Geçiş Kontrolü ve Doğrulama

Her aşamadan sonra aşağıdaki kontroller yapılmalıdır:

- [ ] Form işlevselliği testi
- [ ] İlişki yönetimi testi
- [ ] Yetkilendirme testi 
- [ ] API endpointleri testi
- [ ] Kullanıcı arayüzü testi

## 5. İleriye Dönük Planlar

- [ ] Plugin'ler arası olay (event) sistemi
- [ ] Plugin'ler için sürüm yönetimi
- [ ] Plugin'lerin dinamik olarak etkinleştirilmesi/devre dışı bırakılması
- [ ] Plugin marketplace altyapısı
- [ ] Plugin konfigürasyon arayüzleri

## 6. Sorun Yönetimi

- **Veri erişim sorunları**: Geçiş sırasında hem eski hem yeni model sınıfları erişilebilir olacak
- **Namespace değişiklikleri**: Class aliasing ile geçici olarak çözülecek
- **Eksik işlevsellik**: Her plugin'in tam özellik setine sahip olduğundan emin olunacak
- **Performans sorunları**: Çift model yükleme ve yetkilendirme sorunları için ek önbellek mekanizmaları kullanılacak