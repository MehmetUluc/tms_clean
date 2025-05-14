<?php

namespace App\Console\Commands;

use Database\Seeders\PricingSeeder;
use Illuminate\Console\Command;

class GeneratePricingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-pricing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample pricing data for hotels and rooms';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fiyat verileri oluşturuluyor...');
        
        // Run the pricing seeder
        $seeder = new PricingSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Fiyat verileri başarıyla oluşturuldu!');
        
        return Command::SUCCESS;
    }
}