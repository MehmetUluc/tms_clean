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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('available')->default(0)->comment('Number of rooms available');
            $table->integer('total')->default(0)->comment('Total capacity');
            $table->boolean('is_closed')->default(false)->comment('Closed for sales on this date');
            $table->boolean('stop_sell')->default(false)->comment('Temporary stop sell flag');
            $table->string('notes')->nullable();
            $table->timestamps();
            
            // Each date can only have one inventory entry for a specific rate plan and room
            $table->unique(['rate_plan_id', 'room_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};