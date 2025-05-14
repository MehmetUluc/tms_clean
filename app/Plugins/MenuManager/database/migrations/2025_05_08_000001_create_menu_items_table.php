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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('link_type')->default('url'); // url, route, model, custom
            $table->string('target')->default('_self'); // _self, _blank, etc.
            $table->string('icon')->nullable();
            $table->string('class')->nullable();
            $table->json('attributes')->nullable();
            $table->json('data')->nullable(); // For additional custom data
            $table->string('model_type')->nullable(); // For polymorphic relations
            $table->unsignedBigInteger('model_id')->nullable(); // For polymorphic relations
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_mega_menu')->default(false);
            $table->string('template')->nullable(); // For template-based items
            $table->timestamps();
            
            // Add tenant support if needed
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            }
            
            // Add index for polymorphic relations
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};