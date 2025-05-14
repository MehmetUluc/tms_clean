<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('booking_prices')) {
            Schema::create('booking_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('rate_plan_id')->constrained('rate_plans')->onDelete('cascade');
            $table->date('date')->comment('Konaklama günü');
            $table->decimal('price', 10, 2)->comment('O gün için uygulanan fiyat');
            $table->integer('guests_count')->default(1)->comment('Misafir sayısı');
            $table->boolean('is_per_person')->default(true)->comment('Kişi bazlı mı (true) ünite bazlı mı (false)');
            $table->json('original_data')->nullable()->comment('Fiyat hesaplama anındaki orijinal veri (debugging için)');
            $table->timestamps();
            
            // Her rezervasyon için aynı tarihte tek bir fiyat olabilir
            $table->unique(['reservation_id', 'date'], 'booking_price_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_prices');
    }
};
