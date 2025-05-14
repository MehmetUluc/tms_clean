<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('code')->nullable();
            $table->integer('base_capacity')->default(2);
            $table->integer('max_capacity')->default(2);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->integer('max_infants')->default(0);
            $table->string('size')->nullable();
            $table->string('size_unit')->default('m²');
            $table->json('features')->nullable();
            $table->json('gallery')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['slug', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};