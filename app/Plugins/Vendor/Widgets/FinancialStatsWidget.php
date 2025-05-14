<?php

namespace App\Plugins\Vendor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Plugins\Vendor\Models\VendorTransaction;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    public $summary;
    
    public function mount($summary) 
    {
        $this->summary = $summary;
    }
    
    protected function getStats(): array
    {
        $currency = config('vendor.default_currency', '₺');
        $balance = $this->summary['balance'] ?? 0;
        $totalTransactions = $this->summary['transactions']['total_count'] ?? 0;
        $totalAmount = $this->summary['transactions']['total_amount'] ?? 0;
        $totalNetAmount = $this->summary['transactions']['total_net_amount'] ?? 0;
        $pendingAmount = $this->summary['payments']['pending_payment_amount'] ?? 0;
        
        return [
            Stat::make('Bakiye', number_format($balance, 2) . ' ' . $currency)
                ->description('Kullanılabilir Bakiye')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border-t-4 border-indigo-500',
                ]),
                
            Stat::make('Brüt Gelir', number_format($totalAmount, 2) . ' ' . $currency)
                ->description($totalTransactions . ' İşlem')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([8, 12, 10, 18, 15, 20, 18])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border-t-4 border-emerald-500',
                ]),
                
            Stat::make('Net Gelir', number_format($totalNetAmount, 2) . ' ' . $currency)
                ->description('Komisyon sonrası')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning')
                ->chart([15, 10, 12, 10, 14, 12, 16])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border-t-4 border-amber-500',
                ]),
                
            Stat::make('Bekleyen Ödemeler', number_format($pendingAmount, 2) . ' ' . $currency)
                ->description('Ödeme talepleri')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->chart([5, 3, 8, 5, 10, 8, 15])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 border-t-4 border-pink-500',
                ]),
        ];
    }
}