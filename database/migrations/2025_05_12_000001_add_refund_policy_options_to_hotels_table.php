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
            // Bu alanlar zaten var mı kontrol et, yoksa ekle
            if (!Schema::hasColumn('hotels', 'refund_policy')) {
                $table->enum('refund_policy', ['both', 'refundable', 'non_refundable'])->default('both')
                    ->comment('Otel için izin verilen iade politikası tipi');
            }
            
            if (!Schema::hasColumn('hotels', 'allow_refundable')) {
                $table->boolean('allow_refundable')->default(true)
                    ->comment('Otelin iade edilebilir oda tipi sunup sunmadığı');
            }
            
            if (!Schema::hasColumn('hotels', 'allow_non_refundable')) {
                $table->boolean('allow_non_refundable')->default(true)
                    ->comment('Otelin iade edilemez oda tipi sunup sunmadığı');
            }
            
            if (!Schema::hasColumn('hotels', 'non_refundable_discount')) {
                $table->decimal('non_refundable_discount', 5, 2)->default(0)
                    ->comment('İade edilemez odalar için indirim yüzdesi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Alanları kaldırma
            $table->dropColumn([
                'refund_policy',
                'allow_refundable', 
                'allow_non_refundable', 
                'non_refundable_discount'
            ]);
        });
    }
};