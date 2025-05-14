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
        // Tabloyu silmek riskli olabilir, önce kontrol edelim
        if (Schema::hasTable('theme_settings')) {
            // Eğer tablo varsa, yapısını kontrol edelim
            $hasCorrectStructure = Schema::hasColumns('theme_settings', [
                'key', 'value', 'type', 'group', 'is_public', 'is_active', 'tenant_id'
            ]);
            
            if (!$hasCorrectStructure) {
                Schema::dropIfExists('theme_settings');
            } else {
                // Tablo doğru yapıya sahipse, yalnızca eksik olan sütunları ekleyelim
                return;
            }
        }
        
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, color, image
            $table->string('group')->default('general'); // colors, layout, typography, seo, social, etc.
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('tenant_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};