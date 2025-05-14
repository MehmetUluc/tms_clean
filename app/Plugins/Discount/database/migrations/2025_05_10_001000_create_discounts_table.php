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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type'); // Uses DiscountType enum
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('max_value', 10, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->string('stack_type')->default('stackable'); // Uses StackType enum
            $table->decimal('min_booking_value', 10, 2)->nullable();
            $table->integer('max_uses_total')->nullable();
            $table->integer('max_uses_per_user')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('discount_type');
            $table->index('is_active');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('stack_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};