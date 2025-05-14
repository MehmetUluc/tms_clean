<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use Illuminate\Support\Str;

class RegionHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing regions if needed
        // Region::truncate();

        // Create Countries
        $turkiye = $this->createRegion([
            'name' => 'Türkiye',
            'type' => Region::TYPE_COUNTRY,
            'code' => 'TR',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $kktc = $this->createRegion([
            'name' => 'Kuzey Kıbrıs Türk Cumhuriyeti',
            'type' => Region::TYPE_COUNTRY,
            'code' => 'KKTC',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        // Create Regions for Türkiye
        $akdeniz = $this->createRegion([
            'name' => 'Akdeniz Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $ege = $this->createRegion([
            'name' => 'Ege Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $marmara = $this->createRegion([
            'name' => 'Marmara Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        $karadeniz = $this->createRegion([
            'name' => 'Karadeniz Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 4,
        ]);

        $icAnadolu = $this->createRegion([
            'name' => 'İç Anadolu Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $doguAnadolu = $this->createRegion([
            'name' => 'Doğu Anadolu Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        $guneyDoguAnadolu = $this->createRegion([
            'name' => 'Güneydoğu Anadolu Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 7,
        ]);

        // Create Regions for KKTC
        $kibris = $this->createRegion([
            'name' => 'Kıbrıs Bölgesi',
            'type' => Region::TYPE_REGION,
            'parent_id' => $kktc->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        // Create Cities for Akdeniz Region
        $antalya = $this->createRegion([
            'name' => 'Antalya',
            'type' => Region::TYPE_CITY,
            'parent_id' => $akdeniz->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $mersin = $this->createRegion([
            'name' => 'Mersin',
            'type' => Region::TYPE_CITY,
            'parent_id' => $akdeniz->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $adana = $this->createRegion([
            'name' => 'Adana',
            'type' => Region::TYPE_CITY,
            'parent_id' => $akdeniz->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create Cities for Ege Region
        $izmir = $this->createRegion([
            'name' => 'İzmir',
            'type' => Region::TYPE_CITY,
            'parent_id' => $ege->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $mugla = $this->createRegion([
            'name' => 'Muğla',
            'type' => Region::TYPE_CITY,
            'parent_id' => $ege->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $aydin = $this->createRegion([
            'name' => 'Aydın',
            'type' => Region::TYPE_CITY,
            'parent_id' => $ege->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        // Create Cities for Marmara Region
        $istanbul = $this->createRegion([
            'name' => 'İstanbul',
            'type' => Region::TYPE_CITY,
            'parent_id' => $marmara->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $bursa = $this->createRegion([
            'name' => 'Bursa',
            'type' => Region::TYPE_CITY,
            'parent_id' => $marmara->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        // Create Cities for KKTC
        $lefkosa = $this->createRegion([
            'name' => 'Lefkoşa',
            'type' => Region::TYPE_CITY,
            'parent_id' => $kibris->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $girne = $this->createRegion([
            'name' => 'Girne',
            'type' => Region::TYPE_CITY,
            'parent_id' => $kibris->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $gazimagusa = $this->createRegion([
            'name' => 'Gazimağusa',
            'type' => Region::TYPE_CITY,
            'parent_id' => $kibris->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        // Create Districts for Antalya
        $this->createRegion([
            'name' => 'Konyaaltı',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createRegion([
            'name' => 'Lara',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createRegion([
            'name' => 'Belek',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        $this->createRegion([
            'name' => 'Side',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 4,
        ]);

        $this->createRegion([
            'name' => 'Alanya',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 5,
        ]);

        // Create Districts for İzmir
        $this->createRegion([
            'name' => 'Çeşme',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $izmir->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createRegion([
            'name' => 'Foça',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $izmir->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createRegion([
            'name' => 'Urla',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $izmir->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        // Create Districts for Muğla
        $this->createRegion([
            'name' => 'Bodrum',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $mugla->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createRegion([
            'name' => 'Marmaris',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $mugla->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createRegion([
            'name' => 'Fethiye',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $mugla->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        // Create Districts for İstanbul
        $this->createRegion([
            'name' => 'Beşiktaş',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createRegion([
            'name' => 'Kadıköy',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createRegion([
            'name' => 'Beyoğlu',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        // Create Districts for Girne
        $this->createRegion([
            'name' => 'Girne Merkez',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $girne->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createRegion([
            'name' => 'Alsancak',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $girne->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createRegion([
            'name' => 'Lapta',
            'type' => Region::TYPE_DISTRICT,
            'parent_id' => $girne->id,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);
    }

    protected function createRegion(array $data): Region
    {
        // Add slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Check for existing region with the same slug
        $existingWithSlug = Region::where('slug', $data['slug'])->first();
        if ($existingWithSlug) {
            // Make the slug unique by adding a suffix
            $data['slug'] = $data['slug'] . '-' . strtolower($data['type']);
        }

        return Region::updateOrCreate(
            [
                'name' => $data['name'],
                'type' => $data['type'],
                'parent_id' => $data['parent_id'] ?? null,
            ],
            $data
        );
    }
}