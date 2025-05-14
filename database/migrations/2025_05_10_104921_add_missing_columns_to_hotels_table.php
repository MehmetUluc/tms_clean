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
            // Eksik olan sütunları ekle
            if (!Schema::hasColumn('hotels', 'city')) {
                $table->string('city')->nullable();
            }

            if (!Schema::hasColumn('hotels', 'state')) {
                $table->string('state')->nullable();
            }

            if (!Schema::hasColumn('hotels', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }

            if (!Schema::hasColumn('hotels', 'country')) {
                $table->string('country')->nullable();
            }

            if (!Schema::hasColumn('hotels', 'currency')) {
                $table->string('currency')->default('TRY');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Eklenen sütunları kaldır
            $columns = ['city', 'state', 'zip_code', 'country', 'currency'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('hotels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
