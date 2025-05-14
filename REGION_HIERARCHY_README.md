# Hiyerarşik Bölge Yapısı Rehberi

Bu belge, otel yönetim sistemimizde kullanılan hiyerarşik bölge yapısını açıklamaktadır.

## Genel Bakış

Konaklama sistemimizde, bölgeler artık hiyerarşik bir yapı ile yönetilmektedir. Bu yapı, müşterilerin hem ülke, hem bölge, hem şehir, hem de ilçe bazında otel araması yapabilmelerine olanak tanır.

Bölge tipleri:
1. **Ülke (country)** - Örn: Türkiye, KKTC
2. **Bölge (region)** - Örn: Akdeniz Bölgesi, Ege Bölgesi
3. **Şehir (city)** - Örn: İzmir, Antalya, Girne
4. **İlçe (district)** - Örn: Çeşme, Konyaaltı, Alsancak

## Temel Kullanım

### Bölgelerin Alınması

```php
// Tüm ülkeleri getir
$countries = \App\Models\Region::countries()->get();

// Tüm ana bölgeleri getir
$regions = \App\Models\Region::regions()->get();

// Tüm şehirleri getir
$cities = \App\Models\Region::cities()->get();

// Tüm ilçeleri getir
$districts = \App\Models\Region::districts()->get();

// Türkiye'ye ait bölgeleri getir
$turkeyRegions = \App\Models\Region::where('type', \App\Models\Region::TYPE_COUNTRY)
                  ->where('name', 'Türkiye')
                  ->first()
                  ->children;

// Ege bölgesindeki şehirleri getir
$egeRegion = \App\Models\Region::where('type', \App\Models\Region::TYPE_REGION)
              ->where('name', 'Ege Bölgesi')
              ->first();
$egeCities = $egeRegion->children; // Sadece direkt alt bölgeler

// İzmir'deki ilçeleri getir
$izmir = \App\Models\Region::where('type', \App\Models\Region::TYPE_CITY)
          ->where('name', 'İzmir')
          ->first();
$izmirDistricts = $izmir->children;
```

### Bölgeye Bağlı Otelleri Alma

```php
// Türkiye'deki tüm oteller (alt bölgelerle birlikte)
$turkiye = \App\Models\Region::where('type', \App\Models\Region::TYPE_COUNTRY)
            ->where('name', 'Türkiye')
            ->first();
$allHotelsInTurkiye = $turkiye->all_hotels; // Bu özellik, tüm alt bölgelerdeki otelleri içerir

// Ege bölgesindeki tüm oteller (alt bölgelerle birlikte)
$ege = \App\Models\Region::where('type', \App\Models\Region::TYPE_REGION)
       ->where('name', 'Ege Bölgesi')
       ->first();
$allHotelsInEge = $ege->all_hotels;

// İzmir'deki tüm oteller (ilçeler dahil)
$izmir = \App\Models\Region::where('type', \App\Models\Region::TYPE_CITY)
          ->where('name', 'İzmir')
          ->first();
$allHotelsInIzmir = $izmir->all_hotels;

// Çeşme'deki sadece oteller
$cesme = \App\Models\Region::where('type', \App\Models\Region::TYPE_DISTRICT)
          ->where('name', 'Çeşme')
          ->first();
$hotelsInCesme = $cesme->hotels; // Direkt bağlı oteller
```

### Otel için Bölge Bilgilerini Alma

```php
$hotel = \App\Models\Hotel::find(1);

// Otelin bağlı olduğu tam bölge
$hotelRegion = $hotel->region;

// Otelin ülkesi
$hotelCountry = $hotel->country;

// Otelin ana bölgesi
$hotelMainRegion = $hotel->main_region;

// Otelin şehri
$hotelCity = $hotel->city_region;

// Otelin ilçesi (varsa)
$hotelDistrict = $hotel->district;

// Otelin tam konum bilgisi (örn: "Çeşme, İzmir, Ege Bölgesi, Türkiye")
$fullLocation = $hotel->region->getFullLocation();
```

## Bölgelerde Arama

```php
// İsim ve üst bölge adına göre arama
$searchTerm = 'Antalya';
$regions = \App\Models\Region::where('name', 'like', "%{$searchTerm}%")
            ->orWhereHas('parent', function($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            })
            ->get();
```

## Hiyerarşi Kullanımı

```php
// Bir bölgenin tüm üst bölgelerini (ebeveynlerini) alma
$region = \App\Models\Region::find(1);
$parents = [];
$parent = $region->parent;

while ($parent) {
    $parents[] = $parent;
    $parent = $parent->parent;
}

// Ekmek kırıntıları (breadcrumb) için bölge hiyerarşisi
$breadcrumbHierarchy = $region->getBreadcrumbHierarchy();
// Çıktı: [Ülke, Bölge, Şehir, İlçe] şeklinde bir dizi döner
```

## Admin Paneli Kullanımı

1. **Bölge Yönetimi**: Yönetim panelinde "Bölgeler" menüsünü kullanarak, hiyerarşik yapıyı görüntüleyebilir ve düzenleyebilirsiniz.

2. **Alt Bölge Ekleme**: Her bölge sayfasında "Alt Bölge Ekle" seçeneği ile hızlıca alt bölgeler oluşturabilirsiniz.

3. **Otel Ataması**: Otel oluşturma/düzenleme ekranında, hiyerarşik yapıda istediğiniz seviyedeki bölgeyi seçebilirsiniz.

4. **Filtreleme**: Otelleri ülke, bölge, şehir veya ilçe bazında filtreleyebilirsiniz. Filtreler, otomatik olarak alt bölgeleri de içerir.

## Konum Bilgileri

Her bölge için aşağıdaki konum bilgilerini ekleyebilirsiniz:

- **Enlem/Boylam**: Harita üzerinde gösterim için
- **Saat Dilimi**: Bölgenin yerel saati için
- **Kod**: Ülke veya bölge kodları (TR, KKTC, vb.)

## SEO Optimizasyonu

Her bölge için SEO bilgilerini yönetebilirsiniz:

- **Meta Başlık**
- **Meta Açıklama**
- **Meta Anahtar Kelimeler**

## Kurulum ve Güncelleme

Hiyerarşik bölge yapısını kurmak veya güncellemek için aşağıdaki komutu çalıştırın:

```bash
php artisan setup:region-hierarchy
```

Tamamen yeni bir yapı oluşturmak için:

```bash
php artisan setup:region-hierarchy --fresh
```

## Teknik Detaylar

- Bölgeler, `regions` tablosunda tutulur ve `parent_id` ile birbirine bağlanır.
- Her bölge `type` alanıyla kategorize edilir (country, region, city, district).
- İlişkiler recursive (özyinelemeli) olarak çalışır, böylece her seviyedeki bölgeleri alabilirsiniz.

---

Bu hiyerarşik yapı, konaklama sisteminin daha esnek ve kullanıcı dostu olmasını sağlar. Özellikle arama motorları için SEO optimizasyonu ve müşterilerin bölge bazlı arama yapabilmesi için önemlidir.