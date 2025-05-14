<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\RatePlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDailyRatesPricingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-rates:update-pricing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates daily_rates table with pricing method and refund policy data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update daily rates pricing data...');
        
        // Get all rate plans
        $ratePlans = RatePlan::with('room')->get();
        $this->info('Found ' . $ratePlans->count() . ' rate plans');
        
        $progressBar = $this->output->createProgressBar($ratePlans->count());
        $progressBar->start();
        
        $totalUpdated = 0;
        $errors = 0;
        
        foreach ($ratePlans as $ratePlan) {
            // Skip rate plans without rooms
            if (!$ratePlan->room) {
                $this->warn("Rate plan #{$ratePlan->id} has no associated room, skipping");
                $progressBar->advance();
                continue;
            }
            
            // Get pricing calculation method from room
            $isPerPerson = $ratePlan->room->pricing_calculation_method === 'per_person';
            
            try {
                // Start transaction
                DB::beginTransaction();
                
                // Get daily rates for this rate plan
                $dailyRates = DailyRate::where('rate_plan_id', $ratePlan->id)->get();
                
                if ($dailyRates->isEmpty()) {
                    $this->line("No daily rates found for rate plan #{$ratePlan->id}");
                    $progressBar->advance();
                    continue;
                }
                
                $ratesUpdated = 0;
                
                // Update each daily rate
                foreach ($dailyRates as $dailyRate) {
                    // Set pricing method
                    $dailyRate->is_per_person = $isPerPerson;
                    
                    // For per-person pricing, create prices_json
                    if ($isPerPerson) {
                        $basePrice = $dailyRate->base_price;
                        
                        // Create prices array for different occupancy
                        $prices = [];
                        
                        // Default: first person at base price, additional at 80% of base price
                        $capacity = $ratePlan->room->capacity_adults ?? 2;
                        
                        for ($i = 1; $i <= $capacity; $i++) {
                            if ($i === 1) {
                                $prices[(string)$i] = $basePrice;
                            } else {
                                // Additional persons at 80% of base price
                                $prices[(string)$i] = round($basePrice * 0.8, 2);
                            }
                        }
                        
                        $dailyRate->prices_json = $prices;
                    } else {
                        $dailyRate->prices_json = null;
                    }
                    
                    // Default: all rates are refundable
                    $dailyRate->is_refundable = true;
                    
                    // Save changes
                    $dailyRate->save();
                    $ratesUpdated++;
                }
                
                // Commit transaction
                DB::commit();
                
                $totalUpdated += $ratesUpdated;
                $this->line("Updated {$ratesUpdated} daily rates for rate plan #{$ratePlan->id}");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error updating rate plan #{$ratePlan->id}: " . $e->getMessage());
                Log::error("Error updating daily rates for rate plan #{$ratePlan->id}: " . $e->getMessage(), [
                    'rate_plan_id' => $ratePlan->id,
                    'trace' => $e->getTraceAsString()
                ]);
                $errors++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Update completed!");
        $this->line("Total rate plans processed: " . $ratePlans->count());
        $this->line("Total daily rates updated: {$totalUpdated}");
        $this->line("Errors encountered: {$errors}");
        
        return Command::SUCCESS;
    }
}