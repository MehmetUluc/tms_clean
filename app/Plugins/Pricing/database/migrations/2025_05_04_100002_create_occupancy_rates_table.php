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
        Schema::create('occupancy_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->date('date')->nullable()->comment('Null for default pricing');
            $table->integer('occupancy')->comment('Number of people (1, 2, 3, etc.)');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->boolean('is_default')->default(false)->comment('If true, this is the default price when no date-specific price exists');
            $table->timestamps();
            
            // Ensure uniqueness for occupancy rates
            $table->unique(['rate_plan_id', 'date', 'occupancy'], 'unique_occupancy_rate');
            
            // Ensure only one default price per occupancy level
            $table->unique(['rate_plan_id', 'occupancy', 'is_default'], 'unique_default_occupancy_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupancy_rates');
    }
};