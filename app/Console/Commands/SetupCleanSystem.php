<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupCleanSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:setup-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up a clean system with the minimum required data for client use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('This will set up a clean system with the minimum required data. Continue?', true)) {
            $this->info('Setting up a clean system...');
            
            try {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\CleanSetupSeeder',
                    '--force' => true,
                ]);
                
                $output = Artisan::output();
                $this->info($output);
                
                $this->info('Clean system setup completed successfully!');
                $this->info('The system now has the minimum required data to function properly.');
                
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error('Error setting up clean system: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }
        
        $this->info('Operation cancelled.');
        return Command::SUCCESS;
    }
}