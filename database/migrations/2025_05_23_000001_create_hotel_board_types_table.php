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
        Schema::create('hotel_board_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('board_type_id')->constrained()->cascadeOnDelete();
            $table->enum('pricing_calculation_method', ['per_person', 'per_unit'])->default('per_person');
            $table->timestamps();
            
            // Composite unique constraint
            $table->unique(['hotel_id', 'board_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_board_types');
    }
};