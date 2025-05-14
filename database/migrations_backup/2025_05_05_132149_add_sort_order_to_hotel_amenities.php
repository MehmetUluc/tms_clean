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
        Schema::table('hotel_amenities', function (Blueprint $table) {
            // Sütun var mı diye kontrol et
            if (!Schema::hasColumn('hotel_amenities', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_amenities', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};