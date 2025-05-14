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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('room_number')->nullable();
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->integer('max_occupancy')->default(2);
            $table->integer('size')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_clean')->default(true);
            $table->string('status')->default('available');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('room_board_type', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('board_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['room_id', 'board_type_id']);
            $table->timestamps();
        });

        Schema::create('room_room_amenity', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_amenity_id')->constrained()->cascadeOnDelete();
            $table->primary(['room_id', 'room_amenity_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_room_amenity');
        Schema::dropIfExists('room_board_type');
        Schema::dropIfExists('rooms');
    }
};