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
        // Regions tablosuna parent_id ve type alanları ekliyoruz
        Schema::table('regions', function (Blueprint $table) {
            // Üst bölge ilişkisi için foreign key
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
            
            // Bölge tipi: country, region, city, district
            $table->enum('type', ['country', 'region', 'city', 'district'])->after('name')->default('region');
            
            // Arama ve filtreleme için ekstra özellikler
            $table->string('code', 10)->nullable()->after('slug')->comment('Ülke veya bölge kodu');
            $table->decimal('latitude', 10, 7)->nullable()->after('description');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('timezone', 50)->nullable()->after('longitude');
            $table->integer('sort_order')->default(0)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('is_active');
            
            // İndeks eklemeleri
            $table->index('type');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            $table->dropColumn('type');
            $table->dropColumn('code');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('timezone');
            $table->dropColumn('sort_order');
            $table->dropColumn('is_featured');
            $table->dropIndex(['type']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['sort_order']);
        });
    }
};