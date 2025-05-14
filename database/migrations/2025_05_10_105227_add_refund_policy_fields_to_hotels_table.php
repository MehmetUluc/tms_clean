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
            // allow_refundable sütununu ekle
            if (!Schema::hasColumn('hotels', 'allow_refundable')) {
                $table->boolean('allow_refundable')->default(true);
            }

            // allow_non_refundable sütununu ekle
            if (!Schema::hasColumn('hotels', 'allow_non_refundable')) {
                $table->boolean('allow_non_refundable')->default(true);
            }

            // non_refundable_discount sütununu ekle
            if (!Schema::hasColumn('hotels', 'non_refundable_discount')) {
                $table->decimal('non_refundable_discount', 5, 2)->default(0);
            }

            // refund_policy sütununu ekle
            if (!Schema::hasColumn('hotels', 'refund_policy')) {
                $table->text('refund_policy')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $columns = ['allow_refundable', 'allow_non_refundable', 'non_refundable_discount', 'refund_policy'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('hotels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
