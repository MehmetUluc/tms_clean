<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xml_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('direction', ['import', 'export', 'both'])->default('import');
            $table->enum('entity_type', ['hotel', 'room', 'rate', 'availability', 'reservation', 'other'])->default('hotel');
            $table->text('description')->nullable();
            $table->string('xml_root_path')->nullable();
            $table->json('field_mappings')->nullable();
            $table->json('value_transformations')->nullable();
            $table->json('sample_data')->nullable();
            $table->text('template_content')->nullable();
            $table->string('template_format')->default('xml');
            $table->boolean('is_active')->default(true);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xml_mappings');
    }
};