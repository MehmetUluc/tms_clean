<?php

namespace App\Plugins\Pricing\Jobs;

use App\Plugins\Pricing\Services\DailyRateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPriceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $ratePlanId;
    protected array $data;
    protected ?string $startDate;
    protected ?string $endDate;
    protected bool $isBulkUpdate;
    
    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;
    
    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param int $ratePlanId
     * @param array $data Either array of dates with prices or price data for bulk update
     * @param string|null $startDate Start date for bulk update (null for array format)
     * @param string|null $endDate End date for bulk update (null for array format)
     * @param bool $isBulkUpdate Whether this is a bulk update (true) or an array of dates (false)
     */
    public function __construct(
        int $ratePlanId, 
        array $data, 
        ?string $startDate = null, 
        ?string $endDate = null, 
        bool $isBulkUpdate = false
    ) {
        $this->ratePlanId = $ratePlanId;
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isBulkUpdate = $isBulkUpdate;
    }

    /**
     * Execute the job.
     */
    public function handle(DailyRateService $dailyRateService): void
    {
        try {
            Log::info('Processing price import job', [
                'rate_plan_id' => $this->ratePlanId,
                'is_bulk_update' => $this->isBulkUpdate,
                'data_count' => $this->isBulkUpdate ? 'Bulk update' : count($this->data),
            ]);
            
            if ($this->isBulkUpdate) {
                // Handle bulk update of a date range with the same price data
                $result = $dailyRateService->bulkSaveDailyRates(
                    $this->ratePlanId,
                    $this->startDate,
                    $this->endDate,
                    $this->data
                );
            } else {
                // Handle array of dates with different prices
                $result = $dailyRateService->saveDailyRatesFromArray(
                    $this->ratePlanId,
                    $this->data
                );
            }
            
            if ($result) {
                Log::info('Price import job completed successfully', [
                    'rate_plan_id' => $this->ratePlanId,
                ]);
            } else {
                throw new \Exception('Failed to save prices');
            }
        } catch (\Exception $e) {
            Log::error('Price import job failed: ' . $e->getMessage(), [
                'rate_plan_id' => $this->ratePlanId,
                'exception' => $e,
            ]);
            
            // Rethrow to trigger a retry if attempts remain
            throw $e;
        }
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Price import job failed after all retries: ' . $exception->getMessage(), [
            'rate_plan_id' => $this->ratePlanId,
            'exception' => $exception,
        ]);
    }
}