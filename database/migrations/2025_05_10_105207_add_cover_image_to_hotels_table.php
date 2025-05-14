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
            // cover_image sütunu kontrol et ve ekle
            if (!Schema::hasColumn('hotels', 'cover_image')) {
                $table->string('cover_image')->nullable();
            }

            // featured_image sütunu ile çakışma olabilir mi diye kontrol et
            $hasFeaturedImage = Schema::hasColumn('hotels', 'featured_image');

            // cover_image ve featured_image farklı sütunlar ise ve her ikisi de varsa
            // veri çakışması olmaması için sadece yeni eklenen sütun boş olsun
            if ($hasFeaturedImage && !Schema::hasColumn('hotels', 'cover_image')) {
                // featured_image'dan değer kopyalayabiliriz, ama şimdilik boş olsun
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });
    }
};
