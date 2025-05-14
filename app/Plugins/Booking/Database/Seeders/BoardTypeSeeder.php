<?php

namespace App\Plugins\Booking\Database\Seeders;

use App\Plugins\Booking\Models\BoardType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BoardTypeSeeder extends Seeder
{
    /**
     * Seed default board types.
     * This seeder will create standard board types required for the system to work properly.
     */
    public function run(): void
    {
        // Check if the table exists
        if (!Schema::hasTable('board_types')) {
            $this->command->error('The board_types table does not exist yet. Please run migrations first.');
            return;
        }

        // Default board types to ensure system works properly
        $defaultBoardTypes = [
            [
                'name' => 'Oda Kahvaltı',
                'code' => 'BB',
                'description' => 'Konaklama ve kahvaltı dahil',
                'icon' => 'breakfast',
                'includes' => json_encode(['Konaklama', 'Kahvaltı']),
                'excludes' => json_encode(['Öğle Yemeği', 'Akşam Yemeği', 'İçecekler']),
                'sort_order' => 1,
                'is_active' => true,
                'tenant_id' => null,
            ],
            [
                'name' => 'Yarım Pansiyon',
                'code' => 'HB',
                'description' => 'Konaklama, kahvaltı ve akşam yemeği dahil',
                'icon' => 'half-board',
                'includes' => json_encode(['Konaklama', 'Kahvaltı', 'Akşam Yemeği']),
                'excludes' => json_encode(['Öğle Yemeği', 'İçecekler']),
                'sort_order' => 2,
                'is_active' => true,
                'tenant_id' => null,
            ],
            [
                'name' => 'Tam Pansiyon',
                'code' => 'FB',
                'description' => 'Konaklama ve üç öğün yemek dahil',
                'icon' => 'full-board',
                'includes' => json_encode(['Konaklama', 'Kahvaltı', 'Öğle Yemeği', 'Akşam Yemeği']),
                'excludes' => json_encode(['İçecekler']),
                'sort_order' => 3,
                'is_active' => true,
                'tenant_id' => null,
            ],
            [
                'name' => 'Herşey Dahil',
                'code' => 'AI',
                'description' => 'Konaklama, yemekler ve içecekler dahil',
                'icon' => 'all-inclusive',
                'includes' => json_encode(['Konaklama', 'Kahvaltı', 'Öğle Yemeği', 'Akşam Yemeği', 'İçecekler', 'Atıştırmalıklar']),
                'excludes' => json_encode(['Premium İçecekler']),
                'sort_order' => 4,
                'is_active' => true,
                'tenant_id' => null,
            ],
            [
                'name' => 'Sadece Oda',
                'code' => 'RO',
                'description' => 'Sadece konaklama, yemek dahil değil',
                'icon' => 'room-only',
                'includes' => json_encode(['Konaklama']),
                'excludes' => json_encode(['Kahvaltı', 'Öğle Yemeği', 'Akşam Yemeği', 'İçecekler']),
                'sort_order' => 0,
                'is_active' => true,
                'tenant_id' => null,
            ],
        ];

        $this->command->info('Seeding default board types...');

        // Insert or update existing records
        foreach ($defaultBoardTypes as $boardType) {
            BoardType::updateOrCreate(
                ['code' => $boardType['code']],
                $boardType
            );
        }

        $this->command->info('Default board types have been seeded successfully.');
    }
}