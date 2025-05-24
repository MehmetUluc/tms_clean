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
            // Varsayılan çocuk yaş aralıkları ve politikaları
            $table->json('child_policies')->nullable()->after('check_in_out');
            
            // Örnek yapı:
            // [
            //     {
            //         "age_from": 0,
            //         "age_to": 6,
            //         "price_type": "free", // free, percentage, fixed
            //         "price_value": 0,
            //         "max_children": 2,
            //         "description": "0-6 yaş ücretsiz"
            //     },
            //     {
            //         "age_from": 7,
            //         "age_to": 12,
            //         "price_type": "percentage",
            //         "price_value": 50,
            //         "max_children": 2,
            //         "description": "7-12 yaş %50 indirimli"
            //     }
            // ]
            
            // Çocuk politikası için genel ayarlar
            $table->integer('max_children_per_room')->default(2)->after('child_policies');
            $table->integer('child_age_limit')->default(12)->after('max_children_per_room');
            $table->boolean('children_stay_free')->default(false)->after('child_age_limit');
            $table->text('child_policy_description')->nullable()->after('children_stay_free');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'child_policies',
                'max_children_per_room',
                'child_age_limit',
                'children_stay_free',
                'child_policy_description'
            ]);
        });
    }
};