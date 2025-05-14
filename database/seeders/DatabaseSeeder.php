<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Temel gerekli verileri oluştur (temiz kurulum için gerekli)
        $this->call([
            BoardTypeSeeder::class,
        ]);

        // Gerçekçi test verileri oluştur (opsiyonel)
        // $this->call(RealisticDataSeeder::class);
    }
}
