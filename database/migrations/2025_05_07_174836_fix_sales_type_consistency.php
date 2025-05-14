<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix sales_type consistency between the database schema, models, and UI
     * 
     * The UI uses: 'direct' and 'ask_sell'
     * Some migrations use: 'direct_sale', 'ask_availability', 'inquire_only'
     * Others use: 'direct', 'ask_availability', 'inquire_only'
     * 
     * This migration standardizes everything to use 'direct' and 'ask_sell'
     */
    public function up(): void
    {
        // First convert all sales_type fields to string type to avoid ENUM constraints
        $this->convertToString('rate_periods', 'sales_type');
        $this->convertToString('rate_exceptions', 'sales_type');
        
        // Map old values to new consistent values
        DB::statement("UPDATE rate_periods SET sales_type = 'direct' WHERE sales_type IN ('direct_sale')");
        DB::statement("UPDATE rate_periods SET sales_type = 'ask_sell' WHERE sales_type IN ('ask_availability', 'inquire_only')");
        
        DB::statement("UPDATE rate_exceptions SET sales_type = 'direct' WHERE sales_type IN ('direct_sale')");
        DB::statement("UPDATE rate_exceptions SET sales_type = 'ask_sell' WHERE sales_type IN ('ask_availability', 'inquire_only')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need for a down migration as we're standardizing values
    }
    
    /**
     * Helper method to convert a column type to string
     */
    private function convertToString(string $table, string $column): void
    {
        if (Schema::hasColumn($table, $column)) {
            $columnType = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = '".config('database.connections.mysql.database')."'
                AND TABLE_NAME = '$table'
                AND COLUMN_NAME = '$column'
            ");
            
            if (isset($columnType[0]) && $columnType[0]->DATA_TYPE === 'enum') {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->string($column)->change();
                });
            }
        }
    }
};
