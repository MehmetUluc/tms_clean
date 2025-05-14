<?php

namespace App\Plugins\Accommodation\Filament\Widgets;

use App\Plugins\Accommodation\Models\RoomType;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularRoomTypes extends ChartWidget
{
    protected static ?string $heading = 'En Çok Tercih Edilen Oda Tipleri';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 5;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        // This would be the real implementation
        /*
        $data = DB::table('room_types')
            ->join('rooms', 'room_types.id', '=', 'rooms.room_type_id')
            ->join('reservations', 'rooms.id', '=', 'reservations.room_id')
            ->select('room_types.name', DB::raw('count(*) as total'))
            ->where('reservations.status', '<>', 'cancelled')
            ->groupBy('room_types.id', 'room_types.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        */
        
        // Dummy data for demonstration
        $labels = [
            'Standart Oda',
            'Superior Oda',
            'Deluxe Oda',
            'Junior Suit',
            'King Suit',
        ];
        
        $values = [
            42,
            35,
            28,
            15,
            10,
        ];
        
        // Generate colors for each segment
        $backgroundColors = [
            '#10b981', // green-500
            '#3b82f6', // blue-500
            '#f59e0b', // amber-500
            '#8b5cf6', // violet-500
            '#ec4899', // pink-500
        ];
        
        $hoverBackgroundColors = [
            '#059669', // green-600
            '#2563eb', // blue-600
            '#d97706', // amber-600
            '#7c3aed', // violet-600
            '#db2777', // pink-600
        ];
        
        return [
            'datasets' => [
                [
                    'label' => 'Rezervasyon Sayısı',
                    'data' => $values,
                    'backgroundColor' => $backgroundColors,
                    'hoverBackgroundColor' => $hoverBackgroundColors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverBorderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'padding' => 16,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": " + context.raw + " rezervasyon (" + 
                                Math.round(context.raw / context.dataset.data.reduce((a, b) => a + b, 0) * 100) + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
        ];
    }
    
    public function getColumnSpan(): int|string|array
    {
        return 1;
    }
}