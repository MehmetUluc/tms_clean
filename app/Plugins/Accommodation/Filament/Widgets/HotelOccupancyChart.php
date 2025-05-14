<?php

namespace App\Plugins\Accommodation\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class HotelOccupancyChart extends ChartWidget
{
    protected static ?string $heading = 'Otel Doluluk Oranı';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 2;
    
    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // Generate dummy data for the next 30 days
        $dates = [];
        $occupancyRates = [];
        $forecastRates = [];
        
        $today = Carbon::today();
        
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            $dates[] = $date->format('d M');
            
            // Generate dummy occupancy data with weekend peaks
            $isWeekend = $date->isWeekend();
            $baseOccupancy = $isWeekend ? rand(65, 85) : rand(45, 65);
            
            // Make the trend generally upward for summer season
            $trendFactor = min(25, $i / 1.5);
            $occupancy = min(95, $baseOccupancy + $trendFactor);
            
            // Past dates (real data)
            if ($i < 10) {
                $occupancyRates[] = $occupancy;
                $forecastRates[] = null;
            } 
            // Future dates (forecast)
            else {
                $occupancyRates[] = null;
                $forecastRates[] = $occupancy;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Doluluk Oranı (%)',
                    'data' => $occupancyRates,
                    'borderColor' => '#0284c7',
                    'backgroundColor' => 'rgba(2, 132, 199, 0.1)',
                    'fill' => true,
                    'tension' => 0.2,
                ],
                [
                    'label' => 'Tahmin Edilen Doluluk (%)',
                    'data' => $forecastRates,
                    'borderColor' => '#7c3aed',
                    'backgroundColor' => 'rgba(124, 58, 237, 0.1)',
                    'borderDash' => [5, 5],
                    'fill' => true,
                    'tension' => 0.2,
                ],
            ],
            'labels' => $dates,
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%" }',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}