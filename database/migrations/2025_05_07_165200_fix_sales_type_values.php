<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Update any existing 'direct' value to null first to prevent enum constraint errors
            DB::table('rate_periods')->update(['sales_type' => null]);
            DB::table('rate_exceptions')->update(['sales_type' => null]);
        } catch (\Exception $e) {
            // Ignore errors if tables don't exist yet
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this operation
    }
};