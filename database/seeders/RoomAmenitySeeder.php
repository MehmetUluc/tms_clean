<?php

namespace Database\Seeders;

use App\Plugins\Amenities\Models\RoomAmenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomAmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'Klima',
                'category' => 'comfort',
                'icon' => 'air-conditioner',
                'description' => 'Odanızın sıcaklığını dilediğiniz gibi ayarlayabileceğiniz klima sistemi'
            ],
            [
                'name' => 'Mini Bar',
                'category' => 'food',
                'icon' => 'refrigerator',
                'description' => 'İçecek ve atıştırmalıklar içeren mini buzdolabı'
            ],
            [
                'name' => 'Ücretsiz Wi-Fi',
                'category' => 'connectivity',
                'icon' => 'wifi',
                'description' => 'Yüksek hızlı ücretsiz internet erişimi'
            ],
            [
                'name' => 'LCD TV',
                'category' => 'entertainment',
                'icon' => 'tv',
                'description' => 'Yüksek çözünürlüklü geniş ekran televizyon'
            ],
            [
                'name' => 'Saç Kurutma Makinesi',
                'category' => 'bathroom',
                'icon' => 'hair-dryer',
                'description' => 'Banyo içerisinde saç kurutma makinesi'
            ],
            [
                'name' => 'Kasa',
                'category' => 'safety',
                'icon' => 'safe',
                'description' => 'Değerli eşyalarınız için elektronik kasa'
            ],
            [
                'name' => 'Balkon',
                'category' => 'outdoor',
                'icon' => 'balcony',
                'description' => 'Manzaralı özel balkon'
            ],
            [
                'name' => 'Duş',
                'category' => 'bathroom',
                'icon' => 'shower',
                'description' => 'Duş kabini'
            ],
            [
                'name' => 'Küvet',
                'category' => 'bathroom',
                'icon' => 'bathtub',
                'description' => 'Banyo küveti'
            ],
            [
                'name' => 'Jakuzi',
                'category' => 'luxury',
                'icon' => 'hot-tub',
                'description' => 'Masajlı jakuzi'
            ],
            [
                'name' => 'Çay & Kahve Seti',
                'category' => 'food',
                'icon' => 'coffee',
                'description' => 'Kendi çay ve kahvenizi hazırlayabileceğiniz set'
            ],
            [
                'name' => 'Ütü & Ütü Masası',
                'category' => 'comfort',
                'icon' => 'iron',
                'description' => 'Kıyafetleriniz için ütü ve ütü masası'
            ],
            [
                'name' => 'Terlik',
                'category' => 'comfort',
                'icon' => 'slippers',
                'description' => 'Tek kullanımlık terlikler'
            ],
            [
                'name' => 'Bornoz',
                'category' => 'comfort',
                'icon' => 'bathrobe',
                'description' => 'Yumuşak pamuklu bornozlar'
            ],
            [
                'name' => 'Direk Hat Telefon',
                'category' => 'connectivity',
                'icon' => 'phone',
                'description' => 'Doğrudan aramaları yapabileceğiniz telefon'
            ],
        ];

        foreach ($amenities as $data) {
            $data['slug'] = Str::slug($data['name']);
            $data['is_active'] = true;
            
            RoomAmenity::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}