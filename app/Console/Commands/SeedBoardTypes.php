<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedBoardTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:seed-board-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the board types table with default values required for pricing management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding board types...');
        
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\BoardTypeSeeder',
                '--force' => true,
            ]);
            
            $output = Artisan::output();
            $this->info($output);
            
            $this->info('Board types have been seeded successfully!');
            $this->info('You can now use the Pricing Management module.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding board types: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}