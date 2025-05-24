<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('xml_mappings') && !Schema::hasTable('data_mappings')) {
            // First, create the new table with the updated structure
            Schema::create('data_mappings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
                $table->string('name');
                $table->enum('operation_type', ['import', 'export'])->default('import');
                $table->enum('format_type', ['xml', 'json'])->default('xml');
                $table->json('mapping_data');
                $table->string('mapping_entity')->default('room'); // room, rate, availability, etc.
                $table->text('description')->nullable();
                $table->json('validation_rules')->nullable();
                $table->text('template_content')->nullable(); // For storing templates
                $table->string('template_format')->nullable(); // Output format
                $table->string('version')->default('1.0'); // For template versioning
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_sync_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Tenant ID for multi-tenancy support
                $table->unsignedBigInteger('tenant_id')->nullable();
                
                // Composite unique constraint with tenant_id
                $table->unique(['channel_id', 'operation_type', 'mapping_entity', 'tenant_id', 'format_type'], 'data_mappings_unique_with_tenant');
            });

            // Copy data from old table to new table with mapping of changed columns
            // Skip data migration since xml_mappings table structure might be different
        } 
        // If we're creating data_mappings from scratch (no xml_mappings exists)
        else if (!Schema::hasTable('data_mappings')) {
            Schema::create('data_mappings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
                $table->string('name');
                $table->enum('operation_type', ['import', 'export'])->default('import');
                $table->enum('format_type', ['xml', 'json'])->default('xml');
                $table->json('mapping_data');
                $table->string('mapping_entity')->default('room'); // room, rate, availability, etc.
                $table->text('description')->nullable();
                $table->json('validation_rules')->nullable();
                $table->text('template_content')->nullable(); // For storing templates
                $table->string('template_format')->nullable(); // Output format
                $table->string('version')->default('1.0'); // For template versioning
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_sync_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Tenant ID for multi-tenancy support
                $table->unsignedBigInteger('tenant_id')->nullable();
                
                // Composite unique constraint with tenant_id
                $table->unique(['channel_id', 'operation_type', 'mapping_entity', 'tenant_id', 'format_type'], 'data_mappings_unique_with_tenant');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mappings');
    }
};