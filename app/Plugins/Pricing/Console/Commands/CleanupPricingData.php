<?php

namespace App\Plugins\Pricing\Console\Commands;

use App\Plugins\Pricing\Models\DailyRate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupPricingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pricing:cleanup
                            {--days=365 : Number of days in the past to keep}
                            {--future=730 : Number of days in the future to keep}
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old pricing data that is no longer needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = $this->option('days');
        $futureToKeep = $this->option('future');
        $dryRun = $this->option('dry-run');
        
        $this->info("Starting pricing data cleanup...");
        $this->info("Keeping data from {$daysToKeep} days in the past and {$futureToKeep} days into the future");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }
        
        // Calculate cutoff dates
        $pastCutoff = Carbon::now()->subDays($daysToKeep)->startOfDay();
        $futureCutoff = Carbon::now()->addDays($futureToKeep)->endOfDay();
        
        // Get count of records to delete
        $pastRecords = DailyRate::where('date', '<', $pastCutoff)->count();
        $futureRecords = DailyRate::where('date', '>', $futureCutoff)->count();
        
        $totalRecords = $pastRecords + $futureRecords;
        
        $this->info("Found {$pastRecords} records older than {$pastCutoff->format('Y-m-d')}");
        $this->info("Found {$futureRecords} records newer than {$futureCutoff->format('Y-m-d')}");
        $this->info("Total records to be deleted: {$totalRecords}");
        
        if ($dryRun) {
            $this->warn("Dry run complete. No records were deleted.");
            return;
        }
        
        if ($totalRecords === 0) {
            $this->info("No records to delete. Exiting.");
            return;
        }
        
        if (!$this->confirm("Do you want to continue with deletion?")) {
            $this->info("Operation cancelled.");
            return;
        }
        
        // Use transactions for safety
        DB::beginTransaction();
        
        try {
            $this->info("Deleting old records...");
            $deleted = DailyRate::where('date', '<', $pastCutoff)->delete();
            $this->info("Deleted {$deleted} old records");
            
            $this->info("Deleting far-future records...");
            $deleted = DailyRate::where('date', '>', $futureCutoff)->delete();
            $this->info("Deleted {$deleted} far-future records");
            
            DB::commit();
            
            $this->info("Cleanup completed successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error during pricing data cleanup: " . $e->getMessage(), [
                'exception' => $e
            ]);
            
            $this->error("An error occurred during cleanup: " . $e->getMessage());
        }
    }
}