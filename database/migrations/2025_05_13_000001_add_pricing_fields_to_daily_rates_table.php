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
        Schema::table('daily_rates', function (Blueprint $table) {
            // Pricing method fields
            if (!Schema::hasColumn('daily_rates', 'is_per_person')) {
                $table->boolean('is_per_person')->default(false)
                    ->comment('Kişi başı fiyatlandırma mı?');
            }
            
            // JSON prices for per-person pricing
            if (!Schema::hasColumn('daily_rates', 'prices_json')) {
                $table->json('prices_json')->nullable()
                    ->comment('Kişi başı fiyatlandırmada kişi sayısına göre fiyatlar');
            }
            
            // Refund policy fields
            if (!Schema::hasColumn('daily_rates', 'is_refundable')) {
                $table->boolean('is_refundable')->default(true)
                    ->comment('İade edilebilir mi?');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->dropColumn([
                'is_per_person',
                'prices_json',
                'is_refundable'
            ]);
        });
    }
};