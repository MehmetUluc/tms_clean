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
        Schema::table('daily_rates', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_rates', 'sales_type')) {
                $table->string('sales_type', 20)->default('direct')->after('is_refundable')->comment('direct or ask_sell');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            if (Schema::hasColumn('daily_rates', 'sales_type')) {
                $table->dropColumn('sales_type');
            }
        });
    }
};