<?php

namespace App\Plugins\Booking\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Gelir Analizi';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 3;

    // Default configuration
    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ],
    ];

    // Allow switching between monthly and quarterly views
    public ?string $filter = 'monthly';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'monthly' => 'Aylık Görünüm',
            'quarterly' => 'Çeyreklik Görünüm',
            'roomTypes' => 'Oda Tipine Göre',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'quarterly') {
            return $this->getQuarterlyData();
        } elseif ($this->filter === 'roomTypes') {
            return $this->getRoomTypeData();
        }
        
        return $this->getMonthlyData(); // Default
    }

    private function getMonthlyData(): array
    {
        // Dummy monthly revenue data in TL
        return [
            'datasets' => [
                [
                    'label' => 'Toplam Gelir (TL)',
                    'data' => [128500, 142700, 165400, 195600, 228900, 294500, 356000, 382500, 315000, 246000, 175000, 152000],
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Oda Gelirleri (TL)',
                    'data' => [98500, 112700, 135400, 165600, 198900, 254500, 316000, 342500, 275000, 196000, 135000, 122000],
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Ekstra Hizmetler (TL)',
                    'data' => [30000, 30000, 30000, 30000, 30000, 40000, 40000, 40000, 40000, 50000, 40000, 30000],
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1
                ],
            ],
            'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
        ];
    }

    private function getQuarterlyData(): array
    {
        // Dummy quarterly data
        return [
            'datasets' => [
                [
                    'label' => 'Toplam Gelir (TL)',
                    'data' => [436600, 719000, 1053500, 573000],
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Oda Gelirleri (TL)',
                    'data' => [346600, 619000, 933500, 453000],
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Ekstra Hizmetler (TL)',
                    'data' => [90000, 100000, 120000, 120000],
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1
                ],
            ],
            'labels' => ['1. Çeyrek', '2. Çeyrek', '3. Çeyrek', '4. Çeyrek'],
        ];
    }

    private function getRoomTypeData(): array
    {
        // Dummy data by room type
        return [
            'datasets' => [
                [
                    'label' => 'Toplam Gelir (TL)',
                    'data' => [685000, 825000, 495000, 395000, 282500],
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'
                    ],
                    'borderColor' => [
                        '#059669', '#2563eb', '#d97706', '#7c3aed', '#db2777'
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => ['Standart Oda', 'Superior Oda', 'Deluxe Oda', 'Junior Suit', 'King Suit'],
        ];
    }

    protected function getOptions(): array
    {
        $options = static::$options ?? [];
        
        // Special options for room type view (use doughnut or pie chart)
        if ($this->filter === 'roomTypes') {
            return array_merge($options, [
                'indexAxis' => 'y',
            ]);
        }
        
        return array_merge($options, [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return value.toLocaleString("tr-TR") + " TL" }',
                    ],
                ],
            ],
        ]);
    }
}