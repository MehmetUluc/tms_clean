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
        if (!Schema::hasTable('xml_mappings')) {
            Schema::create('xml_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['import', 'export'])->default('import');
            $table->json('mapping_data');
            $table->string('mapping_entity')->default('room'); // room, rate, availability, etc.
            $table->text('description')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Tenant ID ekle ama foreign key constraint olmadan
            $table->unsignedBigInteger('tenant_id')->nullable();
            
            // Composite unique constraint with tenant_id
            $table->unique(['channel_id', 'type', 'mapping_entity', 'tenant_id'], 'xml_mappings_unique_with_tenant');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml_mappings');
    }
};