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
        // Var olan rate_plans tablosunu yeniden oluşturmak güvenli değil
        if (!Schema::hasTable('rate_plans')) {
            Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('board_type_id')->constrained('board_types')->onDelete('cascade');
            $table->boolean('is_per_person')->default(true)->comment('true: kişi bazlı, false: ünite bazlı');
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            // Her otel-oda-board_type için unique kombinasyon
            $table->unique(['hotel_id', 'room_id', 'board_type_id'], 'rate_plan_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};
