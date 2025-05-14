<?php

namespace App\Plugins\Vendor\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class TransactionTypeWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'transactionTypeChart';
    
    protected static ?string $heading = 'İşlem Türleri';
    
    protected static ?string $pollingInterval = null;
    
    protected int | string | array $columnSpan = 1;
    
    protected function getOptions(): array
    {
        $typeData = session('vendorTypeBreakdown', $this->getDefaultTypeData());
        
        // İşlem türlerini ve yüzdelerini ayır
        $types = [];
        $percentages = [];
        
        foreach ($typeData as $data) {
            $types[] = $data['type'];
            $percentages[] = $data['percentage'];
        }
        
        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $percentages,
            'labels' => $types,
            'colors' => ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6'],
            'legend' => [
                'position' => 'bottom',
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'height' => 300
                        ],
                        'legend' => [
                            'position' => 'bottom'
                        ]
                    ]
                ]
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (value) { return value + "%" }',
                ],
            ],
        ];
    }
    
    private function getDefaultTypeData()
    {
        return [
            [
                'type' => 'Rezervasyon',
                'count' => 75,
                'amount' => 850000,
                'percentage' => 68,
            ],
            [
                'type' => 'İptal',
                'count' => 15,
                'amount' => 120000,
                'percentage' => 9.5,
            ],
            [
                'type' => 'Değişiklik',
                'count' => 12,
                'amount' => 45000,
                'percentage' => 3.5,
            ],
            [
                'type' => 'Ödeme',
                'count' => 26,
                'amount' => 240000,
                'percentage' => 19,
            ],
        ];
    }
}