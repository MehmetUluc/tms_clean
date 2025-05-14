# Migration Sistemi Kullanım Talimatları

Bu doküman, TMS (Travel Management System) projesinin migration sisteminin doğru kullanımı için oluşturulmuştur.

## Genel Bakış

TMS birden fazla plugin kullanır ve her plugin kendi migration dosyalarına sahiptir. Oluşturduğumuz özel **MigrationServiceProvider** sayesinde bu migrationların çalışması ve çakışmaların önlenmesi sağlanmıştır.

## Migration Sistemi Sorunları ve Çözümleri

TMS'in migration sisteminde şu sorunlar yaşanıyordu:

1. **Duplike Migrations**: Ana dizin ve plugin dizinlerinde aynı tabloyu oluşturan migrations
2. **Çakışan Timestamps**: Aynı zamanda çalışması gereken farklı migrations
3. **Tutarsız Şemalar**: Farklı yerlerde aynı tablonun farklı yapılarla tanımlanması
4. **JSON Sütunları**: JSON sütunların doğru tiplerle oluşturulmaması
5. **NULL Değerli Alanlar**: Bazı alanlardaki NULL değerlerin sorun yaratması

Bu sorunları çözmek için `MigrationServiceProvider` ve `FixMigrationsCommand` sınıfları oluşturuldu.

## Migrations Fix Komutu Kullanımı

Migrations'larla ilgili sorunları çözmek için oluşturulan özel bir komut aşağıdaki gibi kullanılabilir:

```bash
php artisan migrations:fix
```

Bu komut şunları yapacaktır:

1. Duplike migration kayıtlarını temizleme
2. Çakışan timestamps'leri düzeltme
3. Eksik migration kayıtlarını ekleme
4. Bozuk veya eksik alanları düzeltme

Eğer tüm verileri silip sıfırdan başlamak istiyorsanız `--fresh` seçeneğini kullanabilirsiniz:

```bash
php artisan migrations:fix --fresh
```

Bu seçenek `php artisan migrate:fresh` komutu gibi tüm tabloları silip yeniden oluşturacaktır.

## Normal Migration İşlemleri

Migration fix komutunu çalıştırdıktan sonra, normal migration işlemlerini çalıştırabilirsiniz:

```bash
# Bekleyen migrationları çalıştırma
php artisan migrate

# Migration durumunu kontrol etme
php artisan migrate:status
```

## Yeni Bir Plugin İçin Migration Oluşturma

Yeni bir plugin için migration oluşturuyorsanız:

1. Migration dosyalarını `app/Plugins/YourPlugin/database/migrations/` klasörüne koyun
2. Timestamp'lerin mevcut dosyalarla çakışmadığından emin olun
3. Her migration dosyasını anlamlı bir isimle adlandırın (örn. `create_your_table_name_table.php`)
4. Plugin'inizi aktifleştirdiğinizde, eklediğiniz migration'lar otomatik olarak çalışacaktır

## Var Olan Bir Tabloya Yeni Alanlar Eklemek

Var olan bir tabloya yeni alanlar eklerken:

1. Her zaman kontrol edin (`if (!Schema::hasColumn('table_name', 'column_name'))`)
2. Yeni bir migration dosyası oluşturun, var olan dosyaları değiştirmeyin
3. Dosya adını `add_fields_to_tablename_table.php` formatında yapın

## Sık Karşılaşılan Hata Çözümleri

### "Table Already Exists" Hatası

Bu hata genellikle duplike migration'lardan kaynaklanır. Çözmek için:

```bash
php artisan migrations:fix
```

### "Column Already Exists" Hatası

Bu hata genellikle aynı sütunu ekleyen birden fazla migration'dan kaynaklanır. Çözmek için var olan migration dosyasına `if (!Schema::hasColumn())` kontrolü ekleyin.

### "Foreign Key Constraint Fails" Hatası

Bu hata genellikle tablolar yanlış sırada oluşturulduğunda ortaya çıkar. Çözmek için:

1. İlgili migration dosyalarının timestamp'lerini kontrol edin  
2. Bağımlı tablonun önce oluşturulduğundan emin olun

## Best Practices

1. Migration dosyalarını asla elle silmeyin
2. Migration kayıtlarını veritabanında değiştirmeyin
3. Mevcut migration dosyalarını düzenlemeyin, yeni bir migration oluşturun
4. Aynı tabloyu iki farklı yerde oluşturmayın
5. Her zaman tarih/zaman uyumunu kontrol edin

## Plugin Migration İşleyişi

TMS'in plugin mimarisinde, her plugin kendi migration'larını içerir ve bunlar otomatik olarak sistemimiz tarafından tespit edilir. `MigrationServiceProvider` şu işlemleri yapar:

1. Tüm plugin klasörlerini tarar
2. `/database/migrations/` klasörlerini bulur
3. Çakışma olup olmadığını kontrol eder
4. Migration dosyalarını Laravel'e kaydeder

Bu sayede her plugin kendi migration'larını tanımlayabilir ve çakışmalar otomatik olarak yönetilir.