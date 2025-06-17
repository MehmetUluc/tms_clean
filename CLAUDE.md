# CLAUDE.md

Bu dosya, Claude Code'un (claude.ai/code) bu projede çalışırken dikkat etmesi gereken noktaları içerir.

## ÖNEMLİ: Dil Kuralı
**Bu projede Claude ile TÜM iletişim TÜRKÇE olacaktır. İngilizce yanıt vermeyiniz.**

## ÖNEMLİ: Oturum Sürekliliği ve Compact İşlemi
Claude'un context penceresi dolduğunda, sistem otomatik olarak konuşmayı özetler (compact). Bu süreçte:

1. **Öğrenilen Bilgileri Kaydet**: Her oturum sonunda veya önemli keşiflerden sonra, CLAUDE.md dosyasına:
   - Karşılaşılan yeni sorunları ve çözümlerini
   - Sistem hakkında öğrenilen yeni bilgileri
   - Gelecek oturumlarda dikkat edilmesi gereken noktaları ekleyin

2. **Compact Öncesi Hazırlık**: Context limitine yaklaşıldığında:
   - Kritik bilgileri CLAUDE.md'ye yazın
   - Devam eden işleri ve durumu özetleyin
   - Hangi dosyaların üzerinde çalışıldığını not edin

3. **Süreklilik İçin**: 
   - Her büyük değişiklikten sonra `git commit` yapılmasını önerin
   - Çözülmemiş sorunları TODO olarak işaretleyin
   - Bir sonraki oturumda nereden devam edileceğini belirtin

## Docker Container Kullanım Kuralı
- **Her zaman `turistik-app` docker container'ı kullanılacak**
- Tüm komutlar ve işlemler bu container üzerinden gerçekleştirilecek
- Container dışında hiçbir işlem yapılmayacak

## Geliştirme Hatırlatmaları
- Herhangi bir özellik yazılmadan evvel sistem bütünlüğü kontrol edilecek, özellikle slug ve price calculation için merkezi yapılara bakılacak

### User Model Circular Dependency Sorunu (17 Haziran 2025 - ÇÖZÜLDÜ)
**Problem:** Memory exhaustion - User modelinde role check method'ları döngüye giriyor
**Sebep:** 
- `isAdmin()` -> `hasRole()` -> `isSuperAdmin()` -> `originalHasRole()` döngüsü
- Partner accessor ile method isim çakışması
**Çözüm:**
1. User modelindeki tüm role check method'larında `originalHasRole()` kullanıldı
2. ID bazlı kontroller önce yapıldı (döngüye girmeden)
3. `getPartnerAttribute()` accessor'u `getAssociatedPartner()` method'una dönüştürüldü
4. Bootstrap app.php'de exception handler güvenli hale getirildi
**Sonuç:** Tüm kullanıcı girişleri (B2C, Partner, Agency, Admin) artık çalışıyor