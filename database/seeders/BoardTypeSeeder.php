<?php

namespace Database\Seeders;

use App\Plugins\Booking\Models\BoardType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class BoardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the table exists
        if (!Schema::hasTable('board_types')) {
            $this->command->error('The board_types table does not exist yet. Please run migrations first.');
            Log::error('BoardTypeSeeder failed: board_types table does not exist');
            return;
        }

        $boardTypes = [
            [
                'name' => 'Herşey Dahil',
                'code' => 'AI',
                'description' => 'Tüm yiyecek ve içecekler dahil',
                'icon' => 'cake',
                'includes' => [
                    'Kahvaltı' => 'Açık büfe kahvaltı',
                    'Öğle Yemeği' => 'Açık büfe öğle yemeği',
                    'Akşam Yemeği' => 'Açık büfe akşam yemeği',
                    'Aperatifler' => 'Gün içi aperatifler',
                    'İçecekler' => 'Alkolsüz ve yerli alkollü içecekler'
                ],
                'excludes' => [
                    'İthal İçkiler' => 'İthal alkollü içecekler',
                    'Özel Restoranlar' => 'A la carte restoranlar',
                    'Spa' => 'Spa hizmetleri'
                ],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Ultra Herşey Dahil',
                'code' => 'UAI',
                'description' => 'Tüm yiyecek, içecek ve bazı aktiviteler dahil',
                'icon' => 'sparkles',
                'includes' => [
                    'Kahvaltı' => 'Açık büfe kahvaltı',
                    'Öğle Yemeği' => 'Açık büfe öğle yemeği',
                    'Akşam Yemeği' => 'Açık büfe akşam yemeği',
                    'Aperatifler' => '24 saat aperatifler',
                    'İçecekler' => 'Alkolsüz ve tüm alkollü içecekler (ithal dahil)',
                    'A La Carte' => 'A la carte restoranlar (haftada bir)',
                    'Mini Bar' => 'Mini bar (günlük yenileme)',
                    'Aktiviteler' => 'Bazı su sporları ve aktiviteler'
                ],
                'excludes' => [
                    'Motorlu Su Sporları' => 'Motorlu su sporları',
                    'Spa' => 'Spa hizmetleri',
                    'Telefon' => 'Telefon görüşmeleri'
                ],
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Tam Pansiyon',
                'code' => 'FB',
                'description' => 'Üç öğün yemek dahil, içecekler hariç',
                'icon' => 'home',
                'includes' => [
                    'Kahvaltı' => 'Açık büfe kahvaltı',
                    'Öğle Yemeği' => 'Set menü öğle yemeği',
                    'Akşam Yemeği' => 'Set menü akşam yemeği'
                ],
                'excludes' => [
                    'İçecekler' => 'Tüm içecekler',
                    'Ara Öğünler' => 'Ara öğün ve aperatifler'
                ],
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Yarım Pansiyon',
                'code' => 'HB',
                'description' => 'Kahvaltı ve akşam yemeği dahil',
                'icon' => 'academic-cap',
                'includes' => [
                    'Kahvaltı' => 'Açık büfe kahvaltı',
                    'Akşam Yemeği' => 'Set menü akşam yemeği'
                ],
                'excludes' => [
                    'Öğle Yemeği' => 'Öğle yemeği',
                    'İçecekler' => 'Tüm içecekler',
                    'Ara Öğünler' => 'Ara öğün ve aperatifler'
                ],
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Oda & Kahvaltı',
                'code' => 'BB',
                'description' => 'Sadece kahvaltı dahil',
                'icon' => 'sun',
                'includes' => [
                    'Kahvaltı' => 'Açık büfe kahvaltı'
                ],
                'excludes' => [
                    'Öğle Yemeği' => 'Öğle yemeği',
                    'Akşam Yemeği' => 'Akşam yemeği',
                    'İçecekler' => 'Tüm içecekler (kahvaltı hariç)',
                    'Ara Öğünler' => 'Ara öğün ve aperatifler'
                ],
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Sadece Oda',
                'code' => 'RO',
                'description' => 'Yemek ve içecek hariç',
                'icon' => 'key',
                'includes' => [
                    'Konaklama' => 'Sadece konaklama'
                ],
                'excludes' => [
                    'Kahvaltı' => 'Kahvaltı',
                    'Öğle Yemeği' => 'Öğle yemeği',
                    'Akşam Yemeği' => 'Akşam yemeği',
                    'İçecekler' => 'Tüm içecekler',
                    'Ara Öğünler' => 'Ara öğün ve aperatifler'
                ],
                'sort_order' => 6,
                'is_active' => true
            ],
        ];

        $this->command->info('Seeding board types...');
        $count = 0;

        foreach ($boardTypes as $boardType) {
            try {
                // Convert arrays to JSON strings for storage
                $data = $boardType;
                $data['includes'] = json_encode($boardType['includes']);
                $data['excludes'] = json_encode($boardType['excludes']);

                // Check if exists
                $existing = BoardType::where('code', $boardType['code'])->first();

                if ($existing) {
                    $this->command->info("Board type with code {$boardType['code']} already exists. Skipping...");
                } else {
                    BoardType::create($data);
                    $count++;
                    $this->command->info("Created board type: {$boardType['name']} ({$boardType['code']})");
                }
            } catch (\Exception $e) {
                $this->command->error("Error creating board type {$boardType['code']}: {$e->getMessage()}");
                Log::error("BoardTypeSeeder error: {$e->getMessage()}", [
                    'code' => $boardType['code'],
                    'exception' => $e
                ]);
            }
        }

        $this->command->info("Board type seeding completed: {$count} new board types created.");
    }
}
}