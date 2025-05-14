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
        if (!Schema::hasTable('rate_periods')) {
            Schema::create('rate_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained('rate_plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('base_price', 10, 2)->nullable()->comment('Ünite bazlı ise temel fiyat');
            $table->json('prices')->nullable()->comment('Kişi bazlı ise fiyatlar: {"1": 100, "2": 180, "3": 240}');
            $table->integer('min_stay')->default(1)->comment('Minimum konaklama gün sayısı');
            $table->integer('quantity')->default(0)->comment('Günlük stok');
            $table->string('sales_type')->default('direct')->comment('direct: Direkt Satış, ask_sell: Sor-Sat');
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            // Aynı plan için tarih aralıklarının çakışmaması için index
            $table->index(['rate_plan_id', 'start_date', 'end_date'], 'rate_period_date_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_periods');
    }
};
