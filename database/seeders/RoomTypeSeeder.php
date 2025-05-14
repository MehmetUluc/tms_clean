<?php

namespace Database\Seeders;

use App\Plugins\Accommodation\Models\RoomType;
use App\Models\BoardType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Standart Oda',
                'description' => 'Rahat ve fonksiyonel standart odalar, konforlu bir konaklama için gereken tüm olanaklara sahiptir.',
                'short_description' => '20-25 m² genişliğinde konforlu konaklama',
                'max_adults' => 2,
                'max_children' => 1,
                'max_occupancy' => 3,
                'base_price' => 1000,
                'min_nights' => 1,
                'size' => 25,
                'beds' => [
                    'İki Tek Kişilik Yatak' => 2,
                    'veya',
                    'Çift Kişilik Yatak' => 1
                ],
                'features' => [
                    'Klima',
                    'TV',
                    'Mini Bar',
                    'Saç Kurutma Makinesi',
                    'Telefon',
                    'Banyo',
                    'Duş'
                ],
                'is_active' => true,
                'is_featured' => false,
                'icon' => 'heroicon-o-home',
                'sort_order' => 1
            ],
            [
                'name' => 'Deluxe Oda',
                'description' => 'Daha geniş ve daha lüks mobilyalara sahip deluxe odalar, konfor düşkünleri için ideal seçimdir.',
                'short_description' => '30-35 m² genişliğinde yüksek konforlu konaklama',
                'max_adults' => 2,
                'max_children' => 2,
                'max_occupancy' => 4,
                'base_price' => 1500,
                'min_nights' => 1,
                'size' => 35,
                'beds' => [
                    'İki Tek Kişilik Yatak' => 2,
                    'veya',
                    'Çift Kişilik Yatak' => 1
                ],
                'features' => [
                    'Klima',
                    'LCD TV',
                    'Mini Bar',
                    'Saç Kurutma Makinesi',
                    'Telefon',
                    'Banyo',
                    'Duş',
                    'Ücretsiz Wi-Fi',
                    'Çay & Kahve Seti'
                ],
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'heroicon-o-star',
                'sort_order' => 2
            ],
            [
                'name' => 'Aile Odası',
                'description' => 'Aileler için özel tasarlanmış daha geniş odalardır. Ara kapılı veya bağlantılı odalar şeklinde olabilir.',
                'short_description' => '40-45 m² genişliğinde, aileler için ideal konaklama',
                'max_adults' => 3,
                'max_children' => 2,
                'max_occupancy' => 5,
                'base_price' => 2000,
                'min_nights' => 2,
                'size' => 45,
                'beds' => [
                    'Çift Kişilik Yatak' => 1,
                    'Tek Kişilik Yatak' => 2
                ],
                'features' => [
                    'Klima',
                    'LCD TV',
                    'Mini Bar',
                    'Saç Kurutma Makinesi',
                    'Telefon',
                    'Banyo',
                    'Duş',
                    'Ücretsiz Wi-Fi',
                    'Çay & Kahve Seti',
                    'Bağlantılı Oda Seçeneği'
                ],
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'heroicon-o-user-group',
                'sort_order' => 3
            ],
            [
                'name' => 'Süit',
                'description' => 'Ayrı yatak odası ve oturma alanına sahip lüks süitler, konaklamanızı unutulmaz kılacak.',
                'short_description' => '50-60 m² genişliğinde, ayrı yatak odası ve oturma odası',
                'max_adults' => 2,
                'max_children' => 2,
                'max_occupancy' => 4,
                'base_price' => 3000,
                'min_nights' => 2,
                'size' => 60,
                'beds' => [
                    'King Size Yatak' => 1,
                    'Kanepe' => 1
                ],
                'features' => [
                    'Klima',
                    'LCD TV',
                    'Mini Bar',
                    'Saç Kurutma Makinesi',
                    'Telefon',
                    'Banyo',
                    'Jakuzi',
                    'Duş',
                    'Ücretsiz Wi-Fi',
                    'Çay & Kahve Seti',
                    'Ayrı Oturma Odası',
                    'Özel Balkon'
                ],
                'is_active' => true,
                'is_featured' => true,
                'icon' => 'heroicon-o-sparkles',
                'sort_order' => 4
            ],
            [
                'name' => 'Tek Kişilik Oda',
                'description' => 'Tek kişilik seyahatler için uygun boyutta, ekonomik ama konforlu odalar.',
                'short_description' => '15-18 m² genişliğinde, tek kişilik konaklama',
                'max_adults' => 1,
                'max_children' => 0,
                'max_occupancy' => 1,
                'base_price' => 800,
                'min_nights' => 1,
                'size' => 18,
                'beds' => [
                    'Tek Kişilik Yatak' => 1
                ],
                'features' => [
                    'Klima',
                    'TV',
                    'Mini Bar',
                    'Saç Kurutma Makinesi',
                    'Telefon',
                    'Banyo',
                    'Duş'
                ],
                'is_active' => true,
                'is_featured' => false,
                'icon' => 'heroicon-o-user',
                'sort_order' => 5
            ],
        ];

        foreach ($roomTypes as $typeData) {
            $slug = Str::slug($typeData['name']);
            
            // Veri dönüşümlerini yap
            $typeData['slug'] = $slug;
            $typeData['features'] = json_encode($typeData['features']);
            $typeData['beds'] = json_encode($typeData['beds']);
            
            // Eğer varsa güncelleyin, yoksa oluşturun
            RoomType::updateOrCreate(
                ['slug' => $slug],
                $typeData
            );
        }

        // Her oda tipini tüm pansiyon tipleriyle ilişkilendir
        $boardTypes = BoardType::all();
        
        foreach (RoomType::all() as $roomType) {
            $roomType->boardTypes()->sync($boardTypes->pluck('id')->toArray());
        }
    }
}