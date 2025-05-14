<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('hotel_hotel_amenity', function (Blueprint $table) {
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_amenity_id')->constrained()->onDelete('cascade');
            $table->primary(['hotel_id', 'hotel_amenity_id']);
            $table->timestamps();
        });
        
        Schema::create('hotel_hotel_tag', function (Blueprint $table) {
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_tag_id')->constrained()->onDelete('cascade');
            $table->primary(['hotel_id', 'hotel_tag_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_hotel_tag');
        Schema::dropIfExists('hotel_hotel_amenity');
        Schema::dropIfExists('hotel_amenities');
    }
};