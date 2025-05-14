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
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->json('prices')->nullable()->after('base_price');
            
            // Also fix the sales_type enum to match the one used in the code
            $table->dropColumn('sales_type');
            $table->enum('sales_type', ['direct', 'ask_availability', 'inquire_only'])->default('direct')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rate_periods', function (Blueprint $table) {
            $table->dropColumn('prices');
            $table->dropColumn('sales_type');
            $table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->default('direct_sale')->after('quantity');
        });
    }
};