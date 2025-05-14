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
        Schema::create('discount_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $table->string('condition_type'); // Uses ConditionType enum
            $table->string('operator'); // Uses ConditionOperator enum
            $table->json('value');
            $table->timestamps();
            $table->softDeletes();

            $table->index('discount_id');
            $table->index('condition_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_conditions');
    }
};