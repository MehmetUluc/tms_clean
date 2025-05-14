<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('rate_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->json('daily_prices')->nullable()->comment('Her gün için fiyat detayları');
            $table->decimal('room_price', 10, 2)->default(0);
            $table->decimal('board_price', 10, 2)->default(0);
            $table->decimal('extra_bed_price', 10, 2)->default(0);
            $table->decimal('child_price', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('currency')->default('TRY');
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_prices');
    }
};