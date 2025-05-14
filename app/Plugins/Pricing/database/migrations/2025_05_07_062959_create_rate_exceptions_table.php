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
        if (!Schema::hasTable('rate_exceptions')) {
            Schema::create('rate_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_period_id')->constrained('rate_periods')->onDelete('cascade');
            $table->date('date')->comment('İstisna tarihi');
            $table->decimal('base_price', 10, 2)->nullable()->comment('Ünite bazlı ise temel fiyat');
            $table->json('prices')->nullable()->comment('Kişi bazlı ise fiyatlar: {"1": 100, "2": 180, "3": 240}');
            $table->integer('min_stay')->nullable()->comment('Minimum konaklama gün sayısı');
            $table->integer('quantity')->nullable()->comment('Günlük stok');
            $table->string('sales_type')->nullable()->comment('direct: Direkt Satış, ask_sell: Sor-Sat');
            $table->boolean('status')->nullable();
            $table->timestamps();
            
            // Her rate_period için aynı tarihte tek bir istisna olabilir
            $table->unique(['rate_period_id', 'date'], 'rate_exception_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_exceptions');
    }
};
