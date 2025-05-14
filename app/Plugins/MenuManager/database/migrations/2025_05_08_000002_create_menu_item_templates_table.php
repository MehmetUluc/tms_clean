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
        Schema::create('menu_item_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('template')->nullable(); // HTML/Blade template
            $table->json('settings')->nullable(); // Configuration settings
            $table->json('fields')->nullable(); // Form fields definition
            $table->string('thumbnail')->nullable(); // Preview image
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add tenant support if needed
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_templates');
    }
};