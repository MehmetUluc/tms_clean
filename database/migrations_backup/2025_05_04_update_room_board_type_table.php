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
        Schema::table('room_board_type', function (Blueprint $table) {
            $table->decimal('price_modifier', 10, 2)->default(0)->after('board_type_id');
            $table->boolean('is_default')->default(false)->after('price_modifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_board_type', function (Blueprint $table) {
            $table->dropColumn(['price_modifier', 'is_default']);
        });
    }
};