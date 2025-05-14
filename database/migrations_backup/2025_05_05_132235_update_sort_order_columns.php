<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Önce null değerleri güncelle
        DB::table('room_amenities')
            ->whereNull('sort_order')
            ->update(['sort_order' => 0]);
            
        DB::table('hotel_amenities')
            ->whereNull('sort_order')
            ->update(['sort_order' => 0]);

        // Sonra sütun kısıtlamalarını güncelle
        Schema::table('room_amenities', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->nullable(false)->change();
        });
        
        Schema::table('hotel_amenities', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_amenities', function (Blueprint $table) {
            $table->integer('sort_order')->nullable()->change();
        });
        
        Schema::table('hotel_amenities', function (Blueprint $table) {
            $table->integer('sort_order')->nullable()->change();
        });
    }
};