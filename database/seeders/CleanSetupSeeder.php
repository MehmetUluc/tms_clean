<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CleanSetupSeeder extends Seeder
{
    /**
     * Seed essential data for a clean setup that's ready for client use.
     * This seeder creates the minimum required data for the system to work properly.
     */
    public function run(): void
    {
        $this->command->info('Setting up a clean installation with essential data...');
        
        try {
            // 1. Seed essential board types
            $this->command->info('Seeding board types...');
            $this->call(BoardTypeSeeder::class);
            
            // 2. Set up permissions (if needed)
            $this->command->info('Setting up permissions...');
            $this->call(RolesAndPermissionsSeeder::class);
            
            // 3. Create a super admin if not exists
            $this->command->info('Setting up super admin...');
            $this->call(SuperAdminSeeder::class);
            
            $this->command->info('Clean setup completed successfully!');
            $this->command->info('Your system now has the minimum required data to function properly.');
            
        } catch (\Exception $e) {
            $this->command->error('Error during clean setup: ' . $e->getMessage());
            Log::error('CleanSetupSeeder error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}