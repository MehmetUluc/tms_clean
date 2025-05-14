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
        Schema::create('discount_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $table->string('target_type'); // Uses TargetType enum
            $table->unsignedBigInteger('target_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('discount_id');
            $table->index('target_type');
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_targets');
    }
};