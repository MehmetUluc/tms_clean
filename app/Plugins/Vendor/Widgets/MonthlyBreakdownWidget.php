<?php

namespace App\Plugins\Vendor\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class MonthlyBreakdownWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'monthlyBreakdownChart';
    
    protected static ?string $heading = 'Aylık Performans';
    
    protected static ?string $pollingInterval = null;
    
    protected int | string | array $columnSpan = 2;
    
    protected function getOptions(): array
    {
        $monthlyData = session('vendorMonthlyBreakdown', $this->getDefaultMonthlyData());
        
        // Ayları ve miktarları ayır
        $months = [];
        $amounts = [];
        $commissions = [];
        $netAmounts = [];
        
        foreach ($monthlyData as $data) {
            $months[] = $data['month'];
            $amounts[] = $data['amount'];
            $commissions[] = $data['commission'];
            $netAmounts[] = $data['net_amount'];
        }
        
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stacked' => true,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Net Gelir',
                    'data' => $netAmounts,
                ],
                [
                    'name' => 'Komisyon',
                    'data' => $commissions,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
            ],
            'yaxis' => [
                'labels' => [
                    'formatter' => 'function (value) { return "₺" + new Intl.NumberFormat("tr-TR").format(value) }',
                ],
            ],
            'colors' => ['#10b981', '#ef4444'],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                    'borderRadius' => 5,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'position' => 'top',
            ],
            'fill' => [
                'opacity' => 1,
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (value) { return "₺" + new Intl.NumberFormat("tr-TR").format(value) }',
                ],
            ],
        ];
    }
    
    private function getDefaultMonthlyData()
    {
        return [
            [
                'month' => 'Oca 2025',
                'amount' => 145000,
                'commission' => 14500,
                'net_amount' => 130500,
            ],
            [
                'month' => 'Şub 2025',
                'amount' => 158000,
                'commission' => 15800,
                'net_amount' => 142200,
            ],
            [
                'month' => 'Mar 2025',
                'amount' => 175000,
                'commission' => 17500,
                'net_amount' => 157500,
            ],
            [
                'month' => 'Nis 2025',
                'amount' => 190000,
                'commission' => 19000,
                'net_amount' => 171000,
            ],
            [
                'month' => 'May 2025',
                'amount' => 210000,
                'commission' => 21000,
                'net_amount' => 189000,
            ],
        ];
    }
}