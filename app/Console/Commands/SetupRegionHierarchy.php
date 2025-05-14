<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupRegionHierarchy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:region-hierarchy {--fresh : Fresh migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the hierarchical region structure with countries, regions, cities, and districts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting setup of region hierarchy...');

        // Back up existing regions if needed
        $this->info('Backing up existing regions data...');
        $regions = DB::table('regions')->get();
        
        if ($regions->count() > 0) {
            $this->info('Found ' . $regions->count() . ' existing regions.');
            
            if ($this->option('fresh')) {
                $this->warn('The --fresh option will delete all existing regions. Proceed with caution!');
                
                if (!$this->confirm('Are you sure you want to delete all existing regions and create a fresh hierarchy?')) {
                    $this->info('Operation cancelled by user.');
                    return 1;
                }
            }
        }

        // Run only region-related migrations
        $this->info('Running migrations for region hierarchy...');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_100000_update_regions_for_hierarchy.php',
            '--force' => true
        ]);
        $this->info(Artisan::output());
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_100001_add_seo_fields_to_regions.php',
            '--force' => true
        ]);
        $this->info(Artisan::output());

        // Run seeders manually
        $this->info('Seeding region hierarchy data...');
        
        if ($this->option('fresh')) {
            $this->info('Clearing existing region data...');
            DB::table('regions')->delete();
        }
        
        // Instantiate and run the seeder directly
        $this->info('Running RegionHierarchySeeder...');
        $seeder = new \Database\Seeders\RegionHierarchySeeder();
        $seeder->run();
        
        $this->info(Artisan::output());

        $this->info('Counting the results...');
        $countries = DB::table('regions')->where('type', 'country')->count();
        $regions = DB::table('regions')->where('type', 'region')->count();
        $cities = DB::table('regions')->where('type', 'city')->count();
        $districts = DB::table('regions')->where('type', 'district')->count();
        
        $this->info("Region hierarchy setup completed successfully!");
        $this->info("Created:");
        $this->info("- {$countries} countries");
        $this->info("- {$regions} regions");
        $this->info("- {$cities} cities");
        $this->info("- {$districts} districts");
        
        return 0;
    }
}