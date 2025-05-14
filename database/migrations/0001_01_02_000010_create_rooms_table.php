<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('room_number')->nullable();
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'out_of_service'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('room_room_amenity', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_amenity_id')->constrained()->onDelete('cascade');
            $table->primary(['room_id', 'room_amenity_id']);
            $table->timestamps();
        });
        
        Schema::create('room_board_type', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('board_type_id')->constrained()->onDelete('cascade');
            $table->primary(['room_id', 'board_type_id']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_board_type');
        Schema::dropIfExists('room_room_amenity');
        Schema::dropIfExists('rooms');
    }
};