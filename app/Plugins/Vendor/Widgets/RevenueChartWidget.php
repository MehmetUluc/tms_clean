<?php

namespace App\Plugins\Vendor\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class RevenueChartWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'revenueChart';
    
    protected static ?string $heading = 'Gelir Grafiği';
    
    protected static ?string $pollingInterval = null;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getOptions(): array
    {
        $chartData = session('vendorChartData', $this->getDefaultChartData());
        
        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => [
                    'show' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Brüt Gelir',
                    'data' => $chartData['amounts'] ?? [26450, 28520, 27950, 29120, 32750, 35250, 38450],
                ],
                [
                    'name' => 'Net Gelir',
                    'data' => $chartData['netAmounts'] ?? [18450, 19520, 19950, 21120, 24750, 26250, 28450],
                ],
            ],
            'xaxis' => [
                'type' => 'datetime',
                'categories' => $chartData['dates'] ?? $this->getDefaultDates(),
                'labels' => [
                    'format' => 'dd MMM',
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'formatter' => 'function (value) { return "₺" + new Intl.NumberFormat("tr-TR").format(value) }',
                ],
            ],
            'colors' => ['#818cf8', '#10b981'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => [2, 2],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.2,
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (value) { return "₺" + new Intl.NumberFormat("tr-TR").format(value) }',
                ],
            ],
            'grid' => [
                'borderColor' => '#f1f1f1',
                'strokeDashArray' => 4,
            ],
        ];
    }
    
    private function getDefaultDates()
    {
        $dates = [];
        $today = Carbon::now();
        
        for ($i = 30; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dates[] = $date->format('Y-m-d');
        }
        
        return $dates;
    }
    
    private function getDefaultChartData()
    {
        return [
            'dates' => $this->getDefaultDates(),
            'amounts' => [26450, 28520, 27950, 29120, 32750, 35250, 38450, 37520, 39450, 43200, 42650, 44250, 45450, 49750, 48250, 47450, 51850, 52520, 54250, 58450, 57250, 59850, 61250, 65450, 66850, 68450, 70850, 72450, 74850, 76250],
            'netAmounts' => [18450, 19520, 19950, 21120, 24750, 26250, 28450, 27520, 28450, 31200, 30650, 32250, 33450, 36750, 35250, 34450, 37850, 38520, 39250, 42450, 41250, 42850, 44250, 46450, 47850, 48450, 49850, 50450, 51850, 53250],
        ];
    }
}