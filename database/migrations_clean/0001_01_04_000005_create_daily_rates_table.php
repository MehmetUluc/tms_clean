<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('base_price', 10, 2)->comment('Temel fiyat - kişi başı veya oda başı');
            $table->string('currency', 3)->default('TRY');
            $table->boolean('is_closed')->default(false)->comment('Kapalı günler için rezervasyon yapılamaz');
            $table->integer('min_stay_arrival')->default(1)->comment('Bu tarihte giriş için minimum konaklama');
            $table->integer('inventory')->default(10)->comment('Müsait oda sayısı');
            $table->enum('status', ['available', 'limited', 'sold_out'])->default('available');
            
            // Pricing method fields
            $table->boolean('is_per_person')->default(false)->comment('Kişi başı fiyatlandırma mı?');
            $table->json('prices_json')->nullable()->comment('Kişi başı fiyatlandırmada kişi sayısına göre fiyatlar');
            
            // Refund policy
            $table->boolean('is_refundable')->default(true)->comment('İade edilebilir mi?');
            
            // Sales type
            $table->string('sales_type', 20)->default('direct')->comment('direct veya ask_sell (Sor-Sat)');
            
            $table->string('notes')->nullable();
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            
            // Each date can only have one rate for a specific rate plan
            $table->unique(['rate_plan_id', 'date']);
            $table->index(['date', 'rate_plan_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
    }
};