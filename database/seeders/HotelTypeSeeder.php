<?php

namespace Database\Seeders;

use App\Plugins\Accommodation\Models\HotelType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotelTypes = [
            [
                'name' => 'Resort',
                'description' => 'Geniş kapsamlı tatil tesisleri genellikle deniz kenarı, dağ veya doğa içinde konumlanır',
                'icon' => 'umbrella-beach',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Şehir Oteli',
                'description' => 'Şehir merkezlerinde veya iş bölgelerinde konumlanmış, genellikle iş seyahatleri için tercih edilen oteller',
                'icon' => 'building',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Butik Otel',
                'description' => 'Küçük ölçekli, özel tasarımlı ve genelde tematik konsepte sahip, kişisel hizmet sunan tesisler',
                'icon' => 'gem',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Apart Otel',
                'description' => 'Kendi yemeğinizi pişirebileceğiniz mutfak olanaklarına sahip konaklama birimleri sunan tesisler',
                'icon' => 'home',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Termal Otel',
                'description' => 'Termal kaynaklar ve sağlık hizmetleri sunan, genellikle tedavi ve dinlenme amaçlı kullanılan tesisler',
                'icon' => 'hot-tub',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($hotelTypes as $typeData) {
            $slug = Str::slug($typeData['name']);
            
            HotelType::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $typeData['name'],
                    'description' => $typeData['description'],
                    'icon' => $typeData['icon'],
                    'sort_order' => $typeData['sort_order'],
                    'is_active' => $typeData['is_active'],
                ]
            );
        }
    }
}