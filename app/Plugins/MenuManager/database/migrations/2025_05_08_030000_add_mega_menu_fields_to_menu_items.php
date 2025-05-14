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
        Schema::table('menu_items', function (Blueprint $table) {
            // Mega menü grid yapılandırması
            $table->json('mega_menu_layout')->nullable()->after('template');
            
            // Grid içeriği
            $table->json('mega_menu_content')->nullable()->after('mega_menu_layout');
            
            // Mega menü şablonu (önceden tanımlanmış mega menü layoutları için)
            $table->string('mega_menu_template')->nullable()->after('mega_menu_content');
            
            // Arka plan rengi, görsel veya stil
            $table->string('mega_menu_background')->nullable()->after('mega_menu_template');
            
            // Ek sütun alanı (kaç sütun olacağını belirler)
            $table->integer('mega_menu_columns')->nullable()->default(4)->after('mega_menu_background');
            
            // Maksimum genişlik
            $table->string('mega_menu_width')->nullable()->after('mega_menu_columns');
            
            // Ek stil özellikleri
            $table->json('mega_menu_styles')->nullable()->after('mega_menu_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn([
                'mega_menu_layout',
                'mega_menu_content',
                'mega_menu_template',
                'mega_menu_background',
                'mega_menu_columns',
                'mega_menu_width',
                'mega_menu_styles'
            ]);
        });
    }
};