<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('board_type_id')->constrained()->onDelete('cascade');
            $table->boolean('is_per_person')->default(true)->comment('true: kişi bazlı, false: ünite bazlı');
            $table->boolean('status')->default(true);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Her otel-oda-board_type için unique kombinasyon
            $table->unique(['hotel_id', 'room_id', 'board_type_id'], 'rate_plan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};