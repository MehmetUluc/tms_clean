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
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('total_price');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('original_price');
            $table->string('discount_code')->nullable()->after('discount_amount');
            $table->boolean('has_discount')->default(false)->after('discount_code');
            $table->foreignId('discount_id')->nullable()->after('has_discount')->constrained('discounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('original_price');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_code');
            $table->dropColumn('has_discount');
            $table->dropForeign(['discount_id']);
            $table->dropColumn('discount_id');
        });
    }
};