<?php

namespace App\Plugins\Booking\Filament\Widgets;

use App\Plugins\Booking\Models\Reservation;
use Filament\Widgets\Widget;

class LatestReservations extends Widget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static string $view = 'booking::widgets.latest-reservations';
    
    public function getDummyReservations(): array
    {
        // Create dummy reservation data
        return [
            [
                'id' => 1245,
                'guest_name' => 'Ahmet Yılmaz',
                'room_name' => 'Deluxe Oda 101',
                'check_in' => now()->addDays(5)->format('d.m.Y'),
                'check_out' => now()->addDays(8)->format('d.m.Y'),
                'nights' => 3,
                'adults' => 2,
                'children' => 1,
                'total_price' => number_format(4500, 2, ',', '.') . ' TL',
                'status' => 'confirmed',
                'status_label' => 'Onaylandı',
                'status_color' => 'success',
                'created_at' => now()->subHours(2)->format('d.m.Y H:i'),
            ],
            [
                'id' => 1244,
                'guest_name' => 'Ayşe Demir',
                'room_name' => 'Superior Oda 205',
                'check_in' => now()->addDays(1)->format('d.m.Y'),
                'check_out' => now()->addDays(5)->format('d.m.Y'),
                'nights' => 4,
                'adults' => 2,
                'children' => 0,
                'total_price' => number_format(5200, 2, ',', '.') . ' TL',
                'status' => 'pending',
                'status_label' => 'Beklemede',
                'status_color' => 'warning',
                'created_at' => now()->subHours(6)->format('d.m.Y H:i'),
            ],
            [
                'id' => 1243,
                'guest_name' => 'Mehmet Kaya',
                'room_name' => 'King Suit 301',
                'check_in' => now()->format('d.m.Y'),
                'check_out' => now()->addDays(7)->format('d.m.Y'),
                'nights' => 7,
                'adults' => 1,
                'children' => 0,
                'total_price' => number_format(7000, 2, ',', '.') . ' TL',
                'status' => 'checked_in',
                'status_label' => 'Giriş Yapıldı',
                'status_color' => 'success',
                'created_at' => now()->subHours(12)->format('d.m.Y H:i'),
            ],
            [
                'id' => 1242,
                'guest_name' => 'Fatma Çelik',
                'room_name' => 'Family Oda 215',
                'check_in' => now()->subDays(3)->format('d.m.Y'),
                'check_out' => now()->addDays(1)->format('d.m.Y'),
                'nights' => 4,
                'adults' => 2,
                'children' => 2,
                'total_price' => number_format(6800, 2, ',', '.') . ' TL',
                'status' => 'checked_in',
                'status_label' => 'Giriş Yapıldı',
                'status_color' => 'success',
                'created_at' => now()->subDays(1)->format('d.m.Y H:i'),
            ],
            [
                'id' => 1241,
                'guest_name' => 'Ali Öztürk',
                'room_name' => 'Junior Suit 401',
                'check_in' => now()->subDays(7)->format('d.m.Y'),
                'check_out' => now()->subDays(2)->format('d.m.Y'),
                'nights' => 5,
                'adults' => 2,
                'children' => 1,
                'total_price' => number_format(8500, 2, ',', '.') . ' TL',
                'status' => 'checked_out',
                'status_label' => 'Çıkış Yapıldı',
                'status_color' => 'primary',
                'created_at' => now()->subDays(10)->format('d.m.Y H:i'),
            ],
        ];
    }
}