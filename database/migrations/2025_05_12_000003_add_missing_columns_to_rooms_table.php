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
        Schema::table('rooms', function (Blueprint $table) {
            // Kapasite sütunlarını ekleyelim
            if (!Schema::hasColumn('rooms', 'capacity_adults')) {
                $table->integer('capacity_adults')->default(2)->after('slug');
            }
            
            if (!Schema::hasColumn('rooms', 'capacity_children')) {
                $table->integer('capacity_children')->default(0)->nullable(false)->after('capacity_adults');
            }
            
            // Boyut sütununu ekleyelim
            if (!Schema::hasColumn('rooms', 'size')) {
                $table->decimal('size', 8, 2)->nullable()->after('capacity_children');
            }
            
            // JSON sütunlarını ekleyelim
            if (!Schema::hasColumn('rooms', 'features_details')) {
                $table->json('features_details')->nullable()->after('size');
            }
            
            if (!Schema::hasColumn('rooms', 'child_policies')) {
                $table->json('child_policies')->nullable()->after('features_details');
            }
            
            // Görsel sütunlarını ekleyelim
            if (!Schema::hasColumn('rooms', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('child_policies');
            }
            
            if (!Schema::hasColumn('rooms', 'gallery')) {
                $table->json('gallery')->nullable()->after('cover_image');
            }
            
            // Durum sütunlarını ekleyelim
            if (!Schema::hasColumn('rooms', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('gallery');
            }
            
            if (!Schema::hasColumn('rooms', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_available');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'capacity_adults',
                'capacity_children',
                'size',
                'features_details',
                'child_policies',
                'cover_image',
                'gallery',
                'is_available',
                'is_featured'
            ]);
        });
    }
};