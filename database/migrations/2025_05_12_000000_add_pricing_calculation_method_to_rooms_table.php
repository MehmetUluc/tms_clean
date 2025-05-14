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
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('pricing_calculation_method', ['per_person', 'per_room'])
                  ->default('per_room')
                  ->after('name')
                  ->comment('Fiyatlandırma hesaplama metodu: per_person (kişi başı) veya per_room (oda başı/unit)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('pricing_calculation_method');
        });
    }
};