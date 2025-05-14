<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consolidates and fixes all pricing-related migration conflicts
     * 
     * This migration addresses these issues:
     * 1. Making base_price nullable in rate_periods table (it should already be nullable)
     * 2. Ensuring consistent sales_type values across the system 
     * 3. Adding prices column to rate_periods table if it doesn't exist
     */
    public function up(): void
    {
        // 1. Fix rate_periods table structure
        Schema::table('rate_periods', function (Blueprint $table) {
            // Check if base_price is already nullable
            $databaseName = config('database.connections.mysql.database');
            $isBasepriceNullable = DB::select("
                SELECT IS_NULLABLE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = '$databaseName' 
                AND TABLE_NAME = 'rate_periods' 
                AND COLUMN_NAME = 'base_price'
            ");
            
            if ($isBasepriceNullable[0]->IS_NULLABLE === 'NO') {
                // If it's not nullable, make it nullable
                $table->decimal('base_price', 10, 2)->nullable()->change();
            }
            
            // Check if prices column exists
            $hasPricesColumn = Schema::hasColumn('rate_periods', 'prices');
            if (!$hasPricesColumn) {
                // Add prices column if it doesn't exist
                $table->json('prices')->nullable()->after('base_price');
            }
        });
        
        // 2. Fix sales_type field to use consistent values
        Schema::table('rate_periods', function (Blueprint $table) {
            // First check if we can modify the sales_type column
            $columnType = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = '".config('database.connections.mysql.database')."'
                AND TABLE_NAME = 'rate_periods'
                AND COLUMN_NAME = 'sales_type'
            ");
            
            if (isset($columnType[0])) {
                // If it's an ENUM, we need to recreate it
                if ($columnType[0]->DATA_TYPE === 'enum') {
                    // Drop and recreate with consistent values
                    $table->dropColumn('sales_type');
                    $table->string('sales_type')->default('direct')->after('quantity');
                }
            }
        });
        
        // 3. Fix sales_type values
        DB::statement("UPDATE rate_periods SET sales_type = 'direct' WHERE sales_type = 'direct_sale'");
        DB::statement("UPDATE rate_periods SET sales_type = 'inquire_only' WHERE sales_type = 'ask_availability'");
        
        // 4. Fix null base_price values
        DB::statement("UPDATE rate_periods SET base_price = 0 WHERE base_price IS NULL");
        
        // 5. Fix rate_exceptions table
        Schema::table('rate_exceptions', function (Blueprint $table) {
            // Check if base_price is already nullable
            $databaseName = config('database.connections.mysql.database');
            $isBasepriceNullable = DB::select("
                SELECT IS_NULLABLE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = '$databaseName' 
                AND TABLE_NAME = 'rate_exceptions' 
                AND COLUMN_NAME = 'base_price'
            ");
            
            if (isset($isBasepriceNullable[0]) && $isBasepriceNullable[0]->IS_NULLABLE === 'NO') {
                // If it's not nullable, make it nullable
                $table->decimal('base_price', 10, 2)->nullable()->change();
            }
            
            // Check if prices column exists
            $hasPricesColumn = Schema::hasColumn('rate_exceptions', 'prices');
            if (!$hasPricesColumn) {
                // Add prices column if it doesn't exist
                $table->json('prices')->nullable()->after('base_price');
            }
        });
        
        // 6. Fix null base_price values in exceptions
        DB::statement("UPDATE rate_exceptions SET base_price = 0 WHERE base_price IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration as these are fixes that should stay
    }
};
