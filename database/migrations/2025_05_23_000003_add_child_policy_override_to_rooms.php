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
            // Çocuk politikası override flag
            $table->boolean('override_child_policy')->default(false)->after('pricing_calculation_method');
            
            // Override durumunda kullanılacak özel çocuk politikaları
            $table->json('custom_child_policies')->nullable()->after('override_child_policy');
            
            // Override durumunda kullanılacak özel ayarlar
            $table->integer('custom_max_children')->nullable()->after('custom_child_policies');
            $table->integer('custom_child_age_limit')->nullable()->after('custom_max_children');
            $table->text('child_policy_note')->nullable()->after('custom_child_age_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'override_child_policy',
                'custom_child_policies',
                'custom_max_children',
                'custom_child_age_limit',
                'child_policy_note'
            ]);
        });
    }
};