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
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('base_price', 10, 2)->comment('For unit-based pricing');
            $table->string('currency', 3)->default('TRY');
            $table->boolean('is_closed')->default(false)->comment('If true, no reservations can be made for this day');
            $table->integer('min_stay_arrival')->default(1)->comment('Minimum stay if arrival is on this day');
            $table->enum('status', ['available', 'limited', 'sold_out'])->default('available');
            $table->string('notes')->nullable();
            $table->timestamps();
            
            // Each date can only have one rate for a specific rate plan
            $table->unique(['rate_plan_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
    }
};