<?php

namespace Database\Seeders;

use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Booking\Models\Guest;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rezervasyon verileri için gerekli modelleri getir
        $hotels = Hotel::all();
        $users = User::all();
        
        // İsim ve soyisim listeleri (rastgele misafir isimleri için)
        $firstNames = ['Ali', 'Mehmet', 'Ayşe', 'Fatma', 'Mustafa', 'Ahmet', 'Zeynep', 'Emine', 'İbrahim', 
                      'Hatice', 'Hüseyin', 'Yusuf', 'Merve', 'Esra', 'Ömer', 'Elif', 'Murat', 'Zehra', 
                      'Hasan', 'Selin', 'Emre', 'Gizem', 'Tolga', 'Burak', 'Deniz', 'Ece', 'Serkan', 'Derya'];
                      
        $lastNames = ['Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Özdemir', 'Arslan', 'Doğan', 
                     'Kılıç', 'Aslan', 'Çetin', 'Şimşek', 'Öztürk', 'Aydın', 'Özkan', 'Yıldırım', 'Tekin', 
                     'Kurt', 'Özkan', 'Koç', 'Acar', 'Aksoy', 'Korkmaz', 'Erdoğan', 'Akgün', 'Tuncer', 'Özçelik'];
        
        // Rezervasyon durumları
        $statuses = ['confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'];
        
        // Her otel için rezervasyon oluştur
        foreach ($hotels as $hotel) {
            // Bu otele ait tüm odaları getir
            $rooms = Room::where('hotel_id', $hotel->id)->get();
            
            // Her otel için 10-20 rezervasyon oluştur
            $reservationCount = rand(10, 20);
            
            for ($i = 0; $i < $reservationCount; $i++) {
                // Rastgele bir oda seç
                $room = $rooms->random();
                
                // Rastgele tarihler oluştur (son 1 ay ve gelecek 3 ay arası)
                $startOffset = rand(-30, 90); // -30 gün önce ile 90 gün sonrası arası
                $checkIn = Carbon::now()->addDays($startOffset)->startOfDay();
                $nights = rand(1, 7); // 1-7 gece arası konaklama
                $checkOut = clone $checkIn;
                $checkOut->addDays($nights);
                
                // Rezervasyon kaydı zamanını hesapla (check-in'den 1-30 gün önce)
                $bookingDate = clone $checkIn;
                $bookingDate->subDays(rand(1, 30));
                
                // Otel için minimum ve maksimum fiyat aralığı
                $basePrice = $room->price;
                $totalPrice = $basePrice * $nights;
                
                // Yetişkin ve çocuk sayısı
                $adults = rand(1, $room->max_adults);
                $children = rand(0, $room->max_children);
                
                // Rastgele bir durum seç
                $status = $checkIn->isPast() ? $statuses[array_rand($statuses)] : 'confirmed';
                
                // Eğer check-in gelecekte ise, checked_in ve checked_out durumlarını eleme
                if ($checkIn->isFuture() && ($status == 'checked_in' || $status == 'checked_out')) {
                    $status = 'confirmed';
                }
                
                // Eğer check-out geçmişte ise, durumu checked_out yap
                if ($checkOut->isPast() && $status != 'cancelled' && $status != 'no_show') {
                    $status = 'checked_out';
                }
                
                // Rezervasyon numarası oluştur
                $reservationNumber = strtoupper(substr($hotel->name, 0, 2)) . '-' . rand(10000, 99999);
                
                // Rezervasyon oluştur
                $reservation = Reservation::create([
                    'hotel_id' => $hotel->id,
                    'room_id' => $room->id,
                    'reservation_number' => $reservationNumber,
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'nights' => $nights,
                    'adults' => $adults,
                    'children' => $children,
                    'total_price' => $totalPrice,
                    'currency' => 'TRY',
                    'payment_status' => rand(0, 1) ? 'paid' : 'pending',
                    'payment_method' => rand(0, 1) ? 'credit_card' : 'bank_transfer',
                    'notes' => rand(0, 3) === 0 ? 'Özel istek: Oda üst katlarda olsun.' : null,
                    'source' => rand(0, 1) ? 'website' : 'phone',
                    'created_at' => $bookingDate,
                    'created_by' => $users->random()->id,
                ]);
                
                // Ana misafir oluştur
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $email = strtolower(str_replace(' ', '', $firstName)) . '.' . strtolower(str_replace(' ', '', $lastName)) . '@example.com';
                
                $mainGuest = Guest::create([
                    'reservation_id' => $reservation->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => '+90' . rand(500, 599) . rand(1000000, 9999999),
                    'nationality' => 'TR',
                    'id_type' => rand(0, 1) ? 'tc_kimlik' : 'passport',
                    'id_number' => rand(10000000000, 99999999999),
                    'birth_date' => Carbon::now()->subYears(rand(25, 60))->subDays(rand(0, 365)),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'address' => 'Örnek Mahallesi, Test Sokak No:' . rand(1, 100),
                    'city' => $hotel->city,
                    'country' => 'Türkiye',
                    'is_primary' => true,
                    'notes' => null,
                ]);
                
                // Eğer birden fazla yetişkin varsa, diğer misafirleri de ekle
                if ($adults > 1 || $children > 0) {
                    // Ekstra yetişkinler
                    for ($j = 1; $j < $adults; $j++) {
                        $firstName = $firstNames[array_rand($firstNames)];
                        $lastName = $mainGuest->last_name; // Aynı soyisim (aile varsayımı)
                        
                        Guest::create([
                            'reservation_id' => $reservation->id,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => null,
                            'phone' => null,
                            'nationality' => 'TR',
                            'id_type' => rand(0, 1) ? 'tc_kimlik' : 'passport',
                            'id_number' => rand(10000000000, 99999999999),
                            'birth_date' => Carbon::now()->subYears(rand(25, 60))->subDays(rand(0, 365)),
                            'gender' => rand(0, 1) ? 'male' : 'female',
                            'address' => $mainGuest->address,
                            'city' => $mainGuest->city,
                            'country' => $mainGuest->country,
                            'is_primary' => false,
                            'notes' => null,
                        ]);
                    }
                    
                    // Çocuklar
                    for ($j = 0; $j < $children; $j++) {
                        $firstName = $firstNames[array_rand($firstNames)];
                        $lastName = $mainGuest->last_name; // Aynı soyisim (aile varsayımı)
                        
                        Guest::create([
                            'reservation_id' => $reservation->id,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => null,
                            'phone' => null,
                            'nationality' => 'TR',
                            'id_type' => 'tc_kimlik',
                            'id_number' => rand(10000000000, 99999999999),
                            'birth_date' => Carbon::now()->subYears(rand(3, 15))->subDays(rand(0, 365)),
                            'gender' => rand(0, 1) ? 'male' : 'female',
                            'address' => $mainGuest->address,
                            'city' => $mainGuest->city,
                            'country' => $mainGuest->country,
                            'is_primary' => false,
                            'is_child' => true,
                            'notes' => null,
                        ]);
                    }
                }
            }
        }
    }
}