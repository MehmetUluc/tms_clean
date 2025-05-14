@props(['widgets' => []])

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 fi-wi p-2">
    @foreach ($widgets as $widgetKey => $widget)
        @if (class_exists($widget))
            <div class="@if (in_array($widget, [
                \App\Filament\Widgets\LatestReservations::class,
                \App\Filament\Widgets\HotelOccupancyChart::class,
                \App\Filament\Widgets\RevenueChart::class,
            ])) col-span-1 md:col-span-2 @elseif ($widget == \App\Filament\Widgets\DashboardOverview::class) col-span-1 md:col-span-2 @else col-span-1 @endif transition-all duration-300 hover:scale-[1.01]">
                @if (method_exists($widget, 'canView'))
                    @if ($widget::canView())
                        @livewire($widget, [], key("widget-{$widgetKey}"))
                    @endif
                @else
                    @livewire($widget, [], key("widget-{$widgetKey}"))
                @endif
            </div>
        @endif
    @endforeach
</div>