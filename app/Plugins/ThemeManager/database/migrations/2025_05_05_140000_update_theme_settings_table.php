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
        if (Schema::hasTable('theme_settings') && !Schema::hasColumn('theme_settings', 'deleted_at')) {
            Schema::table('theme_settings', function (Blueprint $table) {
                $table->softDeletes()->after('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};