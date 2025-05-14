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
        Schema::table('xml_mappings', function (Blueprint $table) {
            // Add format_type column with 'xml' as default value
            $table->enum('format_type', ['xml', 'json'])->default('xml')->after('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xml_mappings', function (Blueprint $table) {
            $table->dropColumn('format_type');
        });
    }
};