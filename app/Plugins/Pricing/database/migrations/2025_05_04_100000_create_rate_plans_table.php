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
        if (!Schema::hasTable('rate_plans')) {
            Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('occupancy_pricing')->default(false)->comment('True for occupancy-based pricing, false for unit-based');
            $table->integer('min_stay')->default(1);
            $table->integer('max_stay')->nullable();
            $table->enum('payment_type', ['pay_online', 'reserve_only', 'inquire_only'])->default('reserve_only');
            $table->json('restriction_days')->nullable()->comment('Days of the week where this plan cannot be used');
            $table->text('cancellation_policy')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('meal_plan', ['none', 'breakfast', 'half_board', 'full_board', 'all_inclusive'])->default('none');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure there is only one default rate plan per room
            $table->unique(['room_id', 'is_default'], 'unique_default_rate_plan_per_room');
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