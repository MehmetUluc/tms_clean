<?php

namespace Database\Seeders;

use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\OccupancyRate;
use App\Plugins\Pricing\Models\ChildPolicy;
use App\Plugins\Pricing\Models\Inventory;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Models\Hotel;
use App\Models\BoardType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RatePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gerekli modelleri yükle
        $hotels = Hotel::all();
        $boardTypes = BoardType::all();
        
        // Her otel için fiyat planları oluştur
        foreach ($hotels as $hotel) {
            // Otele ait oda tipleri
            $roomTypes = RoomType::all();
            
            // Fiyat plan tipleri
            $planTypes = [
                'standard' => 'Standart',
                'advance_purchase' => 'Erken Rezervasyon',
                'last_minute' => 'Son Dakika',
                'non_refundable' => 'İade Edilemez',
                'weekend_special' => 'Hafta Sonu Özel',
            ];
            
            // Her oda tipi için fiyat planları oluştur
            foreach ($roomTypes as $roomType) {
                // Her pansiyon tipi için fiyat planları oluştur
                foreach ($boardTypes as $boardType) {
                    // Her plan tipi için bir tane oluştur
                    foreach ($planTypes as $planType => $planName) {
                        // Baz fiyat hesapla
                        $basePrice = $roomType->base_price;
                        
                        // Pansiyon tipine göre fiyat artışı
                        switch($boardType->code) {
                            case 'AI': // All Inclusive
                                $basePrice *= 1.5;
                                break;
                            case 'UAI': // Ultra All Inclusive
                                $basePrice *= 1.8;
                                break;
                            case 'FB': // Full Board
                                $basePrice *= 1.3;
                                break;
                            case 'HB': // Half Board
                                $basePrice *= 1.2;
                                break;
                            case 'BB': // Bed & Breakfast
                                $basePrice *= 1.1;
                                break;
                            // RO zaten baz fiyat
                        }
                        
                        // Plan tipine göre indirim/artış
                        switch($planType) {
                            case 'advance_purchase':
                                $basePrice *= 0.85; // %15 indirim
                                break;
                            case 'last_minute':
                                $basePrice *= 0.90; // %10 indirim
                                break;
                            case 'non_refundable':
                                $basePrice *= 0.80; // %20 indirim
                                break;
                            case 'weekend_special':
                                $basePrice *= 1.15; // %15 artış
                                break;
                        }
                        
                        // Fiyat planı oluştur
                        $ratePlan = RatePlan::create([
                            'hotel_id' => $hotel->id,
                            'room_type_id' => $roomType->id,
                            'board_type_id' => $boardType->id,
                            'name' => $roomType->name . ' - ' . $boardType->name . ' - ' . $planName,
                            'code' => Str::upper(substr($roomType->name, 0, 2) . '_' . $boardType->code . '_' . substr($planType, 0, 3)),
                            'description' => $this->getPlanDescription($planType, $boardType->name),
                            'type' => $planType,
                            'base_price' => $basePrice,
                            'is_public' => true,
                            'is_active' => true,
                            'min_nights' => ($planType == 'weekend_special') ? 2 : 1,
                            'max_nights' => ($planType == 'last_minute') ? 5 : 30,
                            'min_advance_days' => ($planType == 'advance_purchase') ? 30 : 0,
                            'max_advance_days' => ($planType == 'last_minute') ? 7 : 365,
                            'cancellation_policy' => $this->getCancellationPolicy($planType),
                            'booking_conditions' => $this->getBookingConditions($planType, $boardType->code),
                            'start_date' => Carbon::now()->startOfMonth(),
                            'end_date' => Carbon::now()->addYear()->endOfMonth(),
                        ]);
                        
                        // Doluluk fiyatları oluştur
                        $this->createOccupancyRates($ratePlan, $basePrice);
                        
                        // Çocuk politikaları oluştur
                        $this->createChildPolicies($ratePlan, $basePrice);
                        
                        // Günlük fiyatlar ve envanter oluştur
                        $this->createDailyRatesAndInventory($ratePlan, $basePrice, $roomType);
                    }
                }
            }
        }
    }
    
    /**
     * Fiyat planı açıklaması oluştur
     */
    private function getPlanDescription($planType, $boardType)
    {
        switch($planType) {
            case 'standard':
                return $boardType . ' pansiyon tipinde standart fiyat planı.';
            case 'advance_purchase':
                return $boardType . ' pansiyon tipinde, en az 30 gün önceden yapılan rezervasyonlarda geçerli indirimli plan.';
            case 'last_minute':
                return $boardType . ' pansiyon tipinde, 7 gün içindeki konaklamalar için son dakika fırsatı.';
            case 'non_refundable':
                return $boardType . ' pansiyon tipinde, iptal ve değişiklik yapılamayan iade edilemez özel fiyatlı plan.';
            case 'weekend_special':
                return $boardType . ' pansiyon tipinde, sadece Cuma-Pazar arası konaklamalarda geçerli özel hafta sonu planı.';
            default:
                return $boardType . ' pansiyon tipinde fiyat planı.';
        }
    }
    
    /**
     * İptal politikası oluştur
     */
    private function getCancellationPolicy($planType)
    {
        switch($planType) {
            case 'standard':
                return [
                    'is_refundable' => true,
                    'free_cancellation_days' => 3,
                    'penalty_rates' => [
                        '0-3' => 100, // 0-3 gün kala iptal: %100 ceza
                        '4-7' => 50,  // 4-7 gün kala iptal: %50 ceza
                        '8+' => 0     // 8+ gün kala iptal: %0 ceza
                    ]
                ];
            case 'advance_purchase':
                return [
                    'is_refundable' => true,
                    'free_cancellation_days' => 30,
                    'penalty_rates' => [
                        '0-7' => 100,
                        '8-15' => 75,
                        '16-30' => 50,
                        '31+' => 0
                    ]
                ];
            case 'last_minute':
                return [
                    'is_refundable' => true,
                    'free_cancellation_days' => 1,
                    'penalty_rates' => [
                        '0-1' => 100,
                        '2+' => 0
                    ]
                ];
            case 'non_refundable':
                return [
                    'is_refundable' => false,
                    'free_cancellation_days' => 0,
                    'penalty_rates' => [
                        '0+' => 100
                    ]
                ];
            case 'weekend_special':
                return [
                    'is_refundable' => true,
                    'free_cancellation_days' => 7,
                    'penalty_rates' => [
                        '0-3' => 100,
                        '4-7' => 50,
                        '8+' => 0
                    ]
                ];
            default:
                return [
                    'is_refundable' => true,
                    'free_cancellation_days' => 3,
                    'penalty_rates' => [
                        '0-3' => 100,
                        '4+' => 0
                    ]
                ];
        }
    }
    
    /**
     * Rezervasyon koşulları oluştur
     */
    private function getBookingConditions($planType, $boardType)
    {
        $conditions = [
            'Rezervasyon sırasında geçerli kredi kartı bilgisi gereklidir.',
        ];
        
        switch($planType) {
            case 'standard':
                $conditions[] = 'Standart rezervasyon koşulları geçerlidir.';
                break;
            case 'advance_purchase':
                $conditions[] = 'Rezervasyon sırasında toplam tutarın %30\'u ön ödeme olarak tahsil edilir.';
                $conditions[] = 'Kalan tutar giriş tarihinden 15 gün önce tahsil edilir.';
                break;
            case 'last_minute':
                $conditions[] = 'Rezervasyon sırasında toplam tutar tahsil edilir.';
                break;
            case 'non_refundable':
                $conditions[] = 'Rezervasyon sırasında toplam tutar tahsil edilir.';
                $conditions[] = 'İptal, değişiklik ve iade yapılamaz.';
                break;
            case 'weekend_special':
                $conditions[] = 'Sadece hafta sonları için geçerlidir (Cuma-Pazar arası konaklama).';
                $conditions[] = 'Minimum 2 gece konaklama zorunludur.';
                break;
        }
        
        switch($boardType) {
            case 'AI':
            case 'UAI':
                $conditions[] = 'Tüm yiyecek ve içecekler belirlenen saatlerde dahildir.';
                break;
            case 'FB':
                $conditions[] = 'Kahvaltı, öğle ve akşam yemeği dahildir. İçecekler ekstra ücretlendirilir.';
                break;
            case 'HB':
                $conditions[] = 'Kahvaltı ve akşam yemeği dahildir. İçecekler ekstra ücretlendirilir.';
                break;
            case 'BB':
                $conditions[] = 'Sadece kahvaltı dahildir. Diğer öğünler ve içecekler ekstra ücretlendirilir.';
                break;
            case 'RO':
                $conditions[] = 'Yemek dahil değildir. Tüm yiyecek ve içecekler ekstra ücretlendirilir.';
                break;
        }
        
        return $conditions;
    }
    
    /**
     * Doluluk fiyatları oluştur
     */
    private function createOccupancyRates($ratePlan, $basePrice)
    {
        // Tek kişi, iki kişi, üç kişi ve dört kişi doluluk fiyatları
        $occupancies = [
            1 => $basePrice * 0.8,                // Tek kişi: %20 indirim
            2 => $basePrice,                     // İki kişi: Baz fiyat
            3 => $basePrice + ($basePrice * 0.4), // Üç kişi: %40 ek fiyat
            4 => $basePrice + ($basePrice * 0.7)  // Dört kişi: %70 ek fiyat
        ];
        
        foreach ($occupancies as $occupancy => $price) {
            OccupancyRate::create([
                'rate_plan_id' => $ratePlan->id,
                'occupancy' => $occupancy,
                'price' => round($price, 2),
                'is_active' => true
            ]);
        }
    }
    
    /**
     * Çocuk politikaları oluştur
     */
    private function createChildPolicies($ratePlan, $basePrice)
    {
        // Yaş gruplarına göre politikalar
        $agePolicies = [
            [
                'min_age' => 0,
                'max_age' => 2,
                'description' => '0-2 yaş bebek',
                'price_type' => 'free',
                'price' => 0
            ],
            [
                'min_age' => 3,
                'max_age' => 6,
                'description' => '3-6 yaş çocuk',
                'price_type' => 'percentage',
                'price' => 50  // %50 fiyat
            ],
            [
                'min_age' => 7,
                'max_age' => 12,
                'description' => '7-12 yaş çocuk',
                'price_type' => 'percentage',
                'price' => 70  // %70 fiyat
            ],
            [
                'min_age' => 13,
                'max_age' => 17,
                'description' => '13-17 yaş genç',
                'price_type' => 'fixed',
                'price' => $basePrice * 0.8  // Sabit fiyat (baz fiyatın %80'i)
            ]
        ];
        
        foreach ($agePolicies as $policy) {
            ChildPolicy::create([
                'rate_plan_id' => $ratePlan->id,
                'min_age' => $policy['min_age'],
                'max_age' => $policy['max_age'],
                'description' => $policy['description'],
                'price_type' => $policy['price_type'],
                'price' => $policy['price'],
                'is_active' => true
            ]);
        }
    }
    
    /**
     * Günlük fiyatlar ve envanter oluştur
     */
    private function createDailyRatesAndInventory($ratePlan, $basePrice, $roomType)
    {
        // Bugünden başlayarak 365 gün
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addYear()->endOfDay();
        
        // Her gün için
        $currentDate = clone $startDate;
        
        while ($currentDate->lte($endDate)) {
            // Mevsimsel etkiyi hesapla (yaz ayları daha pahalı)
            $seasonMultiplier = 1.0;
            $month = $currentDate->month;
            
            // Yüksek sezon (Haziran-Ağustos)
            if ($month >= 6 && $month <= 8) {
                $seasonMultiplier = 1.5;
            } 
            // Omuz sezon (Nisan-Mayıs, Eylül-Ekim)
            elseif (($month >= 4 && $month <= 5) || ($month >= 9 && $month <= 10)) {
                $seasonMultiplier = 1.2;
            }
            // Diğer aylar düşük sezon (kış) - çarpan 1.0
            
            // Hafta sonu etkisi (Cuma-Cumartesi günleri daha pahalı)
            $weekendMultiplier = ($currentDate->isWeekend()) ? 1.2 : 1.0;
            
            // Rastgele küçük fiyat dalgalanmaları
            $randomVariation = rand(95, 105) / 100;
            
            // Günlük fiyatı hesapla
            $dailyPrice = $basePrice * $seasonMultiplier * $weekendMultiplier * $randomVariation;
            
            // Günlük fiyat kaydı oluştur
            DailyRate::create([
                'rate_plan_id' => $ratePlan->id,
                'date' => $currentDate->format('Y-m-d'),
                'price' => round($dailyPrice, 2),
                'min_stay' => $ratePlan->min_nights,
                'max_stay' => $ratePlan->max_nights,
                'closed_to_arrival' => false,
                'closed_to_departure' => false,
                'is_active' => true
            ]);
            
            // Envanter kaydı oluştur - rastgele stok
            Inventory::create([
                'rate_plan_id' => $ratePlan->id,
                'date' => $currentDate->format('Y-m-d'),
                'total_inventory' => rand(3, 10),  // Bu oda tipinden otelde ne kadar var 
                'remaining' => rand(0, 3),         // Kalan oda sayısı (0-3 arası)
                'status' => rand(0, 10) > 8 ? 'closed' : 'open',  // %20 ihtimalle o gün kapalı
                'is_active' => true
            ]);
            
            // Sonraki güne geç
            $currentDate->addDay();
        }
    }
}