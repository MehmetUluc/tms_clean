<?php

namespace App\Plugins\Core\src\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardOverview extends Widget
{
    protected static ?string $title = 'Dashboard Overview';
    protected static string $view = 'filament.widgets.dashboard-overview';

    protected function getViewData(): array
    {
        return [
            'stat1' => [
                'label' => 'Total Users',
                'value' => 1234,
            ],
            'stat2' => [
                'label' => 'Total Revenue',
                'value' => '$45,678',
            ],
            'stat3' => [
                'label' => 'New Reservations',
                'value' => 56,
            ],
        ];
    }
}