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
        Schema::table('rate_exceptions', function (Blueprint $table) {
            $table->dropColumn('sales_type');
            $table->enum('sales_type', ['direct', 'ask_availability', 'inquire_only'])->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rate_exceptions', function (Blueprint $table) {
            $table->dropColumn('sales_type');
            $table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->nullable()->after('quantity');
        });
    }
};