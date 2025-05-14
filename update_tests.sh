#!/bin/bash

# PHPUnit attribute formatına tüm test dosyalarını güncelleme script'i

# Test klasörü
TEST_DIR="/filament/tests/Feature"

# Her test dosyasını işle
for file in $TEST_DIR/*.php; do
  echo "İşleniyor: $file"
  
  # /** @test */ yerine #[Test] ile değiştir
  sed -i 's/\/\*\* @test \*\//\#\[Test\]/g' "$file"
  
  # Her bir dosyaya PHPUnit\Framework\Attributes\Test sınıfını ekle
  sed -i '/^use Tests\\TestCase;/a use PHPUnit\\Framework\\Attributes\\Test;' "$file"
  
  # RefreshDatabase trait'ini kaldır ya da yorum satırı yap
  sed -i 's/use RefreshDatabase;/\/\/ use RefreshDatabase;/g' "$file"
  sed -i 's/use LazilyRefreshDatabase;/\/\/ use LazilyRefreshDatabase;/g' "$file"
done

echo "Tüm test dosyaları güncellendi."