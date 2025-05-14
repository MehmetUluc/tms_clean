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
        Schema::table('hotels', function (Blueprint $table) {
            // non_refundable_discount sütununu nullable yap
            if (Schema::hasColumn('hotels', 'non_refundable_discount')) {
                $table->decimal('non_refundable_discount', 5, 2)->default(0)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // non_refundable_discount sütununu tekrar not null yap
            if (Schema::hasColumn('hotels', 'non_refundable_discount')) {
                $table->decimal('non_refundable_discount', 5, 2)->default(0)->nullable(false)->change();
            }
        });
    }
};
