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
        // Check if parent_id column exists, if not add it
        if (!Schema::hasColumn('regions', 'parent_id')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
            });
        }
        
        // Check if type column exists, if not add it
        if (!Schema::hasColumn('regions', 'type')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->enum('type', ['country', 'region', 'city', 'district'])->after('name')->default('region');
                $table->index('type');
            });
        }
        
        // Check and add other missing columns
        if (!Schema::hasColumn('regions', 'code')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->string('code', 10)->nullable()->after('slug')->comment('Ülke veya bölge kodu');
            });
        }
        
        if (!Schema::hasColumn('regions', 'latitude')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('description');
            });
        }
        
        if (!Schema::hasColumn('regions', 'longitude')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }
        
        if (!Schema::hasColumn('regions', 'timezone')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->string('timezone', 50)->nullable()->after('longitude');
            });
        }
        
        if (!Schema::hasColumn('regions', 'sort_order')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('is_active');
            });
        }
        
        if (!Schema::hasColumn('regions', 'is_featured')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('is_active');
                $table->index('is_featured');
            });
        }
        
        // Ensure indexes
        Schema::table('regions', function (Blueprint $table) {
            if (!Schema::hasIndex('regions', 'regions_sort_order_index')) {
                $table->index('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // It's risky to remove columns in down() method
        // But if needed, you can add drop column logic here
    }
};
